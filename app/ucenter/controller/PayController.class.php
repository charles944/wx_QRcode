<?php
//充值控制器
namespace ucenter\controller;
use think\Controller;
class PayController extends BaseController
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

		$t = I('get.t','','text');
		$param = json_decode(urldecode(base64_decode($t)),true);

		$chargeon = modC('CHARGE_ON','','PAY');
		$charge_hook = modC('CHARGE_ADDONS_HOOK','none','PAY');
		if(empty($chargeon)){
			$this->error('未开启充值');
		}else{
			if(empty($charge_hook)){
				$this->setTitle('我要充值');
				$scoreModel = D('User/Score');
				$scores = $scoreModel->getTypeList(array('status'=>1));
				foreach ($scores as &$v) {
					$v['value'] = $scoreModel->getUserScore($uid, $v['id']);
				}
				unset($v);
				$this->assign('scores', $scores);
				$this->display();
			}else{
				$charge_hook =  check_sms_hook_is_exist($charge_hook);
				if($charge_hook == 'none'){
					$this->error("管理员还未配置充值服务商信息，请联系管理");
				}
				$name = get_addon_class($charge_hook);
				$class = new $name();
				return $class->index($param);
			}
		}
    }

    public function detail(){
    	$uid = is_login();
    	$profile = $this->get_self_info($uid);

		$chargeon = modC('CHARGE_ON','','PAY');
		$charge_hook = modC('CHARGE_ADDONS_HOOK','none','PAY');
		if(empty($chargeon)){
			$this->error('未开启充值');
		}else{
			if(empty($charge_hook)){
				$this->setTitle('充值记录');
				$scoreModel = D('User/Score');
				$scores = $scoreModel->getTypeList(array('status'=>1));
				foreach ($scores as &$v) {
					$v['value'] = $scoreModel->getUserScore($uid, $v['id']);
				}
				unset($v);
				$this->assign('scores', $scores);
				$this->display();
			}else{
				$charge_hook =  check_sms_hook_is_exist($charge_hook);
				if($charge_hook == 'none'){
					$this->error("管理员还未配置充值服务商信息，请联系管理");
				}
				$name = get_addon_class($charge_hook);
				$class = new $name();
				return $class->detail();
			}
		}
    }

	public function usergroup(){
		$uid = is_login();
		$group_id=get_login_group();//当前登录角色
		$groupModel=D('AuthGroup');
		$userGroupModel=D('AuthGroupAccess');
		$already_group_list=$userGroupModel->where(array('uid'=>$uid))->field('group_id,status')->select();
		$already_group_ids=array_column($already_group_list,'group_id');
		$already_group_list=array_combine($already_group_ids,$already_group_list);
		$map_already_groups['id']=array('in',$already_group_ids);
		$map_already_groups['status']=1;
		$already_groups=$groupModel->where($map_already_groups)->order('sort asc')->select();
		$already_group_ids=array_unique(array_column($already_groups,'group_id'));
		foreach($already_groups as &$val){
			$val['user_status']=$already_group_list[$val['id']]['status']!=2?($already_group_list[$val['id']]['status']==1)?'<span style="color: green;">已审核</span>':'<span style="color: #ff0000;">已禁用<span style="color: 333">(如有疑问，请联系管理员)</span></span>':'<span style="color: #0003FF;">正在审核</span>';;
			$val['can_login']=$val['id']==$group_id?0:1;
			$val['user_group_status']=$already_group_list[$val['id']]['status'];
		}
		unset($val);
		$already_group_ids=array_diff($already_group_ids,array(0));//去除无分组角色组
		if(count($already_group_ids)){
			$map_can_have_groups['group_id']=array('not in',$already_group_ids);//同组内的角色不显示
		}
		$map_can_have_groups['id']=array('not in',$already_group_ids);//去除已有角色
		$map_can_have_groups['canlevel']=1;//前台可显示
		$map_can_have_groups['status']=1;
		$can_have_groups=$groupModel->where($map_can_have_groups)->order('sort asc')->select();//可持有角色
	   // $register_type=modC('REGISTER_TYPE','normal','UserConfig');
		//$register_type=explode(',',$register_type);
		//if(in_array('invite',$register_type)){//开启邀请注册
			/* $map_can_have_groups['invite']=1;
			$can_up_roles=$groupModel->where($map_can_have_groups)->order('sort asc')->select();//可升级角色
			$this->assign('can_up_roles',$can_up_roles); */
		// }
		$show_group=query_user(array('show_group'),$uid);
		$this->assign('show_group',$show_group['show_group']);
		$this->assign('already_groups',$already_groups);
		$this->assign('can_have_groups',$can_have_groups);
		$this->display();
	}
}