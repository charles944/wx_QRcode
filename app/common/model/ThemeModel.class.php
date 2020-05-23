<?php
namespace common\model;
use think\Model;
class ThemeModel extends Model
{
	protected $name = '';
    protected $tokenFile = '/version.json';
    protected $dir = QN_THEME_PATH;
	
    public function setTheme($name)
    {
        if (D('Config')->where(array('name' => '_THEME_NOW_THEME'))->count()) {
            $res = D('Config')->where(array('name' => '_THEME_NOW_THEME'))->setField('value', $name);
        } else {
            $config['name'] = '_THEME_NOW_THEME';
            $config['type'] = 0;
            $config['title'] = '';
            $config['group'] = 0;
            $config['extra'] = '';
            $config['remark'] = '';
            $config['create_time'] = time();
            $config['update_time'] = time();
            $config['status'] = 1;
            $config['value'] = $name;
            $config['sort'] = 0;
            $res = D('Config')->add($config);
        }
        if ($res) {
            S('conf_THEME_NOW_THEME', $name);
            cookie('TO_LOOK_THEME', $name, array('prefix' => 'WZBV2'));
            clean_cache(RUNTIME_PATH.'cache/');//清除模板缓存
            return true;
        } else {
            $this->error = '写入数据库失败。';
            return false;
        }
    }
    public function getThemeList()
    {
        $tpls = null;
        $dir = $this->dir;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    //去掉"“.”、“..”以及带“.xxx”后缀的文件
                    if ($file != "." && $file != ".." && !strpos($file, ".")) {
                        if (is_file(QN_THEME_PATH . $file . '/info.php')) {
                            $tpl = require_once(QN_THEME_PATH . $file . '/info.php');
                            $tpl['path'] = QN_THEME_PATH . $file;
                            $tpl['file_name'] = $file;
							$token_arr = $this->getToken($file);
							if (!empty($token_arr)) {
								$tpl['token'] = $token_arr['token'];
								$tpl['oid'] = $token_arr['oid'];
								$tpl['md5'] = $token_arr['md5'];
								unset($token_arr);
							}
                            $tpls[$tpl['file_name']] = $tpl;
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $tpls;
    }
    /**
     * 临时查看主题（主题预览用）
     * @param $theme
     * @return bool
     */
    public function lookTheme($theme,$time=180)
    {
        cookie('TO_LOOK_THEME', $theme, array('prefix' => 'WZBV2', 'expire' => $time));//重设cookie
        return true;
    }
    /**获取主题
     * @return mixed
     */
    public function getTheme($name)
    {
        if (is_file(QN_THEME_PATH . $name . '/info.php')) {
            $tpl = require_once(QN_THEME_PATH . $name . '/info.php');
            $tpl['path'] = QN_THEME_PATH . $name;
            $tpl['file_name'] = $name;
			$token_arr = $this->getToken($name);
			if (!empty($token_arr)) {
				$tpl['token'] = $token_arr['token'];
				$tpl['oid'] = $token_arr['oid'];
				$tpl['md5'] = $token_arr['md5'];
				unset($token_arr);
			}
        }
        return $tpl;
    }
	
	/**获取模块的token
     * @param $name 模块名
     * @return string
     */
    public function getToken($name)
    {
        $this->name = $name;
		$token = '';
        if (file_exists($this->getRelativePath($this->tokenFile))) {
            $token_tmp = file_get_contents($this->getRelativePath($this->tokenFile));
			if(!empty($token_tmp)){
				$token = json_decode($token_tmp, true);
			}
        }
        return $token;
    }
	
	
    public function setToken($name, $token)
    {
        $this->name = $name;
        @chmod($this->getRelativePath($this->tokenFile), 0777);
        $result = file_put_contents($this->getRelativePath($this->tokenFile), $token);
        @chmod($this->getRelativePath($this->tokenFile), 0777);
        return $result;
    }
    private function getRelativePath($file)
    {
        return QN_THEME_PATH . $this->name . $file;
    }
}
?>