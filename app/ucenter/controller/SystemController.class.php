<?php
namespace ucenter\controller;
use think\Controller;
class SystemController extends BaseController
{
    public function _initialize()
    {
    	parent::_initialize();
    	$this->_assignSelf(is_login());
    }

    public function index($uid = null)
    {
        $this->setTitle('安全设置');
		$this->_setTab('safe');
        $this->display('basic');
    }
    public function bindSafeCode()
    {
        $profile = $this->get_self_info(is_login());
        //if($profile['qq'] == null || $profile['idcard'] == null || $profile['name'] == null){
        if($profile['qq'] == null || $profile['name'] == null){
        	$this->redirect('first');
        }elseif($profile['mobile'] != null || $profile['mobile'] != ''){
        	$this->redirect('third');
        }elseif($profile['email_ver'] == 1 && $profile['email'] != null){
        	$this->redirect('four');
        }elseif(!$profile['safecode']) {
        	$this->redirect('five');
        }else{
        	$this->redirect('first');
        }
    }
    
    public function first(){
        $uid = is_login();
    	if(IS_POST){
    		$usdate = array();
    		$step = I('post.step','','htmlspecialchars');
    		switch($step){
    			case 1:
    				$usdate['name'] = I('post.name','','op_t');
    				//$usdate['idcard'] = I('post.idcode','','op_t');
    				$usdate['qq'] = I('post.qq','','op_t');
    				if(empty($usdate['name']) || $usdate['name'] == ''){
    					$this->error('请填写姓名！');
    				}
    				if(strlen($usdate['name']) <= 2){
    					$this->error('请正确填写姓名！');;
    				}
    				$res = D('User/Member')->where(array('name'=> $usdate['name'], 'uid'=>array('neq',$uid)))->find();
    				if($res){
    					$this->error('亲填写的姓名已被使用！');
    				}
    				//if(empty($usdate['idcard']) || $usdate['idcard'] == ''){
    				//	$this->error('请填写身份证号码！');
    				//}
    				//$check = $this->checkIdCard($usdate['idcard']);
    				//if(!$check){
    				//	$this->error('身份证号码错误，请重试！');
    				//}
    				
    				//$res = D('User/Member')->where(array('idcard'=> $usdate['idcard'], 'uid'=>array('neq',is_login())))->find();
    				//if($res){
    				//	$this->error('亲填写的身份证号码已被使用！');
    				//}
    				if(empty($usdate['qq']) || $usdate['qq'] == '' || !is_numeric($usdate['qq'])){
    					$this->error('请填写QQ！');
    				}
    				$res = D('User/Member')->where(array('qq'=> $usdate['qq'], 'uid'=>array('neq',$uid)))->find();
    				if($res){
    					$this->error('亲填写的QQ已被使用！');
    				}
    				
    				$rs = D('User/Member')->where(array('uid'=>$uid,'status'=>1))->save($usdate);
    				unset($usdate);
    				if($rs){
    					$this->success('提交成功，继续下一步！','second');
    				}else{
    					$this->error('保存失败！');
    				}
    				break;
    			default:
    				$this->error('步骤错误！');
    				break;
    		}
    	}else{
    		$cash_sms_on = modC('CASH_SMS_ON','1','PAY');
    		$cash_email_on = modC('CASH_EMAIL_ON','1','PAY');
    		//确认用户已经填写的安全码
        	$profile = $this->get_self_info(is_login());
            $this->assign('cash_sms_on',$cash_sms_on);
    		$this->assign('cash_email_on',$cash_email_on);
    		$this->display();
    	}
    }
    
    public function second(){
    	if(IS_POST){
    		$usdate = array();
    		$step = I('post.step','','htmlspecialchars');
    		switch($step){
    			case 2:
    				$usdate['mobile'] = I('post.phone','','op_t');
    				$aType = I('post.type', '', 'op_t');
    				$aVerify = I('post.verify', '', 'intval');
    				$aUid = I('post.uid', 0, 'intval');
    				
    				if (!is_login() || $aUid != is_login()) {
    					$this->error('验证失败');
    				}
    				if($aVerify == null || $aVerify == ''){
    					$this->error('请输入验证码！');
    				}
    				
    				if(empty($usdate['mobile']) || $usdate['mobile'] == ''){
    					$this->error('请填写手机号码！');
    				}
    				if(strlen($usdate['mobile']) <= 10 && strlen($usdate['mobile']) >= 18){
    					$this->error('手机号码长度不正确！');
    				}
    				$res = D('UcenterMember')->where(array('mobile'=> $usdate['mobile'],'id'=>array('neq',is_login())))->find();
    				if($res){
    					$this->error('亲填写的手机号码已被使用！');
    				}
    				
    				$aType = $aType == 'mobile' ? 'mobile' : 'email';
    				$ress = D('Common/Verify')->checkVerify($usdate['mobile'], $aType, $aVerify, $aUid);
    				if (!$ress) {
    					$this->error('验证失败');
    				}
    				$usdate['mobile_ver'] = 1;
    				$rs = D('User/UcenterMember')->where(array('id'=>is_login(),'status'=>1))->save($usdate);
    				if($rs){
    					unset($usdate);
    					$this->success('提交成功，继续下一步！','third');
    				}else{
    					unset($usdate);
    					$this->error('保存失败！');
    				}
    				break;
    			default:
    				$this->error('步骤错误！');
    				break;
    		}
    	}else{
    		$cash_sms_on = modC('cash_sms_on','1','pay');
    		$cash_email_on = modC('cash_email_on','1','pay');
    		
    		//确认用户已经填写的安全码
    		$profile = $this->get_self_info(is_login());
    		if(intval($cash_sms_on) == 1 || intval($cash_sms_on) ){
    		    $this->assign('cash_sms_on',$cash_sms_on);
    		    $this->assign('cash_email_on',$cash_email_on);
    		    $this->display();
    		}elseif(intval($cash_sms_on) == 0 || !intval($cash_sms_on) ){
    		    $this->redirect('third');
    		}
    	}
    }
    
    public function third(){
    	if(IS_POST){
    		$usdate = array();
    		$step = I('post.step','','htmlspecialchars');
    		switch($step){
    			case 3:
    				$usdate['email'] = I('post.email','','op_t');
    				$aType = I('post.type', '', 'op_t');
    				$aVerify = I('post.verify', '', 'op_t');
    				$aUid = I('post.uid', 0, 'intval');
    				if (!is_login() || $aUid != is_login()) {
    					$this->error('验证失败');
    				}
    				if($aVerify == null || $aVerify == ''){
    					$this->error('请输入验证码！');
    				}
    				if(empty($usdate['email']) || $usdate['email'] == ''){
    					$this->error('请填写邮箱号码！');
    				}
    				if(strlen($usdate['mobile']) <= 10 && strlen($usdate['mobile']) >= 18){
    					$this->error('手机号码长度不正确！');
    				}
    				$res = D('UcenterMember')->where(array('email'=> $usdate['email'],'id'=>array('neq',is_login())))->find();
    				if($res){
    					$this->error('亲填写的邮箱已被使用！');
    				}
    	
    				$aType = $aType == 'email' ? 'email' : 'mobile';
    				$ress = D('Common/Verify')->checkVerify($usdate['email'], $aType, $aVerify, $aUid);
    				if (!$ress) {
    					$this->error('验证失败');
    				}
    				$usdate['email_ver'] = 1;
    				$rs = D('User/UcenterMember')->where(array('id'=>is_login(),'status'=>1))->save($usdate);
    				if($rs){
    					unset($usdate);
    					$this->success('提交成功，继续下一步！','four');
    				}else{
    					unset($usdate);
    					$this->error('保存失败！');
    				}
    				break;
    			default:
    				$this->error('步骤错误！');
    				break;
    		}
    	}else{
			$cash_sms_on = modC('CASH_SMS_ON','1','PAY');
    		$cash_email_on = modC('CASH_EMAIL_ON','0','pay');
			//确认用户已经填写的安全码
    		$profile = $this->get_self_info(is_login());
    		if(intval($cash_email_on) == 1 || intval($cash_email_on) ){
    		    $this->assign('cash_sms_on',$cash_sms_on);
    		    $this->assign('cash_email_on',$cash_email_on);
    		    $this->display();
    		}elseif(intval($cash_email_on) == 0 || !intval($cash_email_on) ){
    		    $this->redirect('four');
    		}
       	}
    }
    
    public function four(){
		$profile = $this->get_self_info(is_login());
    	if(IS_POST){
    		$usdate = array();
    		$step = I('post.step','','htmlspecialchars');
    		switch($step){
    			case 4:
    				$usdate['safecode'] = I('post.jy_pwd','','op_t');
					$mobile = $profile['email'];
	    			$aType = 'email';
					$aVerify = I('post.verify', '', 'intval');
    				$aUid = I('post.uid', 0, 'intval');
    				
    				if (!is_login() || $aUid != is_login()) {
    					$this->error('验证失败');
    				}
    				
    				if(empty($usdate['safecode']) || $usdate['safecode'] == ''){
    					$this->error('请填写交易安全码！');
    				}
    				if(strlen($usdate['safecode']) <= 5 && strlen($usdate['safecode']) >= 17){
    					$this->error('交易安全码超出范围！');
    				}
					
					$ress = D('Common/Verify')->checkVerify($mobile, $aType, $aVerify, $aUid);
					if (!$ress) {
						$this->error('验证码错误');
					}
					
					$res=D('User/Member')->where('uid = '.is_login())->save(array('safecode' => md5($usdate['safecode'])));
					if($res){
						clean_query_user_cache(is_login(),'safecode');//删除缓存
						$this->success('修改安全码成功');
					}else{
						$this->error('安全码相同未修改');
					}
					
    				break;
    			default:
    				$this->error('步骤错误！');
    				break;
    		}
    	}else{
    		$cash_sms_on = modC('CASH_SMS_ON','1','PAY');
    		$cash_email_on = modC('CASH_EMAIL_ON','1','PAY');
    		//确认用户已经填写的安全码
    		
    		$this->assign('cash_sms_on',$cash_sms_on);
    		$this->assign('cash_email_on',$cash_email_on);
    		$this->display();
    	}
    }
    
    public function five(){
    	if(IS_POST){
    		$usdate = array();
    		$step = I('post.step','','intval');
    		switch($step){
    			case 5:
    				$usdate['name'] = I('post.name','','op_t');
    				$usdate['pursecardno'] = I('post.account','','op_t');
    				$usdate['account2'] = I('post.account2','','op_t');
    				$usdate['banksit'] = I('post.banksub','','op_t');
    				$usdate['pid'] = I('post.purseid', 0, 'intval');
    				$aUid = I('post.uid', 0, 'intval');
    					
    				if (!is_login() || $aUid != is_login()) {
    					$this->error('验证失败');
    				}
    				if($usdate['pid'] == 0){
    					$this->error('绑定失败');
    				}
    	
    				if(empty($usdate['name']) || $usdate['name'] == ''){
    					$this->error('请填写姓名！');
    				}
    				if(strlen($usdate['pursecardno']) <= 5 && strlen($usdate['pursecardno']) >= 20){
    					$this->error('账号格式不正确，请核对！');
    				}
    				if(strlen($usdate['account2']) <= 5 && strlen($usdate['account2']) >= 20){
    					$this->error('账号格式不正确，请核对！');
    				}
    				if($usdate['pursecardno'] != $usdate['account2']){
    					$this->error('两次输入不一致，请重新输入！');
    				}
    				
    				$res = D('MemberPurseId')->where(array('uid'=> $aUid,'pid'=>$usdate['pid']))->find();
    	
    				if($res != null){
						$usdate['safecode'] = I('post.safecode','','op_t');
    				
						if(empty($usdate['safecode']) || $usdate['safecode'] == ''){
							$this->error('请填写交易安全码！');
						}
						$profile = $this->get_self_info(is_login());
						if(md5($usdate['safecode']) != $profile['safecode']){
							$this->error('交易安全码错误，无法修改！');
						}
						$usdate['update_time'] = Time();
						$r = D('MemberPurseId')->where(array('uid'=> $aUid,'pid'=>$usdate['pid']))->save($usdate);
						if($r){
    						unset($usdate);
    						$this->success('恭喜，修改成功');
    					}else{
    						unset($usdate);
    						$this->error('修改失败，请重试');
    					}
    				}else{
    					$usdate['uid'] = $aUid;
    					$usdate['create_time'] = Time();
    					$usdate['status'] = 1;
    					$r = D('MemberPurseId')->add($usdate);
    					if($r){
    						unset($usdate);
    						$this->success('恭喜完成所有操作，即将跳转到提现页面',U('ucenter/cash/index'));
    					}else{
    						unset($usdate);
    						$this->error('绑定失败，请重试');
    					}
    				}
    				break;
    			default:
    				$this->error('步骤错误！');
    				break;
    		}
    	}else{
    		$cash_sms_on = modC('CASH_SMS_ON','1','PAY');
    		$cash_email_on = modC('CASH_EMAIL_ON','1','PAY');
    		//确认用户已经填写的安全码
    		$profile = $this->get_self_info(is_login());
    			
    		$purseid = D('MemberPurseId')->where('uid='.is_login().' and status = 1')->select();
    	
    		$this->assign('cash_sms_on',$cash_sms_on);
    		$this->assign('cash_email_on',$cash_email_on);
    		$ca = modC('CASH_TAKE_MODE','','PAY');
    		$cc = D('MemberPurse')->where(array('status'=>1,'id'=>array('in',$ca)))->select();
    		$purse_zhifubao = D('MemberPurse')->where(array('status'=>1,'pursename'=>array('like','%支付宝%')))->find();
			$purse_zhifubao_data = D('MemberPurseId')->where('uid='.is_login().' and status = 1 and pid = '.$purse_zhifubao['id'])->find();
			
			
    		$purse_caifutong = D('MemberPurse')->where(array('status'=>1,'pursename'=>array('like','%财付通%')))->find();
			$purse_caifutong_data = D('MemberPurseId')->where('uid='.is_login().' and status = 1 and pid = '.$purse_caifutong['id'])->find();
			
    		$purse_yinhang = D('MemberPurse')->where(array('status'=>1,'pursename'=>array('like','%银行%')))->select();
			
    		$purse_first = D('MemberPurse')->where(array('status'=>1))->order('id asc')->select();
    		$this->assign('purse_offer', $cc);
    		$this->assign('purse_first', reset($purse_first));
    		$this->assign('purse_zhifubao', $purse_zhifubao);
			$this->assign('purse_zhifubao_data', $purse_zhifubao_data);
			
    		$this->assign('purse_caifutong', $purse_caifutong);
    		$this->assign('purse_caifutong_data', $purse_caifutong_data);
			
    		$this->assign('purse_yinhang', $purse_yinhang);
    		$this->display();
    	}
    	
    }

    private function checkIdCard($idcard){
		// 只能是18位
		if(strlen($idcard)!=18){
			return false;
		}
		// 取出本体码
		$idcard_base = substr($idcard, 0, 17);
		// 取出校验码
		$verify_code = substr($idcard, 17, 1);
		// 加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		// 校验码对应值
		$verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		// 根据前17位计算校验码
		$total = 0;
		for($i=0; $i<17; $i++){
			$total += substr($idcard_base, $i, 1)*$factor[$i];
		}
		// 取模
		$mod = $total % 11;
		// 比较校验码
		if($verify_code == $verify_code_list[$mod]){
			return true;
		}else{
			return false;
		}
	}

    private function dobindSafeCode($safecode, $loginpwd){
    	//调用接口
    	$result = callApi('User/bindSafeCode', array($loginpwd, $safecode));
    	return $result;
    	//显示成功信息
    	//$this->success($result['message']);
    }

    private function iframeReturn($result)
    {
        $json = json_encode($result);
        $json = htmlspecialchars($json);
        $html = "<textarea data-type=\"application/json\">$json</textarea>";
        echo $html;
        exit;
    }

    public function unbookmark($favorite_id)
    {
    	//调用API取消收藏
    	$result = callApi('User/deleteFavorite', array($favorite_id));
    	$this->ensureApiSuccess($result);
    	//返回结果
    	$this->success($result['message']);
    }

}