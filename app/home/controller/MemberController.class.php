<?php
namespace home\controller;

use think\Controller;
//require_once APP_PATH . 'user/conf/config.php';
/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class MemberController extends Controller
{
	//register  注册页面
	public function register()
	{
		if (!modC('REG_SWITCH', '', 'USERCONFIG')) {
			$this->error('注册已关闭');
		}
		if (is_login()) {
			$this->error('亲，当前已经登录，不用再注册啦~','home/index/index');
		}
		if (IS_POST) {
			$result = A('home/Register', 'Widget')->doRegister();
			if ($result['status']) {
				$this->success($result['info'],I('post.from',U('home/index/index'),'text'));
			} else {
				$this->error($result['info']);
			}
		} else { //显示注册表单
			$this->checkRegisterType();
			$aType = I('get.type', '', 'op_t');
			$regSwitch = modC('REG_SWITCH', '', 'USERCONFIG');
			$regSwitch = explode(',', $regSwitch);
			$this->assign('regSwitch', $regSwitch);
			$this->assign('type', $aType == '' ? $regSwitch[0] : $aType);
			$this->display();
		}
	}

	// 快捷注册页面
	public function quickRegister()
	{
		if (!modC('REG_SWITCH', '', 'USERCONFIG')) {
			$this->error('注册已关闭');
		}
		if(is_login()){
			$this->redirect('home/index/index');
		}
		if (IS_POST) {
			$result = A('home/Register', 'Widget')->doRegister();
			if ($result['status']) {
				$this->success($result['info'],I('post.from',U('home/index/index'),'text'));
			} else {
				$this->error($result['info']);
			}
		} else {
			$this->checkRegisterType();
			$aType = I('get.type', '', 'op_t');
			$regSwitch = modC('REG_SWITCH', '', 'USERCONFIG');
			$regSwitch = explode(',', $regSwitch);
			$this->assign('regSwitch', $regSwitch);
			$this->assign('type', $aType == '' ? $regSwitch[0] : $aType);
			$this->display();
		}
	}

	public function checkInviteCode()
	{
		if (IS_POST) {
			$aType = I('get.type', '', 'op_t');
			$aCode = I('post.code', '', 'op_t');
			$result['status'] = 0;
			if (!mb_strlen($aCode)) {
				$result['info'] = "请输入邀请码！";
				$this->ajaxReturn($result);
			}
			$invite = D('Ucenter/Invite')->getByCode($aCode);
			if ($invite) {
				if ($invite['end_time'] > time()) {
					$result['status'] = 1;
				} else {
					$result['info'] = "该邀请码已过期！请更换其他邀请码！";
				}
			} else {
				$result['info'] = "不存在该邀请码！请核对邀请码！";
			}
			$this->ajaxReturn($result);
		}
	}
	
	public function upgroup()
	{
		if (IS_POST) {
			$aGroupId = I('group_id', 0, 'intval');
			$uid = is_login();
			$group_id = get_login_group();//当前登录角色
			if($group_id == $aGroupId){
				$this->error('已经是此等级，无需升级！');
			}

			$groupModel=D('AuthGroup');
			$userGroupModel=D('AuthGroupAccess');
			$roleUser = $userGroupModel->where(array('uid' => $uid, 'group_id' => $aGroupId))->find();
			if ($roleUser) {
				$this->error('已经拥有此等级！');
			}
			
			$role_data = $groupModel->where(array('id'=>$aGroupId,'status'=>1))->find();
			if(empty($role_data)){
				$this->error('抱歉，此等级暂时无法使用');
			}
			if($role_data['levelmod'] == 'close'){
				$this->error('抱歉，此等级不支持升级');
			}
			switch($role_data['levelmod']){
				case 'buy':
					$my_money = getMyScore('money');
					if(floatval($my_money) >= floatval($role_data['buyvalue'])){
						$title_d = '恭喜，升级'.$role_data['title'].'等级成功';
						$content_d = '升级'.$role_data['title'].'，花费余额'.$role_data['buyvalue'].'元';

                        api('User/sendReward',array($uid, 0, 'Member', 'dec', 'money', $role_data['buyvalue'], $title_d, $content_d));

                        D('User/Member')->initUser($role_data['id'], $uid);

                        clean_query_user_cache($uid);
                        D('User/Member')->logout();
                        D('User/UcenterMember')->checkLogin($uid);

                        $this->success('使用余额'.$role_data['buyvalue'].'元，升级'.$role_data['title'].'成功！');
					}else{
						$param_child['group_id'] = $aGroupId;
						$param_child['money'] = $role_data['buyvalue'];
						$param_child['pay_name'] = '升级'.$role_data['title'];
						$param['t'] = base64_encode(urlencode(json_encode($param_child)));
						$this->success('余额不足，即将跳转充值页面...',U('Ucenter/Pay/Index',$param));
					}
					break;
				case 'invite':
					$this->assign('group_id', $aGroupId);
					$this->display();
				break;
				case 'authen':
					$this->assign('group_id', $aGroupId);
					$this->display();
				break;
				case 'close':
					$this->error('抱歉，此等级不支持升级');
				break;
				default:
					$this->error('抱歉，此等级不支持升级');
				break;
			}
		} else {
			$this->error('参数错误');
		}
	}
	
	public function upRoleExtend(){
		$t = I('get.t','','text');
		
		$param['uid'] = I('get.uid','','text');
		$param['order_no'] = I('get.order_no','','text');
		$param['charge_id'] = I('get.charge_id','','text');
		$param['role_id'] = I('get.role_id','','text');
		$param['addons'] = I('get.addons','','text');
		$param['sign'] = I('get.sign','','text');
		$newsign = strtolower(md5($param['addons'].$param['charge_id'].$param['order_no'].$param['role_id'].$param['uid']));
		$uid = is_login();
		if(empty($uid)){
			$this->error('当前登陆丢失，请重新登陆，如已经充值，请到身份升级页面继续升级！',U('home/member/login'));
		}
		if(!empty($param)){
			if($newsign != $param['sign']){
				$this->error('校验失败！');
			}
			//解析反馈的参数
			if(intval($param['uid']) != intval($uid)){
				$this->error('用户ID不符，无法升级身份');
			}
			$param_map['uid'] = $param['uid'];
			$param_map['order_no'] = $param['order_no'];
			$param_map['charge_id'] = $param['charge_id'];
			$param_map['is_success'] = 'true';
			$result = D($param['addons'])->where($param_map)->find();
			if(empty($result)){
				$this->error('您没有充值，无法升级身份');
			}
			
			//判断升级的身份信息是否符合
			$role_id = get_login_role();//当前登录角色
			$groupModel = D('Role');
			$userGroupModel = D('UserRole');
			$already_role_list = $userGroupModel->where(array('uid'=>$uid))->field('role_id,status')->select();
			$already_role_ids = array_column($already_role_list,'role_id');
			$already_role_list = array_combine($already_role_ids,$already_role_list);
			$map_already_roles['id']=array('in',$already_role_ids);
			$map_already_roles['status']=1;
			$already_roles=$groupModel->where($map_already_roles)->order('sort asc')->select();
			$already_group_ids=array_unique(array_column($already_roles,'group_id'));
			$already_group_ids=array_diff($already_group_ids,array(0));//去除无分组角色组
			if(count($already_group_ids)){
				$map_can_have_roles['group_id']=array('not in',$already_group_ids);//同组内的角色不显示
			}
			$map_can_have_roles['id']=array('not in',$already_role_ids);//去除已有角色
			$map_can_have_roles['canlevel']=1;//前台可显示
			$map_can_have_roles['status']=1;
			$can_have_roles = $groupModel->where($map_can_have_roles)->order('sort asc')->select();//可持有角色
			$can_have_roles_ids = array_column($can_have_roles,'id');
			if(!in_array($param['role_id'],$can_have_roles_ids)){
				$this->error('升级信息错误，无法升级');
			}
			
			$roleUser = D('UserRole')->where(array('uid' => $uid, 'role_id' => $param['role_id']))->find();
			if ($roleUser) {
				$this->error('已持有该身份！');
			} else {
				$score_type = modC('CHARGE_SCORE_TYPE','gold_coin','PAY');
				$field = D('User/UserScoreType')->where(array('status'=>1, 'mark'=>$score_type))->getField('exchange');
				if(intval($field) <= 0){
					$field = 1;
				}
				$amount_fee = floatval($result['amount'])*$field;
				
				//检测充值金额是否够升级身份
				$role = $groupModel->where(array('id'=>$param['role_id'],'status'=>1))->find();
				if(empty($role)){
					$this->error('无此身份可升级', U('Ucenter/Config/Role'));
				}
				if($amount_fee < $role['buyvalue']){
					$this->error('抱歉，你的充值金额不够升级此身份！', U('Ucenter/Config/Role'));
				}
				/* if($amount_fee < $role['buyvalue']){
					$this->error('抱歉，你的充值金额不够升级此身份！');
				} */
				
				$title = '【'.$result['subject'].'】扣除'.$amount_fee;
				$content = '【'.$result['subject'].'】扣除'.$amount_fee;
				if($uid > 1){
					api('User/sendReward', array($uid, 0, 'qkepay', 'dec', 'money', $amount_fee, $title, $content));
				}
				$memberModel = D('User/Member');
				$memberModel->logout();
				$memberModel->initUser($param['role_id'], $uid);
				clean_query_user_cache($uid,array('avatar64','avatar128','avatar32','avatar256','avatar512','rank_link'));
				$memberModel->login($uid, false, $param['role_id']); //登陆
				$this->success('恭喜，升级身份成功！', U('Ucenter/Config/Role'));
			}
			
		}else{
			$this->error('参数错误！');
		}
	}
	
	//登录页面
	public function login()
	{
		$this->setTitle('用户登录');
		if(is_login()){
			$this->redirect('home/index/index');
		}
		if (IS_POST) {
			$result = A('home/Login', 'Widget')->doLogin();
			if ($result['status']) {
				$this->success($result['info'],I('post.from',U('home/index/index'),'text'));
			} else {
				$this->error($result['info'], $result['url']);
			}
		} else {
			$this->display();
		}
	}
	
	//快捷登录登录页面
	public function quickLogin()
	{
		if(is_login()){
			$this->redirect('home/index/index');
		}
		if (IS_POST) {
			$result = A('home/Login', 'Widget')->doLogin();
			$this->ajaxReturn($result);
		} else { //显示登录弹出框
			$this->display();
		}
	}
	
	// 退出登录
	public function logout()
	{
		if (is_login()) {
			D('User/Member')->logout();
			$this->success('退出登陆成功。','',array('url' => '','html'=>$html,'status' => 1));
		} else {
			$this->error('退出登陆成功。','user/login',array('url' => '','html'=>$html,'status' => 1));
			//exit(json_encode(array('info' =>'退出登陆成功。','url' => 'user/login','status' => 0)));
		}
	}
	
	// 验证码，用于登录和注册
	public function verify()
	{
		verify();
		//  $verify = new \think\Verify();
		//  $verify->entry(1);
	}
	
	// 用户密码找回首页
	public function mi()
	{
		$aEmail = I('post.email', '', 'op_t');
        $aMobile = I('post.mobile', '', 'op_t');
        $aVerify = I('post.verify', '', 'op_t');
        $aMobVerify = I('post.reg_verify', '', 'op_t');
        $do = I('post.do','','op_t');
		$aEmail = strval($aEmail);
		if (IS_POST) {
			if($do == 'email'){
				if(empty($aEmail)){
					$this->error('请输入待找回邮箱账号');
				}
				//检测验证码
				if (!check_verify($aVerify)) {
					$this->error('验证码输入错误');
				}
				//根据用户名获取用户UID
				$user = D('User/UcenterMember')->where(array('email' => $aEmail, 'status' => 1))->find();
				$uid = $user['id'];
				$username = $user['username'];
				if (!$uid) {
					$this->error("邮箱账号错误");
				}
				//生成找回密码的验证码
				$verify = $this->getResetPasswordVerifyCode($uid);
				//发送验证邮箱
				$sitetitle = modC('WEB_SITE_NAME', '靑年PHP', 'Config');
				$url = 'http://' . $_SERVER['HTTP_HOST'] . U('home/member/reset?uid=' . $uid . '&verify=' . $verify);
				$content = D('mail_tpl')->where(array('action_name'=>'user_repass'))->getField('template_content');
				$content = str_replace('{url}', $url, $content);
				$content = str_replace('{account}', $username, $content);
				$content = str_replace('{title}', $sitetitle, $content);
				$data['uid'] = $uid;
				$data['mail_to'] = $aEmail;
				$data['mail_subject'] = $sitetitle . "密码找回";
				$data['mail_body'] = $content;
				$data['add_time'] = TIME();
				$queue_id = D('mail_queue')->add($data);
				$res = send_mail($aEmail, $sitetitle . "密码找回", $content);
				D('mail_queue')->where(array('id'=>$queue_id))->save(array('msg'=>json_encode($res)));
				$this->success('密码找回邮件发送成功，请到邮箱里面查收邮件并按操作提示重置密码', U('home/member/login'));
			}else if($do == 'mobile'){
				if(empty($aMobile)){
					$this->error('请输入正确的手机号码');
				}
				if(empty($aMobVerify)){
					$this->error('请输入手机验证码');
				}

                $isVerify=D('Common/Verify')->checkVerify($aMobile,$type='mobile',$aMobVerify,0);

                if($isVerify){
                    $user=D('User/UcenterMember')->where(array('mobile'=>$aMobile,'status'=>1))->find();
                    if (empty($user)) {
                        $this->error('此手机号码未注册');
                    }
                    $ucModel = D('User/UcenterMember');
                    $newpass = $this->get_password();
                    $res = $ucModel->where(array('id'=>$user['id'],'status'=>1))->save(array('password' =>think_ucenter_md5($newpass, UC_AUTH_KEY)));
                    if ($res) {
                    	$resb = sendSMS($aMobile, '你的新密码重置为：'.$newpass.'，请尽快修改密码，不要告诉其他人', 'resetpassword');
		                if($resb){
		                    $sms_data['uid']= $user['id'];
		                    $sms_data['verify'] = $newpass;
		                    $sms_data['mobile'] = $aMobile;
		                    $sms_data['status'] = 1;
		                    $sms_data['msg'] = $resb;
		                    $sms_data['ip'] = get_client_ip(1);
		                    $sms_data['create_time'] = TIME();
		                    $result = D('Sms')->add($sms_data);
		                }
                    	$this->success('重置密码成功，请注意查收短信！');
                    } else {
                    	$this->error('重置密码失败，请联系客服');
                    }
                }else{
                    $this->error('手机验证码错误');
                }
			}
		} else {
			if (is_login()) {
				redirect(U(C('AFTER_LOGIN_JUMP_URL')));
			}
			$this->display();
		}
	}

	private function get_password( $length = 6 )
	{
		$str = substr(md5(time()), 0, $length);
		return $str;
	}

	// 重置密码
	public function reset($uid, $verify)
	{
		//检查参数
		$uid = intval($uid);
		$verify = strval($verify);
		if (!$uid || !$verify) {
			$this->error("参数错误");
		}
		//确认邮箱验证码正确
		$expectVerify = $this->getResetPasswordVerifyCode($uid);
		if ($expectVerify != $verify) {
			$this->error("参数错误");
		}
		//将邮箱验证码储存在SESSION
		session('reset_password_uid', $uid);
		session('reset_password_verify', $verify);
		//显示新密码页面
		$this->display();
	}
	
	public function doReset($password, $repassword)
	{
		//确认两次输入的密码正确
		if ($password != $repassword) {
			$this->error('两次输入的密码不一致');
		}
		//读取SESSION中的验证信息
		$uid = session('reset_password_uid');
		$verify = session('reset_password_verify');
		//确认验证信息正确
		$expectVerify = $this->getResetPasswordVerifyCode($uid);
		if ($expectVerify != $verify) {
			$this->error("验证信息无效");
		}
		//将新的密码写入数据库
		$data = array('id' => $uid, 'password' => think_ucenter_md5($password, UC_AUTH_KEY));
		$model = D('User/UcenterMember');
		if (!$data) {
			$this->error('密码格式不正确');
		}
		$result = $model->where(array('id' => $uid))->save($data);
		if ($result === false) {
			$this->error('数据库写入错误');
		}
		//显示成功消息
		$this->success('密码重置成功', U('home/member/login'));
	}
	
	private function getResetPasswordVerifyCode($uid)
	{
		$user = D('User/UcenterMember')->where(array('id' => $uid))->find();
		$clear = implode('|', array($user['uid'], $user['username'], $user['last_login_time'], $user['password']));
		$verify = qn_hash($clear, UC_AUTH_KEY);
		return $verify;
	}
	
	// 获取用户注册错误信息
	public function showRegError($code = 0)
	{
		switch ($code) {
			case -1:
				$error = '用户名长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
				break;
			case -2:
				$error = '用户名被禁止注册！';
				break;
			case -3:
				$error = '用户名被占用！';
				break;
			case -4:
				$error = '密码长度必须在6-30个字符之间！';
				break;
			case -5:
				$error = '邮箱格式不正确！';
				break;
			case -6:
				$error = '邮箱长度必须在4-32个字符之间！';
				break;
			case -7:
				$error = '邮箱被禁止注册！';
				break;
			case -8:
				$error = '邮箱被占用！';
				break;
			case -9:
				$error = '手机格式不正确！';
				break;
			case -10:
				$error = '手机被禁止注册！';
				break;
			case -11:
				$error = '手机号被占用！';
				break;
			case -20:
				$error = '用户名只能由数字、字母和"_"组成！';
				break;
			case -30:
				$error = '昵称被占用！';
				break;
			case -31:
				$error = '昵称被禁止注册！';
				break;
			case -32:
				$error = '昵称只能由数字、字母、汉字和"_"组成！';
				break;
			case -33:
				$error = '昵称长度必须在'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
				break;
			default:
				$error = '未知错误24';
		}
		return $error;
	}
	
	// 修改密码提交
	public function profile()
	{
		if (!is_login()) {
			$this->error('您还没有登陆', U('home/member/login'));
		}
		if (IS_POST) {
			//获取参数
			$uid = is_login();
			$password = I('post.old');
			$repassword = I('post.repassword');
			$data['password'] = I('post.password');
			empty($password) && $this->error('请输入原密码');
			empty($data['password']) && $this->error('请输入新密码');
			empty($repassword) && $this->error('请输入确认密码');
			if ($data['password'] !== $repassword) {
				$this->error('您输入的新密码与确认密码不一致');
			}
			if(D('User/UcenterMember')->updateUserFields($uid, $password, $data) !== false){
	            $res['status'] = true;
	        }else{
	            $res['status'] = false;
	            $res['info'] = D('User/UcenterMember')->getError();
	        }
			if ($res['status']) {
				$this->success('修改密码成功！');
			} else {
				$this->error($res['info']);
			}
		} else {
			$this->display();
		}
	}
	
	/**
	 * activate  提示激活页面
	 * @author:
	 */
	public function activate()
	{
		// $aUid = I('get.uid',0,'intval');
		$aUid = session('temp_login_uid');
		$status = D('User/UcenterMember')->where(array('id' => $aUid))->getField('status');
		if ($status != 3) {
			redirect(U('home/member/login'));
		}
		$info = query_user(array('uid', 'nickname', 'email'), $aUid);
		$this->assign($info);
		$this->display();
	}
	/**
	 * reSend  重发邮件
	 * @author:
	 */
	public function reSend()
	{
		$res = $this->activateVerify();
		if ($res === true) {
			$this->success('发送成功');
		} else {
			$this->error('发送失败，请稍候再试！' . $res);
		}
	}
	/**
	 * changeEmail  更改邮箱
	 * @author:
	 */
	public function changeEmail()
	{
		$aEmail = I('post.email', '', 'op_t');
		$aUid = session('temp_login_uid');
		$ucenterMemberModel = D('User/UcenterMember');
		$ucenterMemberModel->where(array('id' => $aUid))->getField('status');
		if ($ucenterMemberModel->where(array('id' => $aUid))->getField('status') != 3) {
			$this->error('权限不足！');
		}
		$ucenterMemberModel->where(array('id' => $aUid))->setField('email', $aEmail);
		clean_query_user_cache($aUid, 'email');
		$res = $this->activateVerify();
		if ($res === true) {
			$this->success('更换成功，请登录邮箱进行激活！如没收到激活信请稍候再试！');
		} else {
			$this->error('更换成功，但是发送失败，请稍候再试！'. $res);
		}
	}
	/**
	 * activateVerify 添加激活验证
	 * @return bool|string
	 * @author:
	 */
	private function activateVerify()
	{
		$aUid = session('temp_login_uid');
		$email = D('User/UcenterMember')->where(array('id' => $aUid))->getField('email');
		$verify = D('Common/Verify')->addVerify($email, 'email', $aUid);
		$res = $this->sendActivateEmail($email, $verify, $aUid); //发送激活邮件
		return $res;
	}
	/**
	 * sendActivateEmail   发送激活邮件
	 * @param $account
	 * @param $verify
	 * @return bool|string
	 * @author:
	 */
	private function sendActivateEmail($account, $verify, $uid)
	{
		$url2 =  $account . '||' . $verify . '||email||' . $uid;
		$url = 'http://' . $_SERVER['HTTP_HOST']. U('home/member/doActivate?s='.base64_encode($url2));
		$sitetitle = modC('WEB_SITE_NAME', '靑年PHP', 'Config');
		$content = D('mail_tpl')->where(array('action_name'=>'reg_email_active'))->getField('template_content');
		$content = str_replace('{url}', $url, $content);
		$content = str_replace('{title}', $sitetitle, $content);
		$data['uid'] = is_login();
		$data['mail_to'] = $account;
		$data['mail_subject'] = $sitetitle . "激活信";
		$data['mail_body'] = $content;
		$data['add_time'] = TIME();
		$queue_id = D('mail_queue')->add($data);
		$res = send_mail($account, $sitetitle . '激活信', $content);
		D('mail_queue')->where(array('id'=>$queue_id))->save(array('msg'=>json_encode($res)));
		return $res;
	}

	/**
	 * doActivate  激活步骤
	 * @author:
	 */
	public function doActivate()
	{
		$s = I('get.s','','text');
		if(!empty($s)){
			$real_param = base64_decode($s);
			$arr = explode('||',$real_param);
			$aAccount = $arr[0];
			$aVerify = $arr[1];
			$aType = $arr[2];
			$aUid = $arr[3];
			$user = D('User/UcenterMember')->where(array('id'=>$aUid))->find();
			if(empty($user)){
				$this->error('无此用户！');
			}
			if($user['status'] != 3){
				$this->error('当前用户状态错误，无法激活！');
			}
			$check = D('Common/Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
			if ($check) {
				if($aType == 'email'){
					set_user_field($aUid, 'email_ver', 1);
				}
				set_user_status($aUid, 1);
				$this->success('激活成功', U('home/index/index'));
			} else {
				$this->error('激活失败！');
			}
		}else{
			$this->error('参数错误');
		}
	}
	/**
	 * checkAccount  ajax验证用户帐号是否符合要求
	 * @author:
	 */
	public function checkAccount()
	{
		$aAccount = I('post.account', '', 'op_t');
        $aType = I('post.type', '', 'op_t');
        if (empty($aAccount)) {
			switch ($aType) {
				case 'username':
					$this->error('用户名不能为空！');
					break;
				case 'mobile':
					$this->error('手机号不能为空！');
					break;
				case 'email':
					$this->error('邮箱不能为空！');
					break;
				default:
					$this->error('用户名不能为空！');
					break;
			}
        }
        //check_username($aAccount);
        $mUcenter = D('User/UcenterMember');
        switch ($aType) {
            case 'username':
                empty($aAccount) && $this->error('用户名格式不正确！');
                $length = mb_strlen($aAccount, 'utf-8'); // 当前数据长度
                if ($length < modC('USERNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')) {
                    $this->error('用户名长度在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'之间');
                }
                $id = $mUcenter->where(array('username' => $aAccount))->getField('id');
                if ($id) {
                    $this->error('该用户名已经存在！');
                }
                preg_match("/^[a-zA-Z0-9_]{".modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').",".modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')."}$/", $aAccount, $result);
                if (!$result) {
                    $this->error('只允许字母和数字和下划线！');
                }
                break;
            case 'email':
                empty($aAccount) && $this->error('请输入邮箱账号');
                $length = mb_strlen($aAccount, 'utf-8'); // 当前数据长度
                if ($length < 4 || $length > 32) {
                    $this->error('邮箱长度在4-32之间');
                }
                $id = $mUcenter->where(array('email' => $aAccount))->getField('id');
                if ($id) {
                    $this->error('该邮箱已经存在！');
                }
                break;
            case 'mobile':
                empty($aAccount) && $this->error('请输入手机号码');
                $id = $mUcenter->where(array('mobile' => $aAccount))->getField('id');
                if ($id) {
                    $this->error('该手机号已经存在！');
                }
                break;
        }
        $this->success('验证成功');
	}
	/**
	 * checkNickname  ajax验证昵称是否符合要求
	 * @author:
	 */
	public function checkNickname()
	{
		$aNickname = I('post.nickname', '', 'op_t');
        if (empty($aNickname)) {
            $this->error('昵称不能为空！');
        }
        $length = mb_strlen($aNickname, 'utf-8'); // 当前数据长度
        if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            $this->error('昵称长度在'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'之间');
        }
        $memberModel = D('member');
        $uid = $memberModel->where(array('nickname' => $aNickname))->getField('uid');
        if ($uid) {
            $this->error('该昵称已经存在！');
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $aNickname, $result);
        if (!$result) {
            $this->error('只允许中文、字母和数字和下划线！');
        }
        $this->success('验证成功');
	}

	/**
	 * 判断注册类型
	 * @return bool
	 */
	private function checkRegisterType()
	{
		$register_type = modC('REGISTER_TYPE', 'normal', 'UserConfig');
		$register_type = explode(',', $register_type);
		if (!in_array('invite', $register_type) && !in_array('normal', $register_type)) {
			$this->error("网站已关闭注册！");
		}
		if (in_array('invite', $register_type) && !in_array('normal', $register_type)) {
			$this->assign('open_invite_register', 1);
		}
		if (in_array('invite', $register_type) && in_array('normal', $register_type)) {
			$this->assign('open_invite_register', 2);
		}
		return true;
	}

	/**
	 * saveAvatar  保存头像
	 * @author:
	 */
	public function saveAvatar()
	{
        $aUid = is_login();
        $aPath = I('post.path', '', 'intval');
		$find_avatarid = D('avatar_list')->where(array('id'=>$aPath,'status'=>1))->find();
		if($find_avatarid){
			$picture_data = D('picture')->where(array('id'=>$find_avatarid['thumb']))->find();
			$data = array('uid' => $aUid, 'status' => 1, 'is_temp' => 0, 'path' => $picture_data['path'],'driver'=> $picture_data['type'], 'create_time' => time());
			$res = M('avatar')->where(array('uid' => $aUid))->save($data);
			if (!$res) {
				M('avatar')->add($data);
			}
			clean_query_user_cache($aUid, array('avatar256', 'avatar128', 'avatar64', 'avatar32', 'avatar512'));
			$this->success('头像更新成功！');
		}else{
			$this->error('参数错误');
		}
	}
}