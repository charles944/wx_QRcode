<?php

namespace admin\controller;

use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminConfigBuilder;

/**
 * 后台身份控制器
 * Class RoleController
 * @package Admin\Controller
 *
 */
class RoleController extends AdminController
{
    protected $roleModel;
    protected $userRoleModel;
    protected $roleConfigModel;
    protected $roleGroupModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->roleModel = D("Admin/Role");
        $this->userRoleModel = D('UserRole');
        $this->roleConfigModel = D('RoleConfig');
        $this->roleGroupModel = D('RoleGroup');
    }

    //身份基本信息及配置 start

    public function index($page = 1, $r = 20)
    {
        $map['status'] = array('egt', 0);
        list($roleList,$totalCount) = $this->roleModel->selectPageByMap($map, $page, $r, 'sort asc');
        $map_group['id'] = array('in', array_column($roleList, 'group_id'));

        $group = $this->roleGroupModel->where($map_group)->field('id,title')->select();
        $group = array_combine(array_column($group, 'id'), $group);

        $authGroupList = M('AuthGroup')->where(array('status' => 1))->field('id,title')->select();
        $authGroupList = array_combine(array_column($authGroupList, 'id'), array_column($authGroupList, 'title'));
        foreach ($roleList as &$val) {
            $user_groups = explode(',', $val['user_groups']);
            $val['group'] = $group[$val['group_id']]['title'];
            foreach ($user_groups as &$vl) {
                $vl = $authGroupList[$vl];
            }
            unset($vl);
            $val['user_groups'] = implode(',', $user_groups);
        }
        unset($val);
        $builder = new AdminListBuilder;
        $builder->meta_title = "身份列表";
        $builder->title("身份列表");
        $builder->buttonNew(U('Role/editRole'))
        ->setStatusUrl(U('setStatus'))
        ->buttonEnable()
        ->buttonDisable()
        ->button('删除', array('class' => 'layui-btn layui-btn-xs fbutton ajax-post', 'url' => U('setStatus', array('status' => -1)), 'target-form' => 'ids'))
        ->buttonSort(U('sort'));
        $builder->keyId()
			->keyText('sort', '等级')
            ->keyText('title', '身份名')
            ->keyText('name', '身份标识')
            ->keyText('group', '所属身份分组')
            ->keyText('user_groups', '默认用户组')
            ->keyText('levelmod', '升级方式')
            ->keyYesNo('audit', '是否审核')
			->keyText('day','有效天数')
            ->keyStatus()
            ->keyDoActionEdit('Role/editRole?id=###')
            ->keyDoAction('Role/configScore?id=###', '默认信息配置')
            ->data($roleList)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 编辑身份
     * 
     */
    public function editRole()
    {
        $aId = I('id', 0, 'intval');
        $is_edit = $aId ? 1 : 0;
        $title = $is_edit ? "编辑身份" : "新增身份";
        if (IS_POST) {
            $data['name'] = I('post.name', '', 'op_t');
            $data['title'] = I('post.title', '', 'op_t');
            $data['description'] = I('post.description', '', 'op_t');
            $data['group_id'] = I('post.group_id', 0, 'intval');
            $data['status'] = I('post.status', 1, 'intval');
            $data['user_groups'] = I('post.user_groups');
			$data['levelmod'] = I('post.levelmod', '', 'text');
			$data['buyvalue'] = I('post.buyvalue', 0, 'intval');
			$data['audit'] = I('post.audit', 0, 'intval');
			$data['canlevel'] = I('post.canlevel', 0, 'intval');
			$data['sort'] = I('post.sort', 0, 'intval');
			$data['levelup_number'] = I("post.levelup_number",0,'intval');
			$data['levelup_mark'] = I("post.levelup_mark",'','text');
			$data['day'] = I('post.day',0,'intval');
            if ($data['user_groups'] != '') {
                $data['user_groups'] = implode(',', $data['user_groups']);
            }
            if ($is_edit) {
                $data['id'] = $aId;
                $result = $this->roleModel->update($data);
            } else {
                $result = $this->roleModel->insert($data);
            }
            if ($result) {
                $this->success($title . "成功", U('Role/index'));
            } else {
                $error_info = $this->roleModel->getError();
                $this->error($title . "失败！" . $error_info);
            }
        } else {
            $data['status'] = 1;
            $data['levelmod'] = 'close';
            $data['audit'] = 0;
			
            if ($is_edit) {
                $data = $this->roleModel->getByMap(array('id' => $aId));
                $data['user_groups']=explode(',',$data['user_groups']);
            }

            $authGroupList = M('AuthGroup')->where(array('status' => 1))->field('id,title')->select(); //用户组列表
			
			$field_score = D('User/Score')->getTypeList(array('status' => 1));

			foreach($field_score as &$v){
				$score_arr[$v['mark']]=$v['title'];
			};

            $group = D('RoleGroup')->field('id,title')->select();

            $group = array_combine(array_column($group, 'id'), array_column($group, 'title'));
            if (!$group) {
                $group = array(0 => '无分组');
            } else {
                $group = array_merge(array(0 => '无分组'), $group);
            }
            $builder = new AdminConfigBuilder;
            $builder->meta_title = $title;
            $builder->title($title)
                ->keyId()
                ->keyText('title', '身份名', '不能重复')
                ->keyText('name', '英文标识', '由英文字母、下划线组成，且不能重复')
                ->keyTextArea('description', '描述')
                ->keySelect('group_id', '所属分组', '', $group)
                ->keyChosen('user_groups', '默认用户组', '用户注册后的默认所在用户组,多选', $authGroupList)
				->keyRadio('levelmod', '升级方式', '默认为不可升级', array('buy'=> "购买升级", 'invite'=> "邀请升级", 'authen'=>"认证升级",'levelup'=>"满足条件升级", 'close'=>"不可升级"))
				->keyText('buyvalue', '购买花费', '单位：元，升级此身份需要花费多少RMB，0为不需要花费')
				->keyMultiInput('levelup_number|levelup_mark','满足条件','满足以上条件可升级',array(array('type'=>'text','style'=>''),array('type'=>'select','opt'=>$score_arr,'style'=>'')))
				->keyInteger('day','有效天数','天，0表示无期限')
                ->keyRadio('audit', '需要审核', '默认为关闭，开启后，用户审核后才能拥有该身份', array(1 => "开启", 0 => "关闭"))
				->keyRadio('canlevel', '前台显示', '默认为显示，开启前台可点击升级', array(1 => "显示", 0 => "不显示"))
				->keyInteger('sort','显示&权重排序','值越大，身份等级越高越靠后')
                ->keyStatus()
                ->data($data)
                ->buttonSubmit(U('editRole'))
                ->buttonBack()
                ->display();
        }
    }

    /**
     * 对身份进行排序
     * 
     */
    public function sort($ids = null)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder;
            $builder->doSort('Role', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = $this->roleModel->selectByMap($map, 'sort asc', 'id,title,sort');
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder;
            $builder->meta_title = '身份排序';
            $builder->data($list);
            $builder->buttonSubmit(U('sort'))->buttonBack();
            $builder->display();
        }
    }

    /**
     * 身份状态设置
     * @param mixed|string $ids
     * @param $status
     * 
     */
    public function setStatus($ids, $status)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        if(in_array(1,$ids)){
            $this->error('id为 1 的身份是系统默认身份，不能被禁用或删除！');
        }
        if ($status == 1) {
            $builder = new AdminListBuilder;
            $builder->doSetStatus('Role', $ids, $status);
        } else if ($status == 0) {
            $result = $this->checkSingleRoleUser($ids);
            if ($result['status']) {
                $builder = new AdminListBuilder;
                $builder->doSetStatus('Role', $ids, $status);
            } else {
                $this->error('身份' . $result['role']['name'] . '（' . $result["role"]["id"] . '）【' . $result["role"]["title"] . '】中存在单身份用户，移出单身份用户后才能禁用该身份！');
            }
        } else if ($status == -1) { //（真删除）
            $result = $this->checkSingleRoleUser($ids);
            if ($result['status']) {
                $result = $this->roleModel->where(array('id' => array('in', $ids)))->delete();
                if ($result) {
                    $userRoleList=$this->userRoleModel->where(array('role_id'=>array('in',$ids)))->select();
                    foreach($userRoleList as $val){
                        $this->setDefaultShowRole($val['role_id'],$val['uid']);
                    }
                    unset($val);
                    $this->userRoleModel->where(array('role_id'=>array('in',$ids)))->delete();
                    $this->success('删除成功！', U('Role/index'));
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('身份' . $result['role']['name'] . '（' . $result["role"]["id"] . '）【' . $result["role"]["title"] . '】中存在单身份用户，移出单身份用户后才能删除该身份！');
            }
        }
    }

    /**
     * 检测要删除的身份中是否存在单身份用户
     * @param $ids 要删除的身份ids
     * @return mixed
     * 
     */
    private function checkSingleRoleUser($ids)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);

        $user_ids=D('User/Member')->where(array('status'=>-1))->field('uid')->select();
        $user_ids=array_column($user_ids,'uid');

        $error_role_id = 0; //出错的身份id
        foreach ($ids as $role_id) {
            //获取拥有该身份的用户ids
            $uids = $this->userRoleModel->where(array('role_id' => $role_id))->field('uid')->select();
            $uids=array_column($uids,'uid');
            if(count($user_ids)){
                $uids=array_diff($uids,$user_ids);
            }
            if (count($uids) > 0) { //拥有该身份
                $uids = array_unique($uids);
                //获取拥有其他身份的用户ids
                $have_uids = $this->userRoleModel->where(array('role_id' => array('not in', $ids), 'uid' => array('in', $uids)))->field('uid')->select();
                if ($have_uids) {
                    $have_uids=array_column($have_uids,'uid');
                    $have_uids = array_unique($have_uids);

                    //获取不拥有其他身份的用户ids
                    $not_have = array_diff($uids, $have_uids);
                    if (count($not_have) > 0) {
                        $error_role_id = $role_id;
                        break;
                    }
                } else {
                    $error_role_id = $role_id;
                    break;
                }
            }
        }
        unset($role_id, $uids, $have_uids, $not_have);

        $result['status'] = 1;
        if ($error_role_id) {
            $result['role'] = $this->roleModel->where(array('id' => $error_role_id))->field('id,name,title')->find();
            $result['status'] = 0;
        }
        return $result;
    }

    /**
     * 身份基本信息配置
     * 
     */
    public function config()
    {
        $builder = new AdminConfigBuilder;
        $data = $builder->handleConfig();

        $builder->title('身份基本信息配置')
            ->data($data)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }

    //身份基本信息及配置 end

    //身份用户管理 start

    public function userList($page = 1, $r = 20)
    {
        $aRoleId = I('role_id', 0, 'intval');
        $aUserStatus = I('user_status', 0, 'intval');
        $aSingleRole=I('single_role',0,'intval');
        $aUid = I('get.uid',0, 'intval');
        $role_list = $this->roleModel->field('id,title as value')->order('sort asc')->select();
        $role_id_list = array_column($role_list, 'id');
        $role_tmp = array(array('id'=>0,'value'=>'全部'));
		foreach($role_list as $key=>$val){
			$role_arr[$val['id']] = $val['value'];
            array_push($role_tmp, array('id' => $val['id'], 'value' => $val['value']));
		}
        if ($aRoleId && in_array($aRoleId, $role_id_list)) {//筛选身份
            $map_user_list['role_id'] = $aRoleId;
        } else {
            //取消默认显示普通身份
            //$map_user_list['role_id'] = $role_list[0]['id'];
        }
        if ($aUserStatus) {//筛选状态
            $map_user_list['status'] = $aUserStatus == 3 ? 0 : $aUserStatus;
        }
        $user_ids=D('User/Member')->where(array('status'=>-1))->field('uid')->select();
        $user_ids=array_column($user_ids,'uid');
        if($aSingleRole){//单身份筛选
            $uids=$this->userRoleModel->group('uid')->field('uid')->having('count(uid)=1')->select();
            $uids=array_column($uids,'uid');//单身份用户id列表
            if($aSingleRole==1){
                if(count($user_ids)){
                    $map_user_list['uid']=array('in',array_diff($uids,$user_ids));
                }else{
                    $map_user_list['uid']=array('in',$uids);
                }
            }else{
                if(count($uids)&&count($user_ids)){
                    $map_user_list['uid']=array('not in',array_merge($user_ids,$uids));
                }else if(count($uids)){
                    $map_user_list['uid']=array('not in',$uids);
                }else if(count($user_ids)){
                    $map_user_list['uid']=array('not in',$user_ids);
                }
            }
        }else{
            if(count($user_ids)){
                $map_user_list['uid']=array('not in',$user_ids);
            }
        }
        if(!empty($aUid)){
            $map_user_list['uid']= $aUid;
            $map_user_list['_logic'] = 'AND';
        }
        $user_list = $this->userRoleModel->where($map_user_list)->page($page, $r)->order('id desc')->select();
        $totalCount = $this->userRoleModel->where($map_user_list)->count();
        foreach ($user_list as &$val) {
            $user = query_user(array('nickname', 'avatar64'), $val['uid']);
            $val['nickname'] = $user['nickname'];
            $val['avatar'] = $user['avatar64'];
        }
        unset($user, $val);

        $statusOptions = array(
            0 => array('id' => 0, 'value' => '全部'),
            1 => array('id' => 1, 'value' => '启用'),
            2 => array('id' => 2, 'value' => '未审核'),
            3 => array('id' => 3, 'value' => '禁用'),
        );

        $singleRoleOptions = array(
            0 => array('id' => 0, 'value' => '全部'),
            1 => array('id' => 1, 'value' => '单身份用户'),
            2 => array('id' => 2, 'value' => '非单身份用户'),
        );

        $builder = new AdminListBuilder();
        $builder->title('身份用户列表')
            ->setSelectPostUrl(U('Role/userList'));
        if ($map_user_list['status'] == 2) {
            $builder->setStatusUrl(U('Role/setUserAudit', array('role_id' => $map_user_list['role_id'])))->buttonEnable('', '审核通过')->buttonDelete('', '审核失败');
        } else {
            $builder->setStatusUrl(U('Role/setUserStatus', array('role_id' => $map_user_list['role_id'])))->buttonEnable()->buttonDisable();
        }

        $builder->buttonModal(U('Role/changeRole',array('role_id'=>$map_user_list['role_id'])), array(), '追加用户',array('data-title'=>'追加用户身份','target-form'=>'ids','class'=>'fbutton ajax-post'))
            ->button('初始化没身份的用户', array('href' => U('Role/initUnhaveUser'),'class'=>'layui-btn layui-btn-xs fbutton ajax-get'))
            ->select('身份：', 'role_id', 'select', '', '', '', $role_tmp)->select('状态：', 'user_status', 'select', '', '', '', $statusOptions)->select('', 'single_role', 'select', '', '', '', $singleRoleOptions)
            ->button('清空无效的身份信息',array('href'=>U('Role/initUndelUser'),'class'=>'layui-btn layui-btn-xs fbutton ajax-get'))
            ->keyId()
			->keyText('uid','UID')
            ->keyLink('nickname', '昵称', 'ucenter/index/information?uid=###')
			->keyText('role_id','角色名',$role_arr)
            ->keyStatus()
			->keyTime('start_time','开始时间')
			->keyTime('end_time','过期时间')
			->keyDoActionDel('role/deluserrole?ids=###&role_id='.$map_user_list['role_id'],'删除身份',array('class'=>'confirm ajax-post layui-btn layui-btn-xs hbutton btn_trash'))
            ->pagination($totalCount, $r)
            ->data($user_list)
            ->display();
    }
	
	public function deluserrole(){
			$roleModel=D('Role');
			$userRoleModel=D('UserRole');
            $aIds=I('get.ids');
            $aRole_id=I('get.role_id',0,'intval');
            $result['status']=0;
            if($aRole_id==0){
                $result['info']='请在对应身份列表下删除身份，以防误操作';
                $this->ajaxReturn($result);
            }
			$role = $roleModel->where(array('id'=>$aRole_id,'status'=>1))->find();
			if(empty($role)){
				$result['info']='无此身份或者此身份未开启无法操作';
                $this->ajaxReturn($result);
			}
			
            $ids=explode(',',$aIds);
            if(!count($ids)){
                $result['info']='没有要操作的用户！';
                $this->ajaxReturn($result);
            }
			
			$maps['id']=array('in',$ids);
            $uids=$this->userRoleModel->where($maps)->field('uid')->select();
            $uids=array_column($uids,'uid');
			$uid = $uids[0];
			
			$user_role = M('UserRole');
			$overdue_role = $user_role->table("__USER_ROLE__ ur")->join("__ROLE__ r on r.id= ur.role_id")->where("r.id != 1 and r.id = ".$aRole_id." and ur.uid=".$uid."")->field("ur.role_id,r.group_id")->select();

			if($overdue_role){
				$data=array();
				$group=array();
				foreach($overdue_role as $or){
					$data[]=$or["role_id"];
					$group[]=$or['group_id'];
				}
				$map=array();
				$map['uid']=$uid;
				$map['role_id']=array("in",$data);
				unset($data);
				$user_role->where($map)->delete();
				unset($map['role_id']);
				$map['group_id']=array("in",$group);
				unset($group);
				M('AuthGroupAccess')->where($map)->delete();
				unset($map);
				clean_query_user_cache($uid,array('avatar64','avatar128','avatar32','avatar256','avatar512','rank_link'));
			}
			$this->success('删除身份成功!');
	}
    
    public function initUndelUser(){
		//先清理无效的用户身份信息
    	$userrole = $this->userRoleModel->select();
    	foreach($userrole as &$v){
    		$u = D('User/UcenterMember')->where('id ='.$v['uid'])->find();
    		if(empty($u)){
    			$this->userRoleModel->where('uid ='.$v['uid'])->delete();
    		}
    	}
    	unset($v);
		//继续清理过期用户身份信息
		$user_role = M('UserRole');
		$overdue_role = $user_role->table("__USER_ROLE__ ur")->join("__ROLE__ r on r.id= ur.role_id")->where("r.day>0 and r.id != 1 and ur.start_time != ur.end_time and ur.uid=".$uid." and ur.end_time<".time())->field("ur.role_id,r.group_id")->select();
		if($overdue_role){
			$data=array();
			$group=array();
			foreach($overdue_role as $or){
				$data[]=$or["role_id"];
				$group[]=$or['group_id'];
			}
			$map=array();
			$map['uid']=$uid;
			$map['role_id']=array("in",$data);
			unset($data);
			$user_role->where($map)->delete();
			unset($map['role_id']);
			$map['group_id']=array("in",$group);
			unset($group);
			M('AuthGroupAccess')->where($map)->delete();
			unset($map);
			clean_query_user_cache($uid,array('avatar64','avatar128','avatar32','avatar256','avatar512','rank_link'));
		}
    	$this->success('清理成功!');
    }

    /**
     * 追加用户身份
     * 
     */
    public function changeRole()
    {
		$roleModel=D('Role');
		$userRoleModel=D('UserRole');
        if(IS_POST){
            $aIds=I('post.ids');
            $aRole_id=I('post.role_id',0,'intval');
            $aRole=I('post.role',0,'intval');
            $result['status']=0;
            if($aRole_id==$aRole||$aRole==0){
                $result['info']='非法操作！';
                $this->ajaxReturn($result);
            }
			$role = $roleModel->where(array('id'=>$aRole,'status'=>1))->find();
			if(empty($role)){
				$result['info']='无此身份或者此身份未开启无法升级';
                $this->ajaxReturn($result);
			}
			
            $ids=explode(',',$aIds);
            if(!count($ids)){
                $result['info']='没有要操作的用户！';
                $this->ajaxReturn($result);
            }

            $map['id']=array('in',$ids);
            $uids=$this->userRoleModel->where($map)->field('uid')->select();
            $uids=array_column($uids,'uid');
            $uids=array_unique($uids);//去重复

            $map_already['uid']=array('in',$uids);
            $map_already['role_id']=$aRole;
            $already_uids=$this->userRoleModel->where($map_already)->field('uid')->select();
            $already_uids=array_column($already_uids,'uid');
            if(count($already_uids)>=1){
                //$already_uids=array_column($already_uids,'uid');
                $last_uids=array_diff($uids,$already_uids);//去除已存在的
            }else{
                $last_uids = $uids;
            }
            //添加身份 20170228
            foreach($last_uids as $val){
                if($val != '1'){
                    $this->initRoleUser($aRole, $val);
                }
            }
            unset($val);
            //$res=$this->userRoleModel->where($map)->delete(); 删除原先身份
			$result['status']=1;
			$result['info']='追加身份成功';
            $this->ajaxReturn($result);
        }else{
            $aIds=I('get.ids');
            $aRole_id=I('get.role_id',0,'intval');
            $ids=implode(',',$aIds);
            $map['id']=array('neq',$aRole_id);
            $map['status']=1;
            $role_list=$this->roleModel->where($map)->field('id,title as value')->order('sort asc')->select();
            $this->assign('role_list',$role_list);
            $this->assign('ids',$ids);
            $this->assign('role_id',$aRole_id);
            $this->display();
        }
    }
	
	/**
	 * 初始化角色用户信息
	 * @param $role_id
	 * @param $uid
	 * @return bool

	 */
	private function initRoleUser($role_id = 0, $uid)
	{
		$memberModel = D('User/Member');
		$role = D('Role')->where(array('id' => $role_id))->find();
		$user_role = array('uid' => $uid, 'role_id' => $role_id, 'step' => "start",'start_time'=>time(),'end_time'=>time()+$role['day']*3600*24);
		if ($role['audit']) { //该角色需要审核
			$user_role['status'] = 2; //未审核
		} else {
			$user_role['status'] = 1;
		}
		$result = D('UserRole')->add($user_role);
		if (!$role['audit']) { //该角色不需要审核
			$memberModel->initUserRoleInfo($role_id, $uid);
		}
		$memberModel->initDefaultShowRole($role_id, $uid);

		return $result;
	}

    /**
     * 设置用户身份状态，启用、禁用
     * @param $ids
     * @param int $status
     * @param int $role_id
     * 
     */
    public function setUserStatus($ids, $status = 1, $role_id = 0)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        if ($status == 1) {
            $map_role['role_id'] = $role_id;
            $map_role['init'] = 0;
            $user_role=$this->userRoleModel->where($map_role)->field('id,uid')->select();
            $to_init_ids=array_column($user_role,'id');
            $to_init_uids=array_combine($to_init_ids,$user_role);
            $to_init_ids=array_intersect($ids,$to_init_ids);//交集获得需要初始化的ids
            foreach($to_init_ids as $val){
                D('User/Member')->initUserRoleInfo($role_id,$to_init_uids[$val]['uid']);
            }
            $builder = new AdminListBuilder;
            $builder->doSetStatus('UserRole', $ids, $status);
        } else if ($status == 0) {
            $uids = $this->userRoleModel->where(array('id' => array('in', $ids)))->field('uid')->select();
            if (count($uids)) {
                $uids = array_column($uids, 'uid');
                $map['role_id'] = array('neq', $role_id);
                $map['uid'] = array('in', $uids);
                $map['status'] = array('gt', 0);
                $has_other_role_user_ids = $this->userRoleModel->where($map)->field('uid')->select();
				if(count($has_other_role_user_ids)){
                    $unHave = array_diff($uids, array_column($has_other_role_user_ids, 'uid'));
                }else{
                    $unHave=$uids;
                }
                if (count($unHave) > 0) {
                    $map_ids['uid']=array('in',$unHave);
                    $map_ids['role_id']=$role_id;
                    $error_ids=$this->userRoleModel->where($map_ids)->field('id')->select();
                    $error_ids=implode(',',array_column($error_ids,'id'));
                    $this->error("id为{$error_ids}的身份用户只拥有该身份，不能被禁用！");
                }
                foreach($uids as $val){
                    $this->setDefaultShowRole($role_id,$val);
                }
                unset($val);
                $builder = new AdminListBuilder;
                $builder->doSetStatus('UserRole', $ids, $status);
            } else {
                $this->info('没有可操作数据！');
            }
        } else {
            $this->error('非法操作！');
        }
    }

    /**
     * 审核用户，通过，不通过
     * @param $ids
     * @param int $status
     * @param int $role_id
     * 
     */
    public function setUserAudit($ids,$status=1,$role_id=0)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        if ($status == 1) {
            $map_role['role_id'] = $role_id;
            foreach ($ids as $val) {
                $map_role['id'] = $val;
                $user_role=$this->userRoleModel->where($map_role)->find();
                if($user_role['init']==0){
                    D('User/Member')->initUserRoleInfo($role_id,$user_role['uid']);
                }
            }
            $builder = new AdminListBuilder;
            $builder->doSetStatus('UserRole', $ids, $status);
        } else if ($status == 0) {
            $uids = $this->userRoleModel->where(array('id' => array('in', $ids)))->field('uid')->select();
            if (count($uids)) {
                $builder = new AdminListBuilder;
                $builder->doSetStatus('UserRole', $ids, $status);
            } else {
                $this->info('没有可操作数据！');
            }
        } else {
            $this->error('非法操作！');
        }
    }


    /**
     * 重新设置用户默认身份及最后登录身份
     * @param $role_id
     * @param $uid
     * @return bool
     * 
     */
    private function setDefaultShowRole($role_id,$uid)
    {
        $memberModel=D('User/Member');
        $user=query_user(array('show_role','last_login_role'),$uid);
        if($role_id==$user['show_role']){
            $roles=$this->userRoleModel->where(array('role_id'=>array('neq',$role_id),'uid'=>$uid,'status'=>array('gt',0)))->field('role_id')->select();
            $roles=array_column($roles,'role_id');
            $show_role=$this->roleModel->where(array('id'=>array('in',$roles)))->order('sort asc')->find();
            $show_role_id=intval($show_role['id']);
            $data['show_role']=$show_role_id;
            if($role_id==$user['last_login_role']){
                $data['last_login_role']=$data['show_role'];
            }
            $memberModel->where(array('uid'=>$uid))->save($data);
        }else{
            if($role_id==$user['last_login_role']){
                $data['last_login_role']=$user['show_role'];
            }
            $memberModel->where(array('uid'=>$uid))->save($data);
        }
        return true;
    }

    //用户管理 end

    //分组 start

    /**
     * 分组列表
     * 
     */
    public function group()
    {
        $group = $this->roleGroupModel->field('id,title,update_time')->select();
        foreach ($group as &$val) {
            $map['group_id'] = $val['id'];
            $roles = $this->roleModel->selectByMap($map, 'id asc', 'title');
            $val['roles'] = implode(',', array_column($roles, 'title'));
        }
        unset($roles, $val);
        $builder = new AdminListBuilder;
        $builder->title('身份分组（同组身份互斥，即同一分组下的身份不能同时被用户拥有；同一身份同时只能存在于一个分组中）')
            ->buttonNew(U('Role/editGroup'))
            ->keyId()
            ->keyText('title', '标题')
            ->keyText('roles', '分组下的身份')
            ->keyUpdateTime()
            ->keyDoActionEdit('Role/editGroup?id=###')
            ->keyDoAction('Role/deleteGroup?id=###', '删除')
            ->data($group)
            ->display();
    }

    /**
     * 编辑分组
     * 
     */
    public function editGroup()
    {
        $aGroupId = I('id', 0, 'intval');
        $is_edit = $aGroupId ? 1 : 0;
        $title = $is_edit ? '编辑分组' : '新增分组';
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'op_t');
            $data['update_time'] = time();
            $roles = I('post.roles');
            if ($is_edit) {
                $result = $this->roleGroupModel->where(array('id' => $aGroupId))->save($data);
                if ($result) {
                    $result = $aGroupId;
                }
            } else {
                if ($this->roleGroupModel->where(array('title' => $data['title']))->count()) {
                    $this->error("{$title}失败！该分组已存在！");
                }
                $result = $this->roleGroupModel->add($data);
            }
            if ($result) {
                $this->roleModel->where(array('group_id' => $result))->setField('group_id', 0); //所有该分组下的身份全部移出
                if (!is_null($roles)) {
                    $this->roleModel->where(array('id' => array('in', $roles)))->setField('group_id', $result); //选中的身份全部移入分组
                }
                $this->success("{$title}成功！", U('Role/group'));
            } else {
                $this->error("{$title}失败！" . $this->roleGroupModel->getError());
            }
        } else {
            $data = array();
            if ($is_edit) {
                $data = $this->roleGroupModel->where(array('id' => $aGroupId))->find();
                $map['group_id'] = $aGroupId;
                $roles = $this->roleModel->selectByMap($map, 'id asc', 'id');
                $data['roles'] = array_column($roles, 'id');
            }
            $roles = $this->roleModel->field('id,group_id,title')->select();
            foreach ($roles as &$val) {
                $val['title'] = $val['group_id'] ? $val['title'] . "  (当前分组id：{$val['group_id']})" : $val['title'];
            }
            unset($val);
            $builder = new AdminConfigBuilder;
            $builder->title("{$title}（同组身份互斥，即同一分组下的身份不能同时被用户拥有；同一身份同时只能存在于一个分组中）");
            $builder->keyId()
                ->keyText('title', '标题')
                ->keyChosen('roles', '分组下身份选择', '一个身份同时只能存在于一个分组下', $roles)
                ->buttonSubmit()
                ->buttonBack()
                ->data($data)
                ->display();
        }
    }

    /**
     * 删除分组（真删除）
     * 
     */
    public function deleteGroup()
    {
        $aGroupId = I('id', 0, 'intval');
        if (!$aGroupId) {
            $this->error('参数错误！');
        }
        $this->roleModel->where(array('group_id' => $aGroupId))->setField('group_id', 0);
        $result = $this->roleGroupModel->where(array('id' => $aGroupId))->delete();
        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    //分组end

    //其他配置 start

    /**
     * 默认积分配置
     * 
     */
    public function configScore()
    {
        $aRoleId = I('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error('请选择身份！');
        }
        $map = getRoleConfigMap('score', $aRoleId);
        if (IS_POST) {
            $aPostKey = I('post.post_key', '', 'op_t');
            $post_key = explode(',', $aPostKey);
            $config_value = array();
            foreach ($post_key as $val) {
                if ($val != '') {
                    $config_value[$val] = I('post.' . $val, 0, 'intval');
                }
            }
            unset($val);
            $data['value'] = json_encode($config_value, true);
            if ($this->roleConfigModel->where($map)->find()) {
                $result = $this->roleConfigModel->saveData($map, $data);
            } else {
                $data = array_merge($map, $data);
                $result = $this->roleConfigModel->addData($data);
            }
            if ($result) {
                $this->success('操作成功！', U('Admin/Role/configScore', array('id' => $aRoleId)));
            } else {
                $this->error('操作失败！' . $this->roleConfigModel->getError());
            }
        } else {
            $mRole_list = $this->roleModel->field('id,title')->select();

            //获取默认配置值
            $score = $this->roleConfigModel->where($map)->getField('value');
            $score = json_decode($score, true);

            //获取member表中积分字段$score_keys
            $model = D('User/Score');
            $score_keys = $model->getTypeList(array('status' => array('GT', -1)));

            $post_key = '';
            foreach ($score_keys as &$val) {
                $post_key .= $val['mark'].',';
                $val['value'] = $score[$val['mark']]?$score[$val['mark']]:0; //写入默认值
            }
            unset($val);

            $this->meta_title = '身份默认积分配置';
            $this->assign('score_keys', $score_keys);
            $this->assign('post_key', $post_key);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId));
            $this->assign('tab', 'score');
            $this->display('score');
        }
    }

    /**
     * 身份默认头像配置
     * 
     */
    public function configAvatar()
    {
        $aRoleId = I('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error('请选择身份！');
        }
        $map = getRoleConfigMap('avatar', $aRoleId);
        $data['data'] = '';
        if (IS_POST) {
            $data['value'] = I('post.avatar_id', 0, 'intval');
            $aSetNull = I('post.set_null', 0, 'intval');
            if (!$aSetNull) {
                if($data['value']==0){
                    $this->error('请先上传头像！');
                }
                if ($this->roleConfigModel->where($map)->find()) {
                    $result = $this->roleConfigModel->saveData($map, $data);
                } else {
                    $data = array_merge($map, $data);
                    $result = $this->roleConfigModel->addData($data);
                }
            } else {//使用系统默认头像
                if ($this->roleConfigModel->where($map)->find()) {
                    $result = $this->roleConfigModel->where($map)->delete();
                }else{
                    $this->success('当前使用的已经是系统默认头像了！');
                }
            }
            if ($result) {
                clear_role_cache($aRoleId);
                $this->success('操作成功！', U('Admin/Role/configAvatar', array('id' => $aRoleId)));
            } else {
                $this->error('操作失败！' . $this->roleConfigModel->getError());
            }
        } else {
            $avatar_id = $this->roleConfigModel->where($map)->getField('value');
            $mRole_list = $this->roleModel->field('id,title')->select();
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId, 'avatar' => $avatar_id));
            $this->assign('tab', 'avatar');
            $this->display('avatar');
        }
    }

    /**
     * 身份默认头衔配置
     * 
     */
    public function configRank()
    {
        $aRoleId = I('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error('请选择身份！');
        }
        $map = getRoleConfigMap('rank', $aRoleId);
        if (IS_POST) {
            $data['value'] = '';
            if (isset($_POST['ranks'])) {
                sort($_POST['ranks']);
                $data['value'] = implode(',', array_unique($_POST['ranks']));
            }
            $aReason['reason'] = I('post.reason', '', 'op_t');
            $data['data'] = json_encode($aReason, true);
            if ($this->roleConfigModel->where($map)->find()) {
                $result = $this->roleConfigModel->saveData($map, $data);
            } else {
                $data = array_merge($map, $data);
                $result = $this->roleConfigModel->addData($data);
            }
            if ($result) {
                $this->success('操作成功！', U('Admin/Role/configrank', array('id' => $aRoleId)));
            } else {
                $this->error('操作失败！' . $this->roleConfigModel->getError());
            }
        } else {
            $mRole_list = $this->roleModel->field('id,title')->select();
            $mRole_list = array_combine(array_column($mRole_list, 'id'), $mRole_list);

            //获取默认配置值
            $rank = $this->roleConfigModel->where($map)->field('value,data')->find();
            if ($rank) {
                $rank['data'] = json_decode($rank['data'], true);
                if (!$rank['data']['reason']) {
                    $rank['data']['reason'] = "{$mRole_list[$aRoleId]['title']}(身份)默认拥有该头衔！";
                }
            } else {
                $rank['data']['reason'] = "{$mRole_list[$aRoleId]['title']}(身份)默认拥有该头衔！";
                $rank['value'] = array();
            }

            //获取头衔列表
            $model = D('Rank');
            $list = $model->field('id,uid,title,logo,create_time,types')->select();
            $canApply = $unApply = array();
            foreach ($list as $val) {
                $val['name'] = query_user(array('nickname'), $val['uid']);
                $val['name'] = $val['name']['nickname'];
                if ($val['types']) {
                    $canApply[] = $val;
                } else {
                    $unApply[] = $val;
                }
            }
            unset($val);

            $this->assign('can_apply', $canApply);
            $this->assign('un_apply', $unApply);
            $this->assign('reason', $rank['data']['reason']);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId, 'ranks' => $rank['value']));
            $this->assign('tab', 'rank');
            $this->display('rank');
        }
    }

    /**
     * 身份扩展资料配置 及 注册时要填写的资料配置
     * 
     */
    public function configField()
    {
        $aRoleId = I('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error('请选择身份！');
        }
        $aType = I('get.type', 0, 'intval'); //扩展资料设置类型：1注册时要填写资料配置，0扩展资料字段设置

        if ($aType) { //注册时要填写资料配置
            $type = 'register_expend_field';
        } else { //扩展资料字段设置
            $type = 'expend_field';
        }
        $map = getRoleConfigMap($type, $aRoleId);
        if (IS_POST) {
            $data['value'] = '';
            if (isset($_POST['fields'])) {
                sort($_POST['fields']);
                $data['value'] = implode(',', array_unique($_POST['fields']));
            }
            if ($this->roleConfigModel->where($map)->find()) {
                $result = $this->roleConfigModel->saveData($map, $data);
            } else {
                $data = array_merge($map, $data);
                $result = $this->roleConfigModel->addData($data);
            }
            if ($result === false) {
                $this->error('操作失败' . $this->roleConfigModel->getError());
            } else {
                clear_role_cache($aRoleId);
                $this->success('操作成功!');
            }
        } else {
            $aType = I('get.type', 0, 'intval'); //扩展资料设置类型：1注册时要填写资料配置，0扩展资料字段设置

            $mRole_list = $this->roleModel->field('id,title')->select();

            $fields = $this->roleConfigModel->where($map)->getField('value');

            if ($aType == 1) { //注册时要填写资料配置
                $map_fields = getRoleConfigMap('expend_field', $aRoleId);
                $expend_fields = $this->roleConfigModel->where($map_fields)->getField('value');
                $field_list = $expend_fields ? $this->getExpendField($expend_fields) : array();
                $this->meta_title = '注册时要填写资料配置';
                $tpl = 'fieldregister'; //模板地址
                $tab = 'fieldRegister';
            } else { //扩展资料字段设置
                $field_list = $this->getExpendField();
                $this->meta_title = '扩展资料字段设置';
                $tpl = 'field'; //模板地址
                $tab = 'field';
            }
            $this->assign('field_list', $field_list);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId, 'fields' => $fields));
            $this->assign('tab', $tab);
            $this->display($tpl);
        }
    }

    //身份其他配置 end

    /**
     * 获取扩展字段列表
     * @param string $in
     * @return mixed
     * 
     */
    private function getExpendField($in = '')
    {
        if ($in != '') {
            $in = is_array($in) ? $in : explode(',', $in);
            $map_field['id'] = array('in', $in);
        }
        $map['status'] = array('egt', 0);
        $profileList = D('field_group')->where($map)->order("sort asc")->select(); //获取扩展字段分组

        $fieldSettingModel = D('field_setting');
        $type_default = array(
            'input' => '单行文本框',
            'radio' => '单选按钮',
            'checkbox' => '多选框',
            'select' => '下拉选择框',
            'time' => '日期',
            'textarea' => '多行文本框'
        );
        $map_field['status'] = array('egt', 0);
        foreach ($profileList as $key => &$val) {
            //获取分组下字段列表
            $map_field['profile_group_id'] = $val['id'];
            $field_list = $fieldSettingModel->where($map_field)->order("sort asc")->select();
            foreach ($field_list as &$vl) {
                $vl['form_type'] = $type_default[$vl['form_type']];
            }
            unset($vl);
            if ($field_list) {
                $val['field_list'] = $field_list;
            } else {
                unset($profileList[$key]);
            }
        }
        unset($key, $val, $field_list);
        return $profileList;
    }

    /**
     * 上传图片（上传默认头像）
     */
    public function uploadPicture()
    {
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器
        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            empty($info['download']) && $info['download'] = $info['file'];
            $return = array_merge($info['download'], $return);
            $return['path256'] = getThumbImageById($return['id'], 256, 256);
            $return['path128'] = getThumbImageById($return['id'], 128, 128);
            $return['path64'] = getThumbImageById($return['id'], 64, 64);
            $return['path32'] = getThumbImageById($return['id'], 32, 32);
        } else {
            $return['status'] = 0;
            $return['info'] = $Picture->getError();
        }
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


   /**
     * 初始化没身份的用户
     */
    public function initUnhaveUser()
    {
        $memberModel=D('User/Member');

        $uids=$memberModel->field('uid')->select();
        $uids=array_column($uids,'uid');

        $role=$this->roleModel->selectByMap(array('status'=>1));
        $role=array_column($role,'id');
        $map['role_id']=array('in',$role);

        $have_uids=$this->userRoleModel->where($map)->field('uid')->select();
        if(count($have_uids)){
            $have_uids=array_column($have_uids,'uid');
            $have_uids=array_unique($have_uids);
            $not_have_uids=array_diff($uids,$have_uids);
        }

        $data['status']=1;
        $data['role_id']=1;
        $data['step']="finish";
        $data['init']=1;
        $dataList=array();

        foreach($not_have_uids as $val){
            $data['uid']=$val;
            $dataList[]=$data;
            $memberModel->initUserRoleInfo(1,$val);
            $memberModel->initDefaultShowRole(1,$val);
        }
        unset($val);
        $this->userRoleModel->addAll($dataList);
        $this->success('操作成功！');
    }
} 