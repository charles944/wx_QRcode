<?php
namespace home\controller;
use think\Controller;

/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends Controller
{
    /* 文件上传 */
    public function upload()
    {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        /* 调用文件上传组件上传文件 */
        $File = D('Admin/File');
        $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
        $info = $File->upload(
            $_FILES,
            C('DOWNLOAD_UPLOAD'),
            C('DOWNLOAD_UPLOAD_DRIVER'),
            C("UPLOAD_{$file_driver}_CONFIG")
        );

        /* 记录附件信息 */
        if ($info) {
            $return['data'] = think_encrypt(json_encode($info['download']));
        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    public function downloadFile($id=null){
        if (empty($id) || !is_numeric($id)) {
            $this->error('参数错误！');
        }
        $info = D('file')->find($id);
        if(empty($info)){
            $this->error( "不存在的文档ID：{$id}");
            return false;
        }
        $File = D('Admin/File');
        $root ='.';
        $call = '';
        if(false === $File->download($root, $info['id'], $call, $info['id'])){
            $this->error( $File->getError());
        }
    }


    /**用于表单自动上传图片的通用方法
     */
    public function uploadFile()
    {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        /* 调用文件上传组件上传文件 */
        $File = D('Admin/File');
        $driver = modC('DOWNLOAD_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        $info = $File->upload(
            $_FILES,
            C('DOWNLOAD_UPLOAD'),
            $driver,
            $uploadConfig
        );

        /* 记录附件信息 */
        if ($info) {
            $return['data'] = $info;
        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    /**
     * 上传图片
     */
    public function uploadPicture()
    {
        if(!is_login()){
			$this->error('还未登陆，无法操作');
		}

        /* 返回标准数据 */
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Admin/Picture');

        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            $driver,
            $uploadConfig
        );
        //TODO:上传到远程服务器
        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            if ($info['Filedata']) {
                $return = array_merge($info['Filedata'], $return);
            }
            if ($info['download']) {
                $return = array_merge($info['download'], $return);
            }
            /*适用于自动表单的图片上传方式*/
            if ($info['file'] || $info['files']) {
                $return['data']['file'] = $info['file']?$info['file']:$info['files'];
            }
			if(empty($return['data'])){
				$return['data'] = $info;
			}
            /*适用于自动表单的图片上传方式end*/
            $aWidth= I('get.width',0,'intval');
            $aHeight=   I('get.height',0,'intval');
            if($aHeight<=0){
                $aHeight='auto';
            }
            if($aWidth>0){
                $return['path_self']=getThumbImageById($return['id'],$aWidth,$aHeight);
            }
        } else {
            $return['status'] = 0;
            $return['info'] = $Picture->getError();
        }


        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    /**用于兼容UM编辑器的图片上传方法
     */
    public function uploadPictureUM()
    {
        header("Content-Type:text/html;charset=utf-8");
        //TODO: 用户登录检测
        /* 返回标准数据 */
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');

        //实际有用的数据只有name和state，这边伪造一堆数据保证格式正确
        $originalName = 'u=2830036734,2219770442&fm=21&gp=0.jpg';
        $newFilename = '14035912861705.jpg';
        $filePath = 'upload\/20140624\/14035912861705.jpg';
        $size = '7446';
        $type = '.jpg';
        $status = 'success';
        $rs = array(
            "originalName" => $originalName,
            'name' => $newFilename,
            'url' => $filePath,
            'size' => $size,
            'type' => $type,
            'state' => $status,
			'original' => $_FILES['upfile']['name']
        );
        /* 调用文件上传组件上传文件 */
        $Picture = D('Admin/Picture');

        $setting = C('EDITOR_UPLOAD');
        $setting['rootPath']='./uploads/editor/picture/';

        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        $info = $Picture->upload(
            $_FILES,
            $setting,
            $driver,
            $uploadConfig
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            if ($info['Filedata']) {
                $return = array_merge($info['Filedata'], $return);
            }
            if ($info['download']) {
                $return = array_merge($info['download'], $return);
            }
            $rs['state'] = 'SUCCESS';
            $rs['url'] = $info['upfile']['path'];
            if ($type == 'ajax') {
                echo json_encode($rs);
                exit;
            } else {
                echo json_encode($rs);
                exit;
            }

        } else {
            $return['state'] = 0;
            $return['info'] = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    public function uploadFileUE(){
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');

        //实际有用的数据只有name和state，这边伪造一堆数据保证格式正确
        $originalName = 'u=2830036734,2219770442&fm=21&gp=0.jpg';
        $newFilename = '14035912861705.jpg';
        $filePath = 'upload\/20140624\/14035912861705.jpg';
        $size = '7446';
        $type = '.jpg';
        $status = 'success';
        $rs = array(
            'name' => $newFilename,
            'url' => $filePath,
            'size' => $size,
            'type' => $type,
            'state' => $status
        );

        /* 调用文件上传组件上传文件 */
        $File = D('Admin/File');

        $driver = modC('DOWNLOAD_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        $setting = C('EDITOR_UPLOAD');
        $setting['rootPath']='./uploads/editor/file/';


        $setting['exts'] = 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,xlsx,xls,ppt,pptx,pdf';
        $info = $File->upload(
            $_FILES,
            $setting,
            $driver,
            $uploadConfig
        );

        /* 记录附件信息 */
        if ($info) {
            $return['data'] = $info;

            $rs['original'] = $info['upfile']['name'];
            $rs['state'] = 'SUCCESS';
            $rs['url'] =  strpos($info['upfile']['savepath'], 'http://') === false ?  __ROOT__.$info['upfile']['savepath'].$info['upfile']['savename']:$info['upfile']['savepath'];
            $rs['size'] = $info['upfile']['size'];
            $rs['title'] = $info['upfile']['savename'];


            if ($type == 'ajax') {
                echo json_encode($rs);
                exit;
            } else {
                echo json_encode($rs);
                exit;
            }



        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    public function uploadAvatar(){

        $aUid = I('get.uid',0,'intval');

        mkdir ("./uploads/avatar/".$aUid);


        $files = $_FILES;
        $setting  = C('PICTURE_UPLOAD');

        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);


        /* 上传文件 */
        $setting['rootPath'] = './uploads/avatar';
        $setting['saveName'] = array('uniqid', '/'.$aUid.'/');
        $setting['savepath'] = '';
        $setting['subName'] = '';
        $setting['replace'] = true;

        //sae下
        if (strtolower(C('PICTURE_UPLOAD_DRIVER'))  == 'sae') {
            // $config[]
            C(require_once(APP_PATH . 'common/conf/config_sae.php'));

            $Upload = new \think\Upload($setting,C('PICTURE_UPLOAD_DRIVER'), array(C('UPLOAD_SAE_CONFIG')));
            $info = $Upload->upload($files);

            $config=C('UPLOAD_SAE_CONFIG');
            if ($info) { //文件上传成功，记录文件信息
                foreach ($info as $key => &$value) {
                    $value['path'] = $config['rootPath'] . 'avatar/' . $value['savepath'] . $value['savename']; //在模板里的url路径

                }
                /* 设置文件保存位置 */
                $this->_auto[] = array('location', 'Ftp' === $driver ? 1 : 0, self::MODEL_INSERT);
            }
        }else{
            $Upload = new \think\Upload($setting, $driver, $uploadConfig);
            $info = $Upload->upload($files);
        }
        if ($info) { //文件上传成功，不记录文件
            $return['status'] = 1;
            if ($info['Filedata']) {
                $return = array_merge($info['Filedata'], $return);
            }
            if ($info['download']) {
                $return = array_merge($info['download'], $return);
            }
            /*适用于自动表单的图片上传方式*/
            if ($info['file']) {
                $return['data']['file'] = $info['file'];

                $path = $info['file']['url'] ? $info['file']['url'] : "./uploads/avatar".$info['file']['savename'];
                $src = $info['file']['url'] ? $info['file']['url'] : __ROOT__."/uploads/avatar".$info['file']['savename'];
               // $return['data']['file']['path'] =;
                $return['data']['file']['path'] =$path;
                $return['data']['file']['src']=$src;
                $size =  getimagesize($path);
                $return['data']['file']['width'] =$size[0];
                $return['data']['file']['height'] =$size[1];
                $return['data']['file']['time'] =time();
            }
        } else {
            $return['status'] = 0;
            $return['info'] = $Upload->getError();
        }

        $this->ajaxReturn($return);
    }

}
