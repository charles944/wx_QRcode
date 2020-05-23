<?php

namespace admin\Model;
use Think\Model;
require_once('./data/version.php');

class VersionModel extends Model
{
	protected $versionPath = './data/version.php';
    /**检查是否有新的更新
     * @return bool
     */
    public function checkUpdate()
    {
        $result = S('admin_update');
        if ($result === false) {

            if ($this->getNextVersion() == '') {
                $result = 0;
            } else {
                $result = 1;
            }
            S('admin_update', $result, 600);
        }
        return $result;

    }
    public function cleanCheckUpdateCache(){
        S('admin_update',null);
    }

    /**获取当前的版本号
     * @return string
     */
    public function getCurrentVersion()
    {
        $version = QN_VERSION;
        $this->refreshVersions();
        $version = $this->where(array('name' => $version))->find();
		if(empty($version)){
			$version['name'] = QN_VERSION;
		}
        return $version;
    }

    /**设置当前版本号
     * @param $name 版本号
     * @return int|void
     */
    public function setCurrentVersion($name)
    {
    	//字符串处理 
    	$string_start = "<?php\n"; 
    	$string_process = "define('QN_VERSION', '".$name."');\n"; 
		$string_process1 = "define('QN_VERSION_NAME', '靑年PHP');\n";
		$string_process2 = "define('QN_VERSION_COPYRIGHT', 'liang');";
    	$string = $string_start.$string_process.$string_process1.$string_process2; 
    	//var_dump($string);
    	//开始写入文件
        return file_put_contents($this->versionPath, $string);
    }

    /**
     * 重新从服务器获取所有的版本信息并更新本地
     */
    public function refreshVersions()
    {
        $content = file_get_contents(C('__CLOUD__') . cloudU('appstore/update/versions',array('aid'=>3)));
        $versions = json_decode($content, true);
		if(!empty($versions)){
			foreach ($versions as $key => $v) {
				$version = $this->where(array('name' => $v['name']))->find();
				if (!$version) {
					$this->add($v);
				} else {
					unset($v['update_time']);
					$version = $v + $version;
					$this->save($version);
				}
			}
			$this->where(array('name' => array('not in', getSubByKey($versions, 'name'))))->delete();
		} else {
			
		}
    }

    public function getNextVersion()
    {
        $versions = $this->order('number asc')->select();
        $currentVersion = $this->getCurrentVersion();
        foreach ($versions as $v) {
            if (version_compare($v['name'], $currentVersion['name']) == 1) {
                return $v;
            }
        }
        return '';
    }
}
