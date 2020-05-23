<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use think\Db;
use ot\Database;

class UpdateController extends AdminController
{
    protected $pack_sql_dir = 'patches/sqls';
    protected $mPackPath = 'patches/info';


    
    private function read_file($filename)
    {
        $db = '';
        if (!$file = fopen($filename, "r")) {
            $db = array();
        } else {
            //整读文件
            while (!feof($file)) {
                $db .= fgets($file);
            }
            fclose($file);
        }
        return $db;
    }

    public function all()
    {
        $db = $this->read_file($this->pack_db_path);
        $db = json_decode($db);
        $db = $this->toArray($db);
        foreach ($db['packs'] as &$pack) {
            $file = $this->pack_sql_dir . '/' . $pack['title'] . '.sql';
            $pack['mtime'] = date('Y-m-d H:i:s', $pack['mtime']);
            $pack['size'] = filesize($file) . ' bytes';
        }
        unset($pack);
        $this->assign('db', $db);
        $title = '快捷操作'; //渲染模板
        $this->assign('meta_title', $title);
        $this->display();
    }

    public function quick()
    {

        $files = $this->getFile($this->mPackPath);
        $list = array();
        foreach ($files as $f) {
            $info = $this->toArray(json_decode($this->read_file($this->mPackPath . '/' . $f)));
            if(!$info){
                continue;
            }
            $file = $this->mPackPath . '/' . $info['ctime'] . '.sql';
            $info['file'] = $info['ctime'] . '.sql';
            $info['id'] = $info['ctime'];
            $info['ctime'] = friendlyDate($info['ctime']);
            $size=filesize($this->pack_sql_dir . '/' . $info['id'].'.sql' );
            if($size/1024>1){
                $info['size'] = number_format(($size*1.0/1024/1024),2) . 'MB';
            }else{
                $info['size'] = number_format(($size*1.0/1024),2) . 'KB';
            }

            if ($info['mtime'] != 0)
                $info['mtime'] = friendlyDate($info['mtime']);
            $list[] = $info;

        }
        /*        $listBuilder = new AdminListBuilder();*/
        /*      $listBuilder->keyText('title', '标题')->keyText('des', '用途介绍')->keyText('author', '作者')->keyText('file', 'sql文件名')->keyText('size', 'sql大小')->keyText('ctime', '创建时间')->keyText('mtime', '修改时间')
                  ->keyDoActionEdit('update/addpack?id=###', '编辑');*/
        /*  $listBuilder->data($list);
          $listBuilder->display();
          dump($list);
          exit;*/

        /*
                dump($fiels);
                exit;

                $db = $this->read_file($this->pack_db_path);
                $db = json_decode($db);
                $db = $this->toArray($db);
                foreach ($db['packs'] as &$pack) {
                    $file = $this->pack_sql_dir . '/' . $pack['title'] . '.sql';
                    $pack['mtime'] = date('Y-m-d H:i:s', $pack['mtime']);
                    $pack['size'] = filesize($file) . ' bytes';
                }
                unset($pack);*/
        $this->assign('list', $list);
        $title = '快捷操作'; //渲染模板
        $this->assign('meta_title', $title);
        $this->display();
    }

    public function view($title = '')
    {
        if (IS_POST) {
            if ($title == '') {
                exit;
            }
            exit($this->read_file($this->pack_sql_dir . "/{$title}.sql"));
        }
    }

    public function del_pack()
    {
        $title = trim($_GET['id']);
        if ($_GET['id']) {
            $myfile = $this->pack_sql_dir . "/{$title}.sql";
            $jsonFile = $this->mPackPath . '/' . $title . '.json';
            $result = unlink($myfile) || unlink($jsonFile);

            if ($result) {
                $this->success('删除文件成功。');
                exit;
            } else {
                $this->error('删除文件失败。');
            }
        } else {
            $this->error('未选择补丁。');
        }


    }


    /**
     * 新增补丁
     */
    public function addpack($title_old = '', $title = '', $sql = '', $des = '', $author = '')
    {

        if (IS_POST) {
            $aId = I('post.id', 0, 'intval');
            if ($aId != 0) {
                //编辑逻辑，取到原有数据
                $info = $this->getJsonFile($aId);
            }
            //dump($this->mPackPath . '/' . $aId . '.json');exit;
            $aTitle = I('post.title');
            $aDes = I('post.des');
            $aAuthor = I('post.author');
            $aSql = I('post.sql');
            if ($aSql == '') {
                $this->error('必须填写Sql语句。');
            }
            $info['title'] = $aTitle;
            $info['des'] = $aDes;
            $info['author'] = $aAuthor;
            if ($aId == 0) {
                //新增逻辑
                $time = time();
                if ($title == '')
                    $title = $time;
                $info['title'] = $title;
                $fh = $this->writeSql($sql, $time);

                $info['ctime'] = time();
                $info['mtime'] = '0';
                $fh = $this->writeJsonFile($time, $info);
                $this->success("新增补丁成功。");

            } else {
                $info['mtime'] = time();
                //打开文件
                $this->writeJsonFile($aId, $info);
                fclose($fh);
                $this->writeSql($aSql, $aId);
                $this->success("编辑补丁成功。");
                exit;
            }
        } else {
            $aId = I('get.id', 0, 'intval');
            if ($aId != 0) {
                $info = $this->getJsonFile($aId);
                $info['sql'] = $this->read_file($this->pack_sql_dir . '/' . $aId . '.sql');
            }

            $formBuilder = new AdminConfigBuilder();
            $formBuilder->title('新增补丁')
            ->keyText('title', '补丁名称')->keyTextArea('des', '用途介绍')->keyTextArea('sql', 'sql语句')->keyText('author', '作者')
                ->buttonSubmit();
            if ($aId != 0) {
                $info['id'] = $aId;
                $formBuilder->keyHidden('id');
            }
            $formBuilder->data($info);
            $formBuilder->display();
        }
    }


    public function use_pack($id = '')
    {
        if (IS_GET && $id != '') {

            //  $db = new Database(array('', $this->pack_sql_dir . "/{$title}.sql"), array(), 'import');
            $error = D('')->executeSqlFile($this->pack_sql_dir . "/{$id}.sql");
            if ($error['error_code'] != '') {
                $this->error($error['error_code']);
                exit;
            } else {
                clean_all_cache();
                $this->success('使用补丁成功。');
            }
        } else {
            $this->error('请选择补丁。');
        }
    }

    /*OneWX二次开发end*/
    /**
     * 获取文件列表
     */
    private function getFile($folder)
    {
        //打开目录
        $fp = opendir($folder);
        //阅读目录
        while (false != $file = readdir($fp)) {
            //列出所有文件并去掉'.'和'..'
            if ($file != '.' && $file != '..') {
                //$file="$folder/$file";
                $file = "$file";

                //赋值给数组
                $arr_file[] = $file;

            }
        }
        //输出结果
        if (is_array($arr_file)) {
            while (list($key, $value) = each($arr_file)) {
                $files[] = $value;
            }
        }
        //关闭目录
        closedir($fp);
        return $files;


    }

    /**
     * @param $sql
     * @param $time
     * @return resource
     * @auth 
     */
    private function writeSql($sql, $time)
    {
//打开文件
        if (!$fh = fopen($this->pack_sql_dir . '/' . $time . '.sql', 'w')) {
            $this->error("不能创建文件 " . $this->pack_sql_dir . '/' . $time);
            exit;
        }
        // 写入内容
        if (fwrite($fh, $sql) === FALSE) {
            $this->error("不能写入到文件" . $this->pack_sql_dir . '/' . $time);
            exit;
        }
        return $fh;
    }

    /**
     * @param $aId
     * @return mixed
     * @auth 
     */
    private function getJsonFile($aId)
    {
        return $this->toArray(json_decode($this->read_file($this->mPackPath . '/' . $aId . '.json')));
    }

    /**json转换为数组
     * @param $stdclassobject
     * @return mixed
     */
    private function toArray($stdclassobject)
    {

        $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;

        foreach ($_array as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? $this->toArray($value) : $value;
            $array[$key] = $value;
        }

        return $array;

    }

    /**
     * @param $time
     * @param $info
     * @return resource
     * @auth 
     */
    private function writeJsonFile($time, $info)
    {
//打开文件
        if (!$fh = fopen($this->mPackPath . '/' . $time . '.json', 'w')) {
            $this->error("不能打开文件 $this->mPackPath" . '/' . $time . '.json');
            exit;
        }
        // 写入内容
        if (fwrite($fh, json_encode($info)) === FALSE) {
            $this->error("不能写入到 $this->mPackPath" . '/' . $time . '.json');
            exit;
        }
        fclose($fh);
        return $fh;
    }

}