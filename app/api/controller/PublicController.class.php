<?php
namespace api\controller;
use think\Controller;

class PublicController extends ApiController {

	public $info = array(
        'name' => 'public',
        'title' => '公共接口',
        'description' => '公共接口',
        'status' => 1,
        'author' => '靑年',
        'version' => '1.0.0'
    );
	
    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }
}