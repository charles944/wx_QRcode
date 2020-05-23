<?php
/**
* +----------------------------------------------------------------------------+
* | 靑年PHP  &  QingNianPHP													    |
* +----------------------------------------------------------------------------+
* | Copyright (c) 2014~2016 http://www.bajiuniandai.com All rights reserved.	|
* +----------------------------------------------------------------------------+
* | Author: 重庆捌玖年代网络科技有限公司版权所有（朝兮夕兮，那你自己想想）		|
* +----------------------------------------------------------------------------+
**/
namespace install\controller;
use think\Controller;
use think\Db;
use think\Storage;

class InstallController extends Controller{

    protected function _initialize(){
    	if(Storage::has( 'conf/install.lock')){
            $this->error('已经成功安装，请不要重复安装!');
        }
        $this->assign('version', QN_VERSION);
		$this->assign('version_name',QN_VERSION_NAME);
    }

    //安装第一步，检测运行所需的环境设置
    public function step1(){
        cookie('error', false);

        //环境检测
        $env = check_env();

        //目录文件读写检测
        if(IS_WRITE){
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }

        //函数检测
        $func = check_func();

        cookie('step', 1);

        $this->assign('env', $env);
        $this->assign('func', $func);
        $this->display();
    }
	
	public function step2(){
		if(IS_POST){
			header("Content-type:text/html;charset=utf8");
            $vcode = I('post.vcode','','text');
            if(empty($vcode)){
                $this->error('请输入激活码');
            }
            
            $result1 = $this->i_dog($vcode);
            $result = $this->d_dog($vcode);
            if($result){
                cookie('step',2);
                $this->success('激活成功，即将启动...',U('install/step3'));
            }
		}else{
			cookie('error') && $this->error('环境检测没有通过，请调整环境后重试！');

			$step = cookie('step');
			if($step != 1 && $step != 2){
				$this->redirect('step1');
			}

			cookie('step', 2);
			$this->display();
		}
	}

    //安装第二步，创建数据库
    public function step3($dbtype = null, $dbhost = null, $dbname = null, $dbuser = null, $dbpass = null, $dbport = null, $dbpre = null, $adminuser = null, $adminpass = null, $adminpass1 = null, $adminemail = null){
        if(IS_POST){
            //检测管理员信息
            if(empty($adminuser) || empty($adminpass) || empty($adminpass1) || empty($adminemail)){
                $this->error('请填写完整管理员信息');
            } else if($adminpass != $adminpass1){
                $this->error('确认密码和密码不一致');
            } else {
                $info = array();
				$admin = array($adminuser, $adminpass, $adminpass1, $adminemail);
                list($info['username'], $info['password'], $info['repassword'], $info['email']) = $admin;
                //缓存管理员信息
                cookie('admin_info', $info);
            }

            //检测数据库配置
            if(empty($dbhost) || empty($dbname) ||  empty($dbuser) || empty($dbpass) || empty($dbport) || empty($dbpre)){
                $this->error('请填写完整的数据库配置');
            } else {
                $DB = array();
				$db = array($dbtype, $dbhost, $dbname, $dbuser, $dbpass, $dbport, $dbpre);
                list($DB['DB_TYPE'], $DB['DB_HOST'], $DB['DB_NAME'], $DB['DB_USER'], $DB['DB_PWD'], $DB['DB_PORT'], $DB['DB_PREFIX']) = $db;
                //缓存数据库配置
                cookie('db_config',$DB);

                //创建数据库
                $dbname = $DB['DB_NAME'];
                unset($DB['DB_NAME']);

                $db  = Db::getInstance($DB);
                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
                $db->execute($sql);
                cookie('step',3);
                // $this->error($db->getError());exit;
            }
            $this->success('信息填写正确',U('install/step4'));
            //跳转到数据库安装页面
        } else {
                cookie('error') && $this->error('环境检测没有通过，请调整环境后重试！');

                $step = cookie('step');
                if($step != 2 && $step != 3){
                    $this->redirect('step1');
                }
                cookie('step', 3);
                $this->display();

        }
    }

    //安装第三步，安装数据表，创建配置文件
    public function step4(){
        if(cookie('step') != 3){
            $this->redirect('step3');
        }
		cookie('step', 4);
        $this->display();

		//连接数据库
		$dbconfig = cookie('db_config');
		$db = Db::getInstance($dbconfig);
		//创建数据表

		create_tables($db, $dbconfig['DB_PREFIX']);
		
		//注册创始人帐号
		$auth  = build_auth_key();
		$admin = cookie('admin_info');
		register_administrator($db, $dbconfig['DB_PREFIX'], $admin, $auth);

		//创建配置文件
		$conf   =   write_config($dbconfig, $auth);
		cookie('config_file',$conf);
		
		show_msg('活码系统安装完成...');
		$url = U('install/index/complete');
		show_overmsg('安装成功，下一步',$url);
		ob_flush();
		flush();
    }
}