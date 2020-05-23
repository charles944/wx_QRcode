<?php
namespace home\widget;
use think\Action;

class RegisterWidget extends Action
{
    public function register($type = "quickRegister")
    {
		if ($type != "quickRegister") {
			$this->display('widget/register/register');
        }else{
			$this->display('widget/register/quickregister');
		}
    }

    public function doRegister(){
    	$aUsername = $username = I('post.username', '', 'op_t');
		$aNickname = I('post.nickname', '', 'op_t');
		$aPassword = I('post.password', '', 'op_t');
		$aVerify = I('post.verify', '', 'op_t');
		$aRegVerify = I('post.reg_verify', 0, 'intval');
		$aRegType = I('post.reg_type', '', 'op_t');
		$aGroup = I('post.group', 1, 'intval');
		$aCode = I('post.code','','text');
		$return = check_action_limit('reg', 'ucenter_member', 1, 1, true);
		if ($return && !$return['state']) {
			$this->error($return['info'], $return['url']);
		}

		// 第一步检测开放注册类型
		$register_type = modC('REGISTER_TYPE', 'normal', 'UserConfig');
		$register_type = explode(',', $register_type);
		if (!in_array('invite', $register_type) && !in_array('normal', $register_type)) {
			$this->error("网站已关闭注册！");
		}

		if(in_array('invite', $register_type)){
			if(!empty($aCode)){
				if (!$this->checkInviteCode($aCode)) {
					$this->error('非法邀请码！');
				}
			}
		}
		/* 检测验证码 */
		if (check_verify_open('reg')) {
			if (!check_verify($aVerify)) {
				$this->error('验证码输入错误。');
			}
		}
		if (empty($aGroup)) {
			$aGroup = 1;
		}
		if (($aRegType == 'mobile' && modC('MOBILE_VERIFY_TYPE', 0, 'USERCONFIG') == 1) || (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $aRegType == 'email')) {
			if (!D('Common/Verify')->checkVerify($aUsername, $aRegType, $aRegVerify, 0)) {
				$str = $aRegType == 'mobile' ? '手机' : '邮箱';
				$this->error($str . '验证失败');
			}
		}
		$aUnType = 0;
		//获取注册类型
		$aUnType = check_username($aUsername);
		if ($aRegType == 'email' && $aUnType != 2) {
			$this->error('邮箱格式不正确');
		}
		if ($aRegType == 'mobile' && $aUnType != 3) {
			$this->error('手机格式不正确');
		}
		if ($aRegType == 'username' && $aUnType != 1) {
			$this->error('用户名格式不正确');
		}
		if (!check_reg_type($aUnType)) {
			$this->error('该类型未开放注册。');
		}
		switch($aUnType){
			case 1:
				$aUsername = $aUsername;
				$email = '';
				$mobile = '';
				break;
			case 2:
				$email = $aUsername;
				$aUsername = '';
				$mobile = '';
				break;
			case 3:
				$mobile = $aUsername;
				$aUsername = '';
				$email = '';
				break;
			default:
				$aUsername = $aUsername;
				$email = '';
				$mobile = '';
				break;
		}
		// 注册用户
		$uid = D('User/UcenterMember')->register($aUsername, $aNickname, $aPassword, $email, $mobile, $aUnType);
		if (0 < $uid) {
			if(in_array('invite', $register_type)){
				if(!empty($aCode)){
					$this->initInviteUser($uid, $aCode, $aGroup);
				}
			}
			D('User/Member')->initUser($aGroup, $uid); //初始化角色用户
			if (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 1 && $aUnType == 2) {
				set_user_status($uid, 3);
				$verify = D('Common/Verify')->addVerify($email, 'email', $uid);
				$res = $this->sendActivateEmail($email, $verify, $uid); //发送激活邮件
				$this->success('注册成功，请登录邮箱进行激活', U('home/member/activate'));
			}
			$uid = D('User/UcenterMember')->login($username, $aPassword, $aUnType, 1);
			$this->success('注册成功', '');
		} else {
			$this->error(A('Home/Member')->showRegError($uid));
		}
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
		$url = 'http://' . $_SERVER['HTTP_HOST'] . U('home/member/doActivate?account=' . $account . '&verify=' . $verify . '&type=email&uid=' . $uid);
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
	 * 判断邀请码是否可用
	 * @param string $code
	 * @return bool
	 */
	private function checkInviteCode($code = '')
	{
		if($code==''){
			return true;
		}
		$invite = D('Ucenter/Invite')->getByCode($code);
		if ($invite['end_time'] >= time()) {
			$map['id'] = $invite['invite_type'];
			$invite_type = D('Ucenter/InviteType')->getSimpleData($map);
			if ($invite_type) {
				return true;
			}
		}
		return false;
	}

	private function initInviteUser($uid = 0, $code = '', $role = 0)
	{
		if ($code != '') {
			$inviteModel = D('Ucenter/Invite');
			$invite = $inviteModel->getByCode($code);
			$data['inviter_id'] = abs($invite['uid']);
			$data['uid'] = $uid;
			$data['invite_id'] = $invite['id'];
			$result = D('Ucenter/InviteLog')->addData($data, $role);
			if ($result) {
				D('Ucenter/InviteUserInfo')->addSuccessNum($invite['invite_type'], abs($invite['uid']));
				$invite_info['already_num'] = $invite['already_num'] + 1;
				if ($invite_info['already_num'] == $invite['can_num']) {
					$invite_info['status'] = 0;
				}
				$inviteModel->where(array('id' => $invite['id']))->save($invite_info);
				$map['id'] = $invite['invite_type'];
				$invite_type = D('Ucenter/InviteType')->getSimpleData($map);
				// if ($invite_type['is_follow']) {
				// 	$followModel = D('Common/Follow');
    //                 $followModel->addFollow($uid, abs($invite['uid']),1);
    //                 $followModel->addFollow(abs($invite['uid']), $uid,1);
				// }
				if($invite['uid']>0){
					//D('User/Score')->setUserScore(array($invite['uid']),'inc',$invite_type['income_score_type'],$invite_type['income_score']);
					api('User/sendReward', array(array($invite['uid']), 'invite', 'inc', $invite_type['income_score_type'], $invite_type['income_score'],'邀请奖励','邀请奖励'));
				}
			}
		}
		return true;
	}
}