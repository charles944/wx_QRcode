<?php
namespace admin\controller;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 * @author 乾坤网络有限公司
 */
class FileController extends AdminController {

    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

        /* 记录附件信息 */
        if($info){
			var_dump($info);
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }
	
	
	/**
     * 上传图片
     * @author 乾坤网络有限公司
     */
    public function uploadFile(){
    	//用户登录检测
    	if(!is_login()){
    		$return  = array('code' => 1, 'msg' => '你还没有登录！');
    		$this->ajaxReturn($return);
    	}
        /* 返回标准数据 */
        //$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

         /* 记录信息 */
        if($info){
            $return['code'] = 0;
            $return['url'] = $info;
            //$return['id'] = $info['imgFile']['id'];
            //empty($info['download']) && $info['download']= $info['file'];
            //$return = array_merge($info['download'], $return);
			/* array(1) {
			  ["logo"]=>
			  array(8) {
				["id"]=>
				string(1) "1"
				["type"]=>
				string(5) "local"
				["path"]=>
				string(60) "/qingnianphpnew/Uploads/Picture/2016-12-13/584fa2eb8e298.png"
				["url"]=>
				string(0) ""
				["md5"]=>
				string(32) "e602ecb38c9964b00938f939b46b4025"
				["sha1"]=>
				string(40) "313785defbbc402a2075e8f0032c67d7bd5aca8d"
				["status"]=>
				string(1) "1"
				["create_time"]=>
				string(10) "1481614059"
			  }
			} */
        } else {
            $return['code'] = 1;
            $return['msg']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /**
     * 上传图片
     * @author 乾坤网络有限公司
     */
    public function uploadPicture(){
    	//用户登录检测
    	if(!is_login()){
    		$return  = array('code' => 1, 'msg' => '你还没有登录！');
    		$this->ajaxReturn($return);
    	}
        /* 返回标准数据 */
        //$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
		$driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            $driver,
            $uploadConfig
        );
        //$pic_driver = C('PICTURE_UPLOAD_DRIVER');
        //$info = $Picture->upload($_FILES, C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'), C("UPLOAD_{$pic_driver}_CONFIG")); 

         /* 记录图片信息 */
        if($info){
            $return['code'] = 0;
            $return['url'] = $info;
            //$return['id'] = $info['imgFile']['id'];
            //empty($info['download']) && $info['download']= $info['file'];
            //$return = array_merge($info['download'], $return);
			/* array(1) {
			  ["logo"]=>
			  array(8) {
				["id"]=>
				string(1) "1"
				["type"]=>
				string(5) "local"
				["path"]=>
				string(60) "/qingnianphpnew/Uploads/Picture/2016-12-13/584fa2eb8e298.png"
				["url"]=>
				string(0) ""
				["md5"]=>
				string(32) "e602ecb38c9964b00938f939b46b4025"
				["sha1"]=>
				string(40) "313785defbbc402a2075e8f0032c67d7bd5aca8d"
				["status"]=>
				string(1) "1"
				["create_time"]=>
				string(10) "1481614059"
			  }
			} */
        } else {
            $return['code'] = 1;
            $return['msg']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
}
