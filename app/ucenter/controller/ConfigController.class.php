<?php
namespace ucenter\controller;
use think\Controller;
use think\Hook;
class ConfigController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $uid = is_login();
        $this->_assignSelf($uid);
        $this->_haveOtherRole();
    }
    /**是否拥有其他角色或可拥有角色*/
    private function _haveOtherRole()
    {
        $uid = is_login();
        $have=0;
        $groupModel=D('AuthGroup');
        $userGroupModel=D('AuthGroupAccess');
        $register_type=modC('REGISTER_TYPE','normal','UserConfig');
        $register_type=explode(',',$register_type);
        if(!in_array('invite',$register_type)){//开启邀请注册
            $map['status']=1;
            $map['invite']=0;
            if($groupModel->where($map)->count()>1){
                $have=1;
            }else{
                $map_user['uid']=$uid;
                $map_user['group_id']=array('neq',get_login_group());
                $map_user['status']=array('egt',0);
                $group_ids=$userGroupModel->where($map_user)->field('role_id')->select();
                if($group_ids){
                    $group_ids=array_column($group_ids,'group_id');
                    $map_can['status']=1;
                    $map_can['id']=array('in',$group_ids);
                    if($groupModel->where($map_can)->count()){
                        $have=1;
                    }
                }
            }
        }else{
            $map['status']=1;
            if($groupModel->where($map)->count()>1){
                $have=1;
            }
        }
        $this->assign('can_show_group',$have);
    }

    public function password()
    {
		$profile = $this->get_self_info(is_login());
		// if($profile['safecode'] == '' || empty($profile['safecode'])){
		// 	$this->redirect('safepass');
		// }
		$this->setTitle('设置我的密码');
        $this->_setTab('password');
        $this->display();
    }
	
	public function safepass()
    {
		$this->setTitle('设置我的安全码');
		$profile = $this->get_self_info(is_login());
		// if($profile['email'] == '' || empty($profile['email']) || (int)$profile['email_ver'] == 0){
		// 	$this->redirect('email');
		// }
		$this->assign('email',$profile['email']);
		$this->assign('safecode',$profile['safecode']);
		if(empty($profile['safecode'])){
			$tabhash = 'bind';
		}else{
			$tabhash = 'change';
		}
		$this->assign('tabhash',$tabhash);
        $this->_setTab('safepass');
        $this->display();
    }
	
	public function email()
    {
		$this->setTitle('设置我的邮箱');
    	$cash_email_on = modC('cash_email_on','1','pay');
    	$profile = $this->get_self_info(is_login());
		$this->assign('email',$profile['email']);
		if($profile['email'] != '' && $profile['email_ver'] == 1){
			$tabhash = 'unbind';
			// if($profile['safecode'] == '' || empty($profile['safecode'])){
			// 	$this->redirect('safepass');
			// }
		}else{
			$tabhash = 'bind';
		}
		$this->assign('tabhash',$tabhash);
		$this->assign('email_on',$cash_email_on);
        $this->_setTab('emailset');
        $this->display();
    }
	
	public function mobile()
    {
		$this->setTitle('设置我的手机');
		$cash_sms_on = modC('cash_sms_on','1','pay');
		$profile = $this->get_self_info(is_login());
		$this->assign('mobile',$profile['mobile']);
		if($profile['mobile'] != '' && $profile['mobile_ver'] == 1){
			$tabhash = 'unbind';
		}else{
			$tabhash = 'bind';
		}
		$this->assign('tabhash',$tabhash);
		$this->assign('sms_on',$cash_sms_on);
        $this->_setTab('mobileset');
        $this->display();
    }
    
    public function score()
    {
		$this->setTitle('查看我的积分');
    	$scoreModel = D('User/Score');
    	$scores = $scoreModel->getTypeList(array('status'=>1));
    	foreach ($scores as &$v) {
    		$v['value'] = $scoreModel->getUserScore(is_login(), $v['id']);
    	}
    	unset($v);
    	$this->assign('scores', $scores);
    
    	$level = nl2br(modC('LEVEL', '
0:Lv1 实习
50:Lv2 试用
100:Lv3 转正
200:Lv4 助理
400:Lv5 经理
800:Lv6 董事
1600:Lv7 董事长
        ', 'UserConfig'));
    	$this->assign('level', $level);
    
    	$action = D('Admin/Action')->getAction(array('status' => 1));
    	$action_module = array();
    	foreach ($action as &$v) {
    		$v['rule_array'] = unserialize($v['rule']);
    		foreach ($v['rule_array'] as &$o) {
    			if (is_numeric($o['rule'])) {
    				$o['rule'] = $o['rule'] > 0 ? '+' . intval($o['rule']) : $o['rule'];
    			}
    			$o['score'] = D('User/Score')->getType(array('id' => $o['field']));
    		}
    		if ($v['rule_array'] != false) {
    			$action_module[$v['module']]['action'][] = $v;
    		}
    
    	}
    	unset($v);
    
    	foreach ($action_module as $key => &$a) {
    		if (empty($a['action'])) {
    			unset($action_module[$key]);
    		}
    		$a['module'] = D('Common/Module')->getModule($key);
    	}
    
    	$this->assign('action_module', $action_module);
    	$this->_setTab('score');
    	$this->display();
    }
	
    public function other()
    {
		$this->setTitle('查看我的第三方绑定信息');
		$other = D('SyncLogin')->where(array('uid'=>is_login()))->select();
		
		$this->assign('other',$other);
        $this->_setTab('other');
        $this->display();
    }
	
    public function avatar()
    {
		$this->setTitle('设置我的头像');
        $this->_setTab('avatar');
        $this->display();
    }
	
    public function role()
    {
        $uid = is_login();
        $userGroupModel=D('AuthGroupAccess');
        $groupModel=D('AuthGroup');
        if(IS_POST){
            $aShowGroup=I('post.show_group',0,'intval');
            $map['group_id']=$aShowGroup;
            $map['uid']=$uid;
            $map['status']=array('egt',1);
            if(!$userGroupModel->where($map)->count()){
                $this->error('参数错误！');
            }
            $result=M('User/UcenterMember')->where(array('uid'=>$uid))->setField('show_group',$aShowGroup);
            if($result){
                clean_query_user_cache($uid,array('show_group'));
                $this->success('设置成功！');
            }else{
                $this->error('设置失败！');
            }
        }else{
            $group_id=get_login_group();
            $already_group_list=$userGroupModel->where(array('uid'=>$uid))->field('group_id,status')->select();
            $already_group_ids=array_column($already_group_list,'group_id');
            $already_group_list=array_combine($already_group_ids,$already_group_list);
            $map_already_groups['id']=array('in',$already_group_ids);
            $map_already_groups['status']=1;
            $already_groups=$groupModel->where($map_already_groups)->order('sort asc')->select();
            $already_group_ids=array_unique(array_column($already_groups,'id'));
            foreach($already_groups as &$val){
                $val['user_status']=$already_group_list[$val['id']]['status']!=2?($already_group_list[$val['id']]['status']==1)?'<span style="color: green;">已审核</span>':'<span style="color: #ff0000;">已禁用<span style="color: 333">(如有疑问，请联系管理员)</span></span>':'<span style="color: #0003FF;">正在审核</span>';;
                $val['can_login']=$val['id']==$group_id?0:1;
                $val['user_group_status']=$already_group_list[$val['id']]['status'];
            }
            unset($val);
            $already_group_ids=array_diff($already_group_ids,array(0));//去除无分组角色组
            if(count($already_group_ids)){
                $map_can_have_roles['group_id']=array('not in',$already_group_ids);//同组内的角色不显示
            }
            $map_can_have_roles['id']=array('not in',$already_group_ids);//去除已有角色
            $map_can_have_roles['canlevel']=1;//前台可显示
            $map_can_have_roles['status']=1;
            $can_have_roles=$groupModel->where($map_can_have_roles)->order('sort asc')->select();//可持有角色
           // $register_type=modC('REGISTER_TYPE','normal','UserConfig');
            //$register_type=explode(',',$register_type);
            //if(in_array('invite',$register_type)){//开启邀请注册
                /* $map_can_have_roles['invite']=1;
                $can_up_roles=$roleModel->where($map_can_have_roles)->order('sort asc')->select();//可升级角色
                $this->assign('can_up_roles',$can_up_roles); */
            // }
            $show_role=query_user(array('show_group'),$uid);
            $this->assign('show_group',$show_role['show_group']);
            $this->assign('already_groups',$already_groups);
            $this->assign('can_have_groups',$can_have_roles);
            $this->_setTab('role');
            $this->display();
        }
    }
	
    public function index()
    {
		$this->setTitle('设置我的资料');
        $aUid = I('get.uid', is_login(), 'intval');
        $aTab = I('get.tab', '', 'op_t');
        $aNickname = I('post.nickname', '', 'op_t');
        $aSex = I('post.sex', 0, 'intval');
        $aEmail = I('post.email', '', 'op_t');
        $aSignature = I('post.signature', '', 'op_t');
        $aCommunity = I('post.community', 0, 'intval');
        $aDistrict = I('post.district', 0, 'intval');
        $aCity = I('post.city', 0, 'intval');
        $aProvince = I('post.province', 0, 'intval');
        $qq = I('post.qq', 0, 'op_t');
        if (IS_POST) {
            $this->checkNickname($aNickname);
            $this->checkSex($aSex);
            $this->checkSignature($aSignature);
            $user['pos_province'] = $aProvince;
            $user['pos_city'] = $aCity;
            $user['pos_district'] = $aDistrict;
            $user['pos_community'] = $aCommunity;
            $user['nickname'] = $aNickname;
            $user['sex'] = $aSex;
            $user['signature'] = $aSignature;
            $user['uid'] = get_uid();
            $user['qq'] = $qq;
            $rs_member = D('User/Member')->save($user);
            $ucuser['id'] = get_uid();
            $rs_ucmember = D('User/UcenterMember')->save($ucuser);
            clean_query_user_cache(get_uid(), array('nickname','qq', 'sex', 'signature', 'email', 'pos_province', 'pos_city', 'pos_district', 'pos_community'));
            if ($rs_member || $rs_ucmember) {
                $this->success('设置成功');
            } else {
                $this->success('未修改数据');
            }
        } else {
            $this->assign('tab', $aTab);
            $this->_setTab('info');
            $this->display();
        }
    }
    /**验证用户名*/
    private function checkNickname($nickname)
    {
        $length = mb_strlen($nickname, 'utf8');
        if ($length == 0) {
            $this->error('请输入昵称。');
        } else if ($length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            $this->error('昵称不能超过'. modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'个字。');
        } else if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG')) {
            $this->error('昵称不能少于'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'个字。');
        }
        $match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname);
        if (!$match) {
            $this->error('昵称只允许中文、字母、下划线和数字。');
        }
        $map_nickname['nickname'] = $nickname;
        $map_nickname['uid'] = array('neq', is_login());
        $had_nickname = D('User/Member')->where($map_nickname)->count();
        if ($had_nickname) {
            $this->error('昵称已被人使用。');
        }
		$denyName = M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    $this->error('该昵称已被禁用。');
                }
            }
        }
    }
    /**验证签名*/
    private function checkSignature($signature)
    {
        $length = mb_strlen($signature, 'utf8');
        if ($length >= 100) {
            $this->error('签名不能超过100个字');
        }
    }

    public function changeAvatar()
    {
        $this->defaultTabHash('change-avatar');
        $this->display();
    }
    private function iframeReturn($result)
    {
        $json = json_encode($result);
        $json = htmlspecialchars($json);
        $html = "<textarea data-type=\"application/json\">$json</textarea>";
        echo $html;
        exit;
    }
    public function doChangePassword($old_password, $new_password)
    {
        //调用接口
        $memberModel=D('User/UcenterMember');
        $res=$memberModel->changePassword($old_password,$new_password);
        if($res){
            $this->success('修改密码成功');
        }else{
            $this->error($memberModel->getErrorMessage());
        }
    }
	
	public function dochangesafepass($old_password = null, $new_password = null, $confirm_password = null, $verify = null)
	{
		$profile = $this->get_self_info(is_login());
		$aAccount = $profile['email'];
		$aUid = is_login();
		
		if(empty($new_password)){
			$this->error('请输入新安全码');
		}
		if(empty($confirm_password)){
			$this->error('请再次输入新安全码');
		}
		if(empty($verify)){
			$this->error('请输入验证码');
		}
		if($new_password != $confirm_password){
			$this->error('两次密码不一致，请核对');
		}
		
		$aType = 'email';
		$res = D('Common/Verify')->checkVerify($aAccount, $aType, $verify, $aUid);
        if (!$res) {
           $this->error('验证码错误');
        }
		
		//if($profile['safecode'] != md5($old_password)){
		//	$this->error('旧安全码错误');
		//}
		//$password = D('User/UcenterMember')->where('id='.is_login())->getField('password');
		//if($password != think_ucenter_md5($login_password, UC_AUTH_KEY)){
		//	$this->error('账号登录密码错误');
		//}
		
		$res=D('User/Member')->where('uid = '.is_login())->save(array('safecode' => md5($new_password)));
		if($res){
			clean_query_user_cache(is_login(),'safecode');//删除缓存
            $this->success('修改安全码成功');
        }else{
            $this->error('安全码相同未修改');
        }
		
	}
	
	public function dobindsafepass($old_password, $confirm_password)
	{
		$profile = $this->get_self_info(is_login());
		if(empty($old_password)){
			$this->error('请输入安全码');
		}
		if(empty($confirm_password)){
			$this->error('请再次输入安全码');
		}
		if($old_password != $confirm_password){
			$this->error('两次密码不一致，请核对');
		}
		$res=D('User/Member')->where('uid = '.is_login())->save(array('safecode' => md5($old_password)));
		if($res){
			clean_query_user_cache(is_login(),'safecode');//删除缓存
            $this->success('设置安全码成功');
        }else{
            $this->error('未知错误，设置未成功');
        }
		
	}
    /**
     * @param $sex
     * @return int
     * @auth 
     */
    private function checkSex($sex)
    {
        if ($sex < 0 || $sex > 2) {
            $this->error('性别必须属于男、女、保密。');
            return $sex;
        }
        return $sex;
    }
    /**
     * @param $email
     * @param $email
     * @auth 
     */
    private function checkEmail($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (!preg_match($pattern, $email)) {
            $this->error('邮箱格式错误。');
        }
        $map['email'] = $email;
        $map['id'] = array('neq', get_uid());
        $had = D('User/UcenterMember')->where($map)->count();
        if ($had) {
            $this->error('该邮箱已被人使用。');
        }
    }
    /**
     * saveUsername  修改用户名
     * @author:
     */
    public function saveUsername()
    {
        $aUsername = $cUsername = I('post.username', '', 'op_t');
        if (!check_reg_type('username')) {
            $this->error('用户名选项已关闭！');
        }
        //判断是否登录
        if (!is_login()) {
            $this->error('请登录后操作！');
        }
        //判断提交的用户名是否为空
        if (empty($aUsername)) {
            $this->error('用户名不能为空！');
        }

         //验证用户名是否是字母和数字
        preg_match("/^[a-zA-Z0-9_]{".modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').",".modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')."}$/", $aUsername, $match);
        if (!$match) {
            $this->error('用户名只允许英文字母和数字且长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'位之间！');
        }
        $uid = get_uid();
        $mUcenter = D('User/UcenterMember');
        //判断用户是否已设置用户名
        $username = $mUcenter->where(array('id' => $uid))->getField('username');
        if (empty($username)) {
            //判断修改的用户名是否已存在
            $id = $mUcenter->where(array('username' => $aUsername))->getField('id');
            if ($id) {
                $this->error('该用户名已经存在！');
            } else {
                //修改用户名
                $rs = $mUcenter->where(array('id' => $uid))->save(array('username' => $aUsername));
                if (!$rs) {
                    $this->error('设置失败！');
                }
                $this->success('设置成功！');
            }
        }
        $this->error('用户名已经确定不允许修改！');
    }
	/**
     * checkVerify  验证邮箱验证码
     * @author:
     */
    public function dobindemail()
    {
        $aAccount = I('email', '', 'op_t');
        $aType = 'email';
        $aVerify = I('verify', '', 'op_t');
        $aUid = I('uid', 0, 'intval');
		$profile = $this->get_self_info(is_login());
		
        if (!is_login() || $aUid != is_login()) {
            $this->error('验证失败');
        }
		if(empty($aAccount)){
			$this->error('请输入绑定邮箱');
		}
		if(empty($aVerify)){
			$this->error('请输入邮箱验证码');
		}
        $res = D('Common/Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
           $this->error('绑定失败，验证码错误');
        }
        D('User/UcenterMember')->where(array('id' => $aUid))->save(array('email' => $aAccount, 'email_ver'=> 1));
		clean_query_user_cache(is_login(),'email');//删除缓存
        $this->success('验证成功', U('ucenter/config/email'));
    }
	
	/**
     * changeaccount  解绑邮箱
     * @author: 朝兮夕兮
     */
	public function dounbindemail(){
		//$aAccount = I('email', '', 'op_t');
		$profile = $this->get_self_info(is_login());
		$aAccount = $profile['email'];
        $aType = 'email';
        $aVerify = I('verify', '', 'op_t');
		$aSafeCode = I('safecode','','op_t');
        $aUid = I('uid', 0, 'intval');
        if (!is_login() || $aUid != is_login()) {
            $this->error('账号错误');
        }
        $res = D('Common/Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
           $this->error('验证码错误');
        }
		if(md5($aSafeCode) != $profile['safecode']){
			 $this->error('安全码错误');
		}
		
        D('User/UcenterMember')->where(array('id' => $aUid))->save(array('email' => '', 'email_ver'=> 0));
		clean_query_user_cache(is_login(),'email');//删除缓存
        $this->success('解绑成功', U('ucenter/config/email'));
	}
	/**
     * checkVerify  验证手机验证码
     * @author:
     */
    public function dobindmobile()
    {
        $aAccount = I('mobile', '', 'op_t');
        $aType = 'mobile';
        $aVerify = I('verify', '', 'op_t');
        $aUid = I('uid', 0, 'intval');
		$profile = $this->get_self_info(is_login());
		
        if (!is_login() || $aUid != is_login()) {
            $this->error('账号错误');
        }
		//确认手机号码没有重复
        $user = D('User/UcenterMember')->where(array('mobile' => $mobile, 'id'=>array('neq',is_login())))->find();
        if ($user) {
            $this->error('该手机号码已绑定到另一个账号，不能重复绑定');
        }
		unset($user);
		
        $res = D('Common/Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
           $this->error('验证码错误');
        }
        D('User/UcenterMember')->where(array('id' => $aUid))->save(array('mobile' => $aAccount, 'mobile_ver'=> 1));
		clean_query_user_cache(is_login(),'mobile');//删除缓存
        $this->success('绑定成功', U('ucenter/config/mobile'));
    }
	
	/**
     * changeaccount  解绑手机
     * @author: 朝兮夕兮
     */
	public function dounbindmobile(){
		//$aAccount = I('email', '', 'op_t');
		$profile = $this->get_self_info(is_login());
		$aAccount = $profile['mobile'];
        $aType = 'mobile';
        $aVerify = I('verify', '', 'op_t');
		$aSafeCode = I('safecode','','op_t');
        $aUid = I('uid', 0, 'intval');
        if (!is_login() || $aUid != is_login()) {
            $this->error('账号错误');
        }
        $res = D('Common/Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
           $this->error('验证码错误');
        }
		if(md5($aSafeCode) != $profile['safecode']){
			 $this->error('安全码错误');
		}
		
        D('User/UcenterMember')->where(array('id' => $aUid))->save(array('mobile' => '', 'mobile_ver'=> 0));
		clean_query_user_cache(is_login(),'mobile');//删除缓存
        $this->success('解绑成功', U('ucenter/config/mobile'));
	}

    public function cleanRemember(){
        $uid = is_login();
        if($uid){
            D('user_token')->where('uid=' . $uid)->delete();
            $this->success('清除成功！');
        }
        $this->error('清除失败！');
    }
}