<?php
namespace ucenter\controller;
use think\Controller;
class CashController extends BaseController
{
    public function _initialize()
    {
    	parent::_initialize();
    	$this->_assignSelf(is_login());
    }

    public function index()
    {
		$uid = is_login();
    	$profile = $this->get_self_info($uid);
    	$edu = modC('CASH_LOWEST_LIMIT','0','PAY');
    	$cash_verify = modC('CASH_TAKE_VERIFY_ON',1,'PAY');
    	$cash_take_sms_on = modC('CASH_TAKE_SMS_ON',0,'PAY');
		
    	if(IS_POST){
			$step = I('post.tp','','intval');
			$usdate['name'] = I('post.name','','op_t');
			$purseid = I('post.purseid','','op_t');
			$safecode = I('post.safecode','','op_t');
			$money = I('post.money', 0, 'intval');
			$aUid = I('post.uid', 0, 'intval');
			$vt = I('post.vt',0, 'intval');
			$purse = explode('|',$purseid);
			$pid = $purse[0];
			$pname = $purse[1];
			$pcard = $purse[2];
			$pcardid = $purse[3];
			$uname = $purse[4];
			
			if (!$uid || $aUid != $uid) {
				$this->error(L('_UID_ERROR_'));
			}
			$member = D('member')->where(array('uid'=>$uid,'status'=>1))->find();
			if(!$member){
				$this->error(L('_NO_UID_ERROR_'));
			}
			
			if (empty($vt)) {
				$this->error(L('_SELECT_SCORE_TYPE_'));
			}
			
			$tixian_1 = D('User/UserPayOffer')->where(array('uid'=>$aUid,'status'=>0))->find();
			if($tixian_1 != null){
				$this->success(L('_HAS_ONE_PAY_ORDER_VERIFY_'),U('verify','id='.$tixian_1['id'].'&tp=1'));
			}
			$tixian_2 = D('User/UserPayOffer')->where(array('uid'=>$aUid,'status'=>1))->find();
			if($tixian_2 != null){
				$this->success(L('_HAS_ONE_PAY_ORDER_'),'cash/detail');
			}
			if($money <= 0){
				$this->error(L('_NEED_INPUT_MONEY_'));
			}
			$scoretp = D('User/UserScoreType')->where(array('id'=>$vt,'status'=>1,'cash'=>1))->find();
			if(empty($scoretp)){
				$this->error(L('_SCORE_TYPE_CANT_PAY_'));
			}
			
			if((int)$money < (int)$scoretp['minimum']){
				$this->error(L('_LOW_PAY_LOW_').$scoretp['minimum'].L('_MONEY_'));
			}
			if($safecode == '' || $safecode == null){
				$this->error(L('_INPUT_SAFECODE_'));
			}
			
			$str = $profile[$scoretp['mark']]/$scoretp['exchange'];
			$str = explode('.',$str);
			if((int)$money > (int)$str[0]){
				$this->error(L('_TOTAL_CURRENT_ACCOUNT_').$str[0].L('_MONEY_').L('_AMOUNT_CASH_AVAILABLE_'));
			}
			if(md5($safecode) != $profile['safecode']){
				$this->error(L('_ERROR_SAFECODE_'));
			}
			
			$rs = D('MemberPurse')->where(array('status'=>1,'id'=>$pid))->find();
			if($rs == null){
				$this->error(L('_ERROR_SCORE_TYPE_PAY_'));
			}
			$rsb = D('MemberPurseId')->where(array('status'=>1,'id'=>$pcardid))->find();
			if($rsb == null){
				$this->error(L('_ERROR_UID_SCORE_TYPE_PAY_'));
			}
			
			if((int)$cash_verify == 0){
				$num = $money*$scoretp['exchange'];
				$gold_type = $scoretp['mark'];
				$gold_name = $scoretp['title'];
				$title= '申请提现'.$gold_name.$money.'元';
				$content = '申请提现'.$gold_name.$money.'元到'.$pname.'账号：'.$pcard;
				$r = api('User/sendReward', array($uid,0, 'cash', 'dec',$gold_type ,$num, $title, $content));
				
				$paydata['uid'] = $uid;
				$paydata['username'] = $profile['username'];
				$paydata['action_ip'] = get_client_ip(1);
				$paydata['name'] = $uname;
				$paydata['pursecardno'] = $pcard;
				$paydata['pursecardid'] = $pcardid;
				$paydata['purse_pid'] = $pid;
				$paydata['purse_pname'] = $pname;
				$paydata['pay_offer'] = $money;
				$paydata['pay_gold_coin'] = $money*$scoretp['exchange'];
				$paydata['create_time'] = time();
				$paydata['status'] = 1;
				$paydata['score_type'] = $vt;
				$paydata['remark'] = $scoretp['title'].'提现'.$money.'元';
				$q = D('User/UserPayOffer')->add($paydata);
				if($q){
					clean_query_user_cache($uid, array($scoretp['mark']));
					$this->success('申请提现'.$gold_name.$money.'元成功，我们会尽快给亲处理，请耐心等待！',U('ucenter/cash/detail'));
				}else{
					$this->error(L('_ERROR_PAY_OFF_'));
				}
			}else{
	    		if((int)$cash_take_sms_on == 0){
	    			$mobile = I('post.mobile','','op_t');
	    			$aType = 'mobile';
	    		}else{
	    			$mobile = I('post.email','','op_t');
	    			$aType = 'email';
	    		}
	    		$aVerify = I('post.verify', '', 'intval');
	    		
	    		if((int)$cash_take_sms_on == 0){
	    			if($mobile != $profile['mobile']){
	    				$this->error(L('_ERROR_MOBILE_'));
	    			}
	    		}else{
	    			if($mobile != $profile['email']){
	    				$this->error(L('_ERROR_EMAIL_'));
	    			}
	    		}
	    		
	    		$ress = D('Common/Verify')->checkVerify($mobile, $aType, $aVerify, $aUid);
	    		if (!$ress) {
	    			$this->error(L('_ERROR_VERIFY_'));
	    		}
				
				$num = $money*$scoretp['exchange'];
				$gold_type = $scoretp['mark'];
				$gold_name = $scoretp['title'];
				$title= '申请提现'.$gold_name.$money.'元';
				$content = '申请提现'.$gold_name.$money.'元到'.$pname.'账号：'.$pcard;
				$r = api('User/sendReward', array($uid,0, 'cash', 'dec',$gold_type ,$num, $title, $content));
				
				$paydata['uid'] = $uid;
				$paydata['username'] = $profile['username'];
				$paydata['action_ip'] = get_client_ip(1);
				$paydata['name'] = $uname;
				$paydata['pursecardno'] = $pcard;
				$paydata['pursecardid'] = $pcardid;
				$paydata['purse_pid'] = $pid;
				$paydata['purse_pname'] = $pname;
				$paydata['pay_offer'] = $money;
				$paydata['pay_gold_coin'] = $money*$scoretp['exchange'];
				$paydata['create_time'] = time();
				$paydata['status'] = 1;
				$paydata['score_type'] = $vt;
				$paydata['remark'] = $scoretp['title'].'提现'.$money.'元';
				$q = D('User/UserPayOffer')->add($paydata);
				if($q){
					clean_query_user_cache($uid, array($scoretp['mark']));
					$this->success('申请提现'.$gold_name.$money.'元成功，我们会尽快给亲处理，请耐心等待！',U('ucenter/cash/detail'));
				}else{
					$this->error(L('_ERROR_PAY_OFF_'));
				}
			}
    	}else{
			if($profile['safecode'] == null || empty($profile['safecode'])){
				$this->redirect('Ucenter/System/first');
			}
			if((int)$cash_verify == 1){
				if((int)$cash_take_sms_on == 1){
	    			if($profile['email_ver'] == 0 || $profile['email'] == null || $profile['email'] == ''){
	    				$this->redirect('ucenter/system/first');
	    			}
		    	}else{
	    			if($profile['mobile'] == null || $profile['mobile'] == '' || $profile['mobile_ver'] == 0){
	    				$this->redirect('ucenter/system/first');
	    			}
		    	}
			}
	    	
	    	$rs = D('User/UserPayOffer')->where(array('uid'=>$uid,'status'=>array('in','1,0')))->select();
	    	if($rs){
	    		
	    	}
			$this->setTitle('申请提现');
			$scoreModel = D('User/Score');
			$scores = $scoreModel->getTypeList(array('status'=>1));
			foreach ($scores as &$v) {
				$v['value'] = $scoreModel->getUserScore($uid, $v['id']);
			}
			unset($v);
			$this->assign('scores', $scores);
			$this->assign('cash_verify',$cash_verify);
			$this->assign('cash_take_sms_on',$cash_take_sms_on);
			
			$ca = modC('CASH_TAKE_MODE','','PAY');
			$rs = D('MemberPurseId')->where(array('uid'=>$uid,'status'=>1,'pid'=>array('in',$ca)))->select();
			if(empty($rs)){
				$this->redirect('Ucenter/System/first');
			}else{
				foreach($rs as &$v){
					$v['pursename'] = D('MemberPurse')->where(array('id'=>$v['pid'],'status'=>1))->getfield('pursename');
				}
				$this->assign('purse_list', $rs);
				$this->display();
			}
    	}
    }
    
    public function verify($id = 0,$tp = 1){
    	$edu = modC('CASH_LOWEST_LIMIT','0','PAY');
		$uid = is_login();
    	$profile = $this->get_self_info($uid);
    	$cash_verify = modC('CASH_TAKE_VERIFY_ON',1,'PAY');
    	$cash_take_sms_on = modC('CASH_TAKE_SMS_ON',0,'PAY');
    	
    	if((int)$cash_verify == 0){
    		$rs = D('User/UserPayOffer')->where(array('id'=>$id,'uid'=>$uid,'status'=>0))->find();
    		if($rs){
				$scoretp = D('User/UserScoreType')->where(array('id'=>$rs['score_type'],'status'=>1,'cash'=>1))->find();
				if(empty($scoretp)){
					$this->error(L('_SCORE_TYPE_CANT_PAY_'));
				}
				$str = $profile[$scoretp['mark']]/$scoretp['exchange'];
				$str = explode('.',$str);
				$will_gold = (int)$rs['pay_gold_coin']/$scoretp['exchange'];
				if((int)$will_gold > (int)$str[0]){
					D('User/UserPayOffer')->where(array('id'=>$id,'uid'=>$uid,'status'=>0))->delete();
					$this->error('当前账号总额：'.$str[0].'元，提现超出可提现金额，请重新填写金额',U('ucenter/cash/index'));
				}
				$num = $rs['pay_gold_coin'];
				$gold_type = $scoretp['mark'];
				$gold_name = $scoretp['title'];
				$title= '申请提现'.$gold_name.$rs['pay_offer'].'元';
				$content = '申请提现'.$gold_name.$rs['pay_offer'].'元到'.$rs['purse_pname'].'账号：'.$rs['pursecardno'];
				$r = api('User/sendReward', array($uid,0, 'cash', 'dec',$gold_type ,$num, $title, $content));
				//$a->sendPayLog($uid,0 , 'dec', $gold_type, $num, $title, $content);
				$rsq = D('User/UserPayOffer')->where(array('uid'=>$uid,'id'=>$id,'status'=>0))->setField('status',1);
				if($rsq){
					clean_query_user_cache($uid, array($scoretp['mark']));
					$this->success('申请提现'.$gold_name.$rs['pay_offer'].'元成功，我们会尽快给亲处理，请耐心等待！',U('ucenter/cash/index'));
				}else{
					$this->error(L('_ERROR_NON_'),U('ucenter/cash/index'));
				}
    		}else{
    			$this->error(L('_ERROR_ORDER_'),U('ucenter/cash/index'));
    		}
    	}else{
	    	if(IS_POST){
	    		if((int)$cash_take_sms_on == 0){
	    			$mobile = I('post.phone','','op_t');
	    			$aType = 'mobile';
	    		}else{
	    			$mobile = I('post.email','','op_t');
	    			$aType = 'email';
	    		}
	    		$aVerify = I('post.verify', '', 'intval');
	    		$aUid = I('post.uid', 0, 'intval');
	    		if (!$uid || $aUid != $uid) {
	    			$this->error(L('_UID_ERROR_'));
	    		}
	    		
	    		$member = D('member')->where(array('uid'=>$uid,'status'=>1))->find();
	    		if(!$member){
	    			$this->error(L('_NO_UID_ERROR_'));
	    		}
	    		if((int)$cash_take_sms_on == 0){
	    			if($mobile != $profile['mobile']){
	    				$this->error(L('_ERROR_MOBILE_'));
	    			}
	    		}else{
	    			if($mobile != $profile['email']){
	    				$this->error(L('_ERROR_EMAIL_'));
	    			}
	    		}
	    		
	    		$ress = D('Common/Verify')->checkVerify($mobile, $aType, $aVerify, $aUid);
	    		if (!$ress) {
	    			$this->error(L('_ERROR_VERIFY_'));
	    		}
	    		$rs = D('User/UserPayOffer')->where(array('id'=>$id,'uid'=>$uid,'status'=>0))->find();
	    		if($rs){
	    			$scoretp = D('User/UserScoreType')->where(array('id'=>$rs['score_type'],'status'=>1,'cash'=>1))->find();
					if(empty($scoretp)){
						$this->error(L('_SCORE_TYPE_CANT_PAY_'));
					}
					$str = $profile[$scoretp['mark']]/$scoretp['exchange'];
					$str = explode('.',$str);
					$will_gold = (int)$rs['pay_gold_coin']/$scoretp['exchange'];
					if((int)$will_gold > (int)$str[0]){
						D('User/UserPayOffer')->where(array('id'=>$id,'uid'=>$uid,'status'=>0))->delete();
						$this->error('当前账号总额：'.$str[0].'元，提现超出可提现金额，请重新填写金额',U('ucenter/cash/index'));
					}
					$num = $rs['pay_gold_coin'];
					$gold_type = $scoretp['mark'];
					$gold_name = $scoretp['title'];
					$title= '申请提现'.$gold_name.$rs['pay_offer'].'元';
					$content = '申请提现'.$gold_name.$rs['pay_offer'].'元到'.$rs['purse_pname'].'账号：'.$rs['pursecardno'];
					$r = api('User/sendReward', array($uid,0, 'cash', 'dec',$gold_type ,$num, $title, $content));
					//$a->sendPayLog($uid,0 , 'dec', $gold_type, $num, $title, $content);
					$rsq = D('User/UserPayOffer')->where(array('uid'=>$uid,'id'=>$id,'status'=>0))->setField('status',1);
					if($rsq){
						clean_query_user_cache($uid, array($scoretp['mark']));
						$this->success('申请提现'.$gold_name.$rs['pay_offer'].'元成功，我们会尽快给亲处理，请耐心等待！',U('ucenter/cash/index'));
					}else{
						$this->error(L('_ERROR_NON_'),U('ucenter/cash/index'));
					}
	    		}else{
	    			$this->error(L('_ERROR_ORDER_'),U('ucenter/cash/index'));
	    		}
	    	}else{
	    		
	    		if((int)$cash_take_sms_on == 1){
	    			if($profile['email_ver'] == 0 || $profile['email'] == null || $profile['email'] == ''){
	    				$this->redirect('ucenter/system/first');
	    			}
	    		}else{
	    			if($profile['mobile'] == null || $profile['mobile'] == '' || $profile['mobile_ver'] == 0){
	    				$this->redirect('ucenter/system/first');
	    			}
	    		}
	    		
	    		if($id === 0){
	    			$rs = D('User/UserPayOffer')->where(array('uid'=>$uid,'status'=>0))->find();
	    		}else{
	    			$rs = D('User/UserPayOffer')->where(array('id'=>$id,'status'=>0))->find();
	    		}
		    	if($rs == null){
		    		$this->redirect('Ucenter/cash/index');
		    	}
		    	
		    	if($rs['uid'] != $uid){
		    		$this->error(L('_UID_ERROR_'));
		    	}
				$scoretp = D('User/UserScoreType')->where(array('id'=>$rs['score_type'],'status'=>1,'cash'=>1))->find();
					if(empty($scoretp)){
						$this->error(L('_SCORE_TYPE_CANT_PAY_'));
					}
				$str = $profile[$scoretp['mark']];
		    	$str = explode('.',$str);
		    	if((int)$rs['pay_gold_coin'] > (int)$str[0]){
		    		D('User/UserPayOffer')->where(array('id'=>$id,'uid'=>$uid,'status'=>0))->delete();
		    		$this->error('当前账号总额：'.$str[0].'元，提现超出可提现金额，请重新申请提现');
		    	}
				
				$scoreModel = D('User/Score');
				$scores = $scoreModel->getTypeList(array('status'=>1));
				foreach ($scores as &$v) {
					$v['value'] = $scoreModel->getUserScore($uid, $v['id']);
				}
				unset($v);
				$this->assign('scores', $scores);
		    	$this->assign('cash_take_sms_on',$cash_take_sms_on);
		    	$this->assign('order_list',$rs);
		    	$this->display();
	    	}
    	}
    }
    
    public function type(){
    	$uid = is_login();
    	$purse = D('MemberPurse')->where('status = 1')->select();
    	foreach($purse as &$v){
    		$v['user'] = D('MemberPurseId')->where('uid='.$uid.' and pid='.$v['id'])->find();
    	}
		$this->setTitle('钱包设置 - 提现方式');
    	$this->assign('purse_list',$purse);
    	$this->display();
    }
    
    public function detail(){
		$uid = is_login();
		$this->setTitle('提现记录 - 提现明细');
    	$list = D('User/UserPayOffer')->where(array('uid'=>$uid))->order('id desc')->select();
    	$this->assign('order_list',$list);
    	$this->display();
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
    
}