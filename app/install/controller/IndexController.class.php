<?php
/**
* +----------------------------------------------------------------------------+
* | 靑年 PHP													    |
* +----------------------------------------------------------------------------+
* | Copyright (c) 2014~2016 http://www.qkephp.com All rights reserved.	|
* +----------------------------------------------------------------------------+
* | Author: 乐清乾坤网络有限公司版权所有（朝兮夕兮，那你自己想想）		|
* +----------------------------------------------------------------------------+
**/
namespace install\controller;
use think\Controller;
use think\Storage;

class IndexController extends Controller{
	
    //安装首页
    public function index(){
    	header("Content-Type:text/html;charset=utf-8");
    	if(is_file('conf/user.php')){
    		$msg = '已经成功安装，请不要重复安装!';
    		$this->error($msg);
        }
        if(is_file('conf/install.lock')){
        	$msg = '请删除install.lock文件后安装!';
        	$this->error($msg);
        }
       $this->assign('version_name',QN_VERSION_NAME);
        $this->assign('version', QN_VERSION);
        $this->display();
    }

    //安装完成
    public function complete(){
        $step = cookie('step');
        $this->assign('version', QN_VERSION);
        if(!$step){
            $this->redirect(U('home/index/index'));
        } elseif($step != 4) {
            $this->redirect("install/step{$step}");
        }

        // 写入安装锁定文件
        Storage::put('conf/install.lock', 'lock');
        if(!cookie('update')){
            //创建配置文件
            $this->assign('info',cookie('config_file'));
        }
        cookie('step', null);
        cookie('error', null);
        cookie('update',null);
        $this->display();
    }
}