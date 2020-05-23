<?php
namespace admin\controller;

class ThemeController extends AdminController{

    /**
     * 模版列表页
     */
    public function index()
    {
        $aCleanCookie=I('get.cleanCookie',0,'intval');
        if($aCleanCookie){
            cookie('TO_LOOK_THEME', null, array('prefix' => 'WZBV2'));
        }
        // 根据应用目录取全部APP信息
        $dir = QN_THEME_PATH;
        $tplList = null;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    //去掉"“.”、“..”以及带“.xxx”后缀的文件
                    if ($file != "." && $file != ".." && !strpos($file, ".")) {
                        if (is_file(QN_THEME_PATH  . $file . '/info.php')) {
                            $tpl = require_once(QN_THEME_PATH  . $file . '/info.php');
                            $tpl['path']=ltrim(QN_THEME_PATH,'.'). $file;
                            $tpl['file_name'] = $file;
                            $tplList[] = $tpl;
                        }
                    }
                }
                closedir($dh);
            }
        }
        $now_theme = modC('NOW_THEME', 'default', 'Theme');
        $this->assign('now_theme', $now_theme);
        $this->assign('tplList', $tplList);
        $this->display();
    }

    public function packageDownload()
    {
        $aTheme = I('theme', '', 'text');
        if ($aTheme != '') {
            $themePath = QN_THEME_PATH;
            require_once(APP_PATH . "framework/library/ot/PclZip.class.php");
            $archive = new \PclZip($themePath . $aTheme . '.zip');
            $data = $archive->create($themePath . $aTheme, PCLZIP_OPT_REMOVE_PATH, $themePath);
            if ($data) {
                $this->_download($themePath . $aTheme . '.zip',$aTheme . '.zip');
                return;
            } else {
                $this->error('打包失败！');
                return;
            }
        }
        $this->error('参数错误！');
    }

    private function _download($get_url,$file_name)
    {
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . 'QingNianPHP V2_Theme_' . $file_name);
        header('Content-Length: ' . filesize($get_url));
        error_reporting(0);
        readfile($get_url);
        flush();
        ob_flush();
        $this->_delFile($get_url);
        exit;
    }

    public function delete()
    {
        $aTheme = I('theme', '', 'text');
        if ($aTheme != '') {
            $themePath = QN_THEME_PATH. $aTheme;
            $res = $this->_deldir($themePath);
            if ($res) {
                $this->success('删除成功！', U('admin/theme/index'));
                return;
            } else {
                $this->error('删除失败！', U('admin/theme/index'));
                return;
            }
        }
        $this->error('参数错误！', U('admin/theme/index'));
    }

    public function setTheme()
    {
        $aTheme = I('get.theme', 'default', 'text');
        $themeModel = D('Common/Theme');
        if ($themeModel->setTheme($aTheme)) {
            $result['info'] = '设置主题成功！';
            $result['status'] = 1;
        } else {
            $result['info'] = '设置主题失败！';
            $result['status'] = 0;
        }
        $this->ajaxReturn($result);
    }

    public function lookTheme()
    {
        $aTheme = I('theme', '', 'text');
        cookie('TO_LOOK_THEME', $aTheme, array('prefix' => 'WZBV2', 'expire' => 180));//重设cookie
        S('conf_THEME_NOW_THEME',$aTheme,180);//重设modC的session
        clean_cache('./~runtime/cache/');//清除模板缓存
        redirect(U('home/index/index'));
    }

    public function add()
    {
        if(IS_POST){
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => QN_THEME_PATH,
                'savePath' => '',
                'saveName' => '',
                'exts' => array('zip', 'rar'),
                'autoSub' => true,
                'subName' => '',
                'replace' => true,
            );
            $upload = new \think\Upload($config); // 实例化上传类
            $info = $upload->upload($_FILES);
            if (!$info) { // 上传错误提示错误信息
                $this->error($upload->getError());
            } else { // 上传成功
                $this->_unCompression($info['pkg']['savename']);
                $this->success("安装成功！", U('admin/theme/index'));
            }
        }else{
            $this->display();
        }
    }

    private function _unCompression($filename)
    {
        $ThemePkg =QN_THEME_PATH;
        require_once(APP_PATH . "framework/library/ot/PclZip.class.php");
        $pcl = new \PclZip($ThemePkg . $filename);
        if ($pcl->extract($ThemePkg)) {
            $result = $this->_delFile($ThemePkg . $filename);
            if ($result) {
                return true;
            }
        }
        return false;
    }

    private function _delFile($path)
    {
        $result = @unlink($path);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    private function _deldir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->_deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
} 