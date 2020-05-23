<?php
namespace home\widget;
use think\Action;

class LoginWidget extends Action
{
    public function login($type = "quickLogin")
    {
		if (is_login()) {
			redirect(U('home/index/index'));
		}
		$this->assign('login_type', $type);
		$ph = array();
		check_login_type('username') && $ph[] = '用户名';
		check_login_type('email') && $ph[] = '邮箱';
		check_login_type('mobile') && $ph[] = '手机号';
		$this->assign('ph', implode('/', $ph));
        if ($type != "quickLogin") {
			$this->display('widget/login/login');
        }else{
			$this->display('widget/login/quicklogin');
		}
    }

    public function doLogin()
    {
        $aUsername = $username = I('post.username', '', 'op_t');
        $aPassword = I('post.password', '', 'op_t');
        $aVerify = I('post.verify', '', 'op_t');
        $aRemember = I('post.remember', 0, 'intval');
		$res['status']=0;

        /* 检测验证码 */
        if (check_verify_open('login')) {
            if (!check_verify($aVerify)) {
                $res['info']="验证码输入错误。";
                return $res;
            }
        }

        $aUnType = check_username($aUsername);

        if (!check_reg_type($aUnType)) {
            $res['info']="该类型未开放登录。";
        }

        $uid = D('User/UcenterMember')->login($username, $aPassword, $aUnType, $aRemember);
        if (0 < $uid) {
            $res['status']=1;
            $res['info']='登陆成功';
        } else {
			//登录失败
            switch ($uid) {
                case -1:
                    $res['info']= '用户不存在！';
                    break; //系统级别禁用
                case -2:
                    $res['info']= '密码错误！';
                    break;
                case -3:
                    $res['info'] = '账号未激活，请先激活';
                    $res['url'] = U('home/member/activate');
                    break;
                case -4:
                    $res['info'] = '用户已被禁用';
                    break;
                default:
                    $res['info']= $uid;
                    break; // 0-接口参数错误（调试阶段使用）
            }
        }
        return $res;
    }
}