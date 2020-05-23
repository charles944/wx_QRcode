<?php
namespace admin\controller;

/**
 * 后台首页控制器
 * @author 朝兮夕兮
 */
class PublicController extends \think\Controller {
	

	public function lock($username = null, $password = null){
		if(IS_POST){
			$uid = D('User/UcenterMember')->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('User/Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
					$this->success('解锁成功！', U('index/index'));
				} else {
					$this->error($Member->getError());
				}
			
			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}
		}else{
			/* 读取数据库中的配置 */
			/* $config	=	S('DB_CONFIG_DATA');
			if(!$config){
				$config	=	D('Config')->lists();
				S('DB_CONFIG_DATA',$config);
			}
			C($config); */ //添加配置
			$this->display();
		}
	}
    /**
     * 后台用户登录
     * @author 朝兮夕兮
     */
	public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            if (APP_DEBUG==false){
                if(!check_verify($verify)){
                    $this->error('验证码输入错误！');
                }
            }
            $aUnType = check_username($username);
            $uid = D('User/UcenterMember')->login($username, $password, $aUnType);
            if(0 < $uid){
                $this->success('登录成功！', U('Index/index'));
            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    case -3: $error = '账号未激活'; break;
                    case -4: $error = '账号已禁用'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
            if(is_login()){
                $this->redirect('index/index');
            }else{
                /* 读取数据库中的配置 */
                /* $config	=	S('DB_CONFIG_DATA');
                if(!$config){
                    $config	=	D('Config')->lists();
                    S('DB_CONFIG_DATA',$config);
                }
                C($config); */ //添加配置
                $domain_url = get_domain();
                $s = S($domain_url.'_qn_admin_auth');
                $this->assign('license', $s['msg']);
                $this->assign('license_color', $s['isaudit']);
				//$this->assign('MODULE_ALIAS', getModule());
                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('User/Member')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function verify(){
        verify();
       // $verify = new \think\Verify();
       // $verify->entry(1);
    }

}