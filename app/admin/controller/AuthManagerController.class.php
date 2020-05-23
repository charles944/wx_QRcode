<?php
namespace admin\controller;

use admin\model\AuthRuleModel;
use admin\model\AuthGroupModel;
use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminConfigBuilder;

/**
 * 权限管理控制器
 * Class AuthManagerController
 * @author 
 */
class AuthManagerController extends AdminController
{
    protected $authGroupModel;
    protected $authGroupAccessModel;
    protected $authGroupConfigModel;
    protected $authRuleModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->authGroupModel = D("AuthGroup");
        $this->authGroupAccessModel = D('AuthGroupAccess');
        $this->authGroupConfigModel = D('AuthGroupConfig');
        $this->authRuleModel = D('AuthRule');
    }

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 
     */
    public function updateRules()
    {
        //需要新增的节点必然位于$nodes
        $nodes = $this->returnNodes(false);

        $AuthRule = M('AuthRule');
        $map = array('module' => 'admin', 'type' => array('in', '1,2'));//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            if ($value['pid'] > 0) {
                $temp['type'] = AuthRuleModel::RULE_URL;
            } else {
                $temp['type'] = AuthRuleModel::RULE_MAIN;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids = array();//保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            if (isset($data[$key])) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']] = $rule;
            } elseif ($rule['status'] == 1) {
                $ids[] = $rule['id'];
            }
        }
        if (count($update)) {
            foreach ($update as $k => $row) {
                if ($row != $diff[$row['id']]) {
                    $AuthRule->where(array('id' => $row['id']))->save($row);
                }
            }
        }
        if (count($ids)) {
            $AuthRule->where(array('id' => array('IN', implode(',', $ids))))->save(array('status' => -1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if (count($data)) {
            $AuthRule->addAll(array_values($data));
        }
        if ($AuthRule->getDbError()) {
            trace('[' . __METHOD__ . ']:' . $AuthRule->getDbError());
            return false;
        } else {
            return true;
        }
    }


    /**
     * 权限管理首页
     * @author 
     */
    public function index()
    {
        $list = $this->lists('AuthGroup', array('module' => 'admin'), 'sort asc');
        $list = int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('_use_tip', true);
        $this->meta_title = '权限管理';
        $this->display();
    }

    /**
     * 创建管理员用户组
     * @author 
     */
    // public function createGroup()
    // {
    //     if (empty($this->auth_group)) {
    //         $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
    //     }
    //     $this->meta_title = '新增用户组';
    //     $this->display('editgroup');
    // }

    /**
     * 编辑管理员用户组
     * @author 
     */
    public function editGroup()
    {
        $aId = I('id', 0, 'intval');
        $is_edit = $aId ? 1 : 0;
        $title = $is_edit ? "编辑用户组" : "新增用户组";
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'op_t');
            $data['description'] = I('post.description', '', 'op_t');
            $data['status'] = I('post.status', 1, 'intval');
            $data['sort'] = I('post.sort', 0, 'intval');
            if ($is_edit) {
                $data['id'] = $aId;
                $result = $this->authGroupModel->save($data);
            } else {
                $data['module'] = 'admin';
                $data['type'] = AuthGroupModel::TYPE_ADMIN;
                $result = $this->authGroupModel->add($data);
            }
            if ($result) {
                $this->success($title . "成功", U('AuthManager/index'));
            } else {
                $error_info = $this->authGroupModel->getError();
                $this->error($title . "失败！" . $error_info);
            }
        } else {
            $data['status'] = 1;

            if ($is_edit) {
                $data = $this->authGroupModel->where(array('id' => $aId))->find();
            }

            $builder = new AdminConfigBuilder;
            $builder->meta_title = $title;
            $builder->title($title)
                ->keyId()
                ->keyText('title', '用户组名称', '不能重复')
                ->keyTextArea('description', '描述')
                ->keyInteger('sort','显示&权重排序','值越大，身份等级越高越靠后')
                ->keyStatus()
                ->data($data)
                ->buttonSubmit(U('editGroup'))
                ->buttonBack()
                ->display();
        }
    }




    /**
     * 管理员用户组数据写入/更新
     * @author 
     */
    public function writegroup()
    {
        if (isset($_POST['rules'])) {
            sort($_POST['rules']);
            $_POST['rules'] = implode(',', array_unique($_POST['rules']));
        }
        $_POST['module'] = 'admin';
        $_POST['type'] = AuthGroupModel::TYPE_ADMIN;
        $AuthGroup = D('AuthGroup');
        $data = $AuthGroup->create();
        if ($data) {
            $oldGroup = $AuthGroup->find($_POST['id']);
            $data['rules'] = $this->getMergedRules($oldGroup['rules'], explode(',', $_POST['rules']), 'eq');
            if (empty($data['id'])) {
                $r = $AuthGroup->add($data);
            } else {
                $r = $AuthGroup->save($data);
            }
            if ($r === false) {
                $this->error('操作失败' . $AuthGroup->getError());
            } else {
                $this->success('操作成功!');
            }
        } else {
            $this->error('操作失败' . $AuthGroup->getError());
        }
    }

    private function checkUserAuthGroup($groupid)
    {
        $user_ids_data=D('AuthGroupAccess')->where(array('group_id'=>$groupid, 'status'=>1))->field('uid')->select();
        $user_ids=array_column($user_ids_data,'uid');
        if(count($user_ids) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 状态修改
     * @author 
     */
    public function changeStatus($method = null)
    {
        $id     =   I('id');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        switch (strtolower($method)) {
            case 'forbidgroup':
                if($id ==1 ){
                    $this->error('ID=1的默认用户组无法禁用');
                }
                $result = $this->checkUserAuthGroup($id);
                if ($result) {
                    $this->error('此用户组下存在用户，移除用户后才能禁用该用户组！');
                } else {
                    $msg    =   array('success'=>'禁用成功', 'error'=>'禁用失败');
                    $this->forbid('AuthGroup', "id={$id}", $msg);
                }
                break;
            case 'resumegroup':
                $this->resume('AuthGroup', "id={$id}");
                break;
            case 'deletegroup':
                if($id ==1 ){
                    $this->error('ID=1的默认用户组无法删除');
                }
                $result = $this->checkUserAuthGroup($id);
                if ($result) {
                    $this->error('此用户组下存在用户，移除用户后才能删除该用户组！');
                } else {
                    $this->delete('AuthGroup', "id={$id}");
                }
                break;
            default:
                $this->error($method . '参数非法');
        }
    }

    /**
     * 用户组授权用户列表
     * @author 
     */
    public function user($group_id)
    {
        if (empty($group_id)) {
            $this->error('参数错误');
        }

        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))->getfield('id,title,rules');
        $prefix = C('DB_PREFIX');
        $l_table = $prefix . (AuthGroupModel::UCENTER_MEMBER);
        $r_table = $prefix . (AuthGroupModel::AUTH_GROUP_ACCESS);
        $model = M()->table($l_table . ' m')->join($r_table . ' a ON m.id=a.uid');
        $_REQUEST = array();
        $list = $this->lists($model, array('a.group_id' => $group_id, 'm.status' => array('egt', 0)), 'm.id asc', null, 'm.id,m.last_login_time,m.last_login_ip,m.status,m.username,m.email,m.mobile,a.start_time,a.end_time');
        int_to_string($list,array('status'=>array(1=>'正常',-1=>'删除',0=>'冻结',2=>'未审核',3=>'未激活')));
        $this->assign('_list', $list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->meta_title = '成员授权';
        $this->display();
    }



    public function tree($tree = null)
    {
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     * @author 
     */
    public function group()
    {
        $uid = I('uid');
        $auth_groups = D('AuthGroup')->getGroups();
        $user_groups = AuthGroupModel::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value) {
            $ids[] = $value['group_id'];
        }
        $nickname = D('Admin/Member')->getNickName($uid);
        $this->assign('nickname', $nickname);
        $this->assign('auth_groups', $auth_groups);
        $this->assign('user_groups', implode(',', $ids));
        $this->display();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author 
     */
    public function addToGroup()
    {
        $uid = I('uid');
        $gid = I('group_id');
        if (empty($uid)) {
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if (is_numeric($uid)) {
            if (is_administrator($uid)) {
                $this->error('该用户为超级管理员');
            }
            if (!M('UcenterMember')->where(array('id' => $uid))->find()) {
                $this->error('用户不存在');
            }
        }

        if ($gid && !$AuthGroup->checkGroupId($gid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToGroup($uid, $gid)) {
            $this->success('操作成功');
        } else {
            $this->error('用户组未做变更，已拥有用户组');
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 
     */
    public function removeFromGroup()
    {
        $uid = I('uid');
        $gid = I('group_id');
        if ($uid == UID) {
            $this->error('不允许解除自身授权');
        }
        if (empty($uid) || empty($gid)) {
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error('用户组不存在');
        }
        if ($AuthGroup->removeFromGroup($uid, $gid)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function addNode()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        if (IS_POST) {
            $Rule = D('AuthRule');
            $data = $Rule->create();
            if ($data) {
                if (intval($data['id']) == 0) {
                    $id = $Rule->add();
                } else {
                    $Rule->save($data);
                    $id = $data['id'];
                }

                if ($id) {
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    $this->success('编辑成功');
                } else {
                    $this->error('编辑失败');
                }
            } else {
                $this->error($Rule->getError());
            }
        } else {
            $aId = I('id', 0, 'intval');
            if ($aId == 0) {
                $info['module']=I('module','','op_t');
            }else{
                $info = D('AuthRule')->find($aId);
            }

            $this->assign('info', $info);
            //  $this->assign('info', array('pid' => I('pid')));
            $modules = D('Common/Module')->getAll();
            $this->assign('Modules', $modules);
            $this->meta_title = '新增前台权限节点';
            $this->display();
        }
    }

    public function deleteNode(){
        $aId=I('id',0,'intval');
        if($aId>0){
           $result=   M('AuthRule')->where(array('id'=>$aId))->delete();
            if($result){
                $this->success('删除成功。');
            }else{
                $this->error('删除失败。');
            }
        }else{
            $this->error('必须选择节点。');
        }
    }
    /**
     * 访问授权页面
     * @author 
     */
    public function access()
    {
        $this->updateRules();
        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
            ->getfield('id,id,title,rules');
        $node_list = $this->returnNodes();
        $map = array('module' => 'admin', 'type' => AuthRuleModel::RULE_MAIN, 'status' => 1);
        $main_rules = M('AuthRule')->where($map)->getField('name,id');
        $map = array('module' => 'admin', 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
        $child_rules = M('AuthRule')->where($map)->getField('name,id');

        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->meta_title = '访问授权';
        $this->display();
    }

    public function accessUser()
    {
        $aId = I('get.group_id', 0, 'intval');

        if (IS_POST) {
            $aId = I('id', 0, 'intval');
            $aOldRule = I('post.old_rules', '', 'text');
            $aRules = I('post.rules', array());
            $rules = $this->getMergedRules($aOldRule, $aRules);
            $authGroupModel = M('AuthGroup');
            $group = $authGroupModel->find($aId);
            $group['rules'] = $rules;
            $result = $authGroupModel->save($group);
            if ($result) {
                $this->success('权限保存成功。');
            } else {
                $this->error('权限保存失败。');
            }

        }
        $this->updateRules();
        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'type' => AuthGroupModel::TYPE_ADMIN))
            ->getfield('id,id,title,rules');
        $node_list = $this->getNodeListFromModule(D('Common/Module')->getAll());
		
        //  $node_list   =M('AuthRule')->where(array('module'=>array('neq','admin'),'type'=>AuthRuleModel::RULE_URL,'status'=>1))->select();

        $map = array('module' => array('neq', 'admin'), 'type' => AuthRuleModel::RULE_MAIN, 'status' => 1);
        $main_rules = M('AuthRule')->where($map)->getField('name,id');
        $map = array('module' => array('neq', 'admin'), 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
        $child_rules = M('AuthRule')->where($map)->getField('name,id');

        $group = M('AuthGroup')->find($aId);
        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $group);

        $this->meta_title = '用户前台授权';
        $this->display('');
    }
    
    public function union(){
    	
   		$aId = I('get.group_id', 0, 'intval');
    	
    	if (IS_POST) {
    		$aId = I('id', 0, 'intval');
    		$aNewRule = I('post.tage','','text');
    		$authGroupModel = M('AuthGroup');
    		$group = $authGroupModel->find($aId);
    		$rules = json_encode($aNewRule);
    		$group['union_rules'] = $rules;
    		$result = $authGroupModel->save($group);
    		if ($result) {
    			$this->success('权限保存成功。');
    		} else {
    			$this->error('权限保存失败。');
    		}
    	
    	}
    	$this->updateRules();
    	$auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'type' => AuthGroupModel::TYPE_ADMIN))
    	->getfield('id,id,title,rules');
    	$node_list = $this->getNodeListFromModule(D('Common/Module')->getAll());
    	//  $node_list   =M('AuthRule')->where(array('module'=>array('neq','admin'),'type'=>AuthRuleModel::RULE_URL,'status'=>1))->select();
    	
    	$map = array('module' => array('neq', 'admin'), 'type' => AuthRuleModel::RULE_MAIN, 'status' => 1);
    	$main_rules = M('AuthRule')->where($map)->getField('name,id');
    	$map = array('module' => array('neq', 'admin'), 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
    	$child_rules = M('AuthRule')->where($map)->getField('name,id');

    	
    	$group = M('AuthGroup')->find($aId);
    	$that_group = M('AuthGroup')->where(array('id'=> $aId, 'status' => array('egt', '0'), 'type' => AuthGroupModel::TYPE_ADMIN))
    	->getfield('id,id,title,rules,union_rules');
    	$this->assign('main_rules', $main_rules);
    	$this->assign('auth_rules', $child_rules);
    	$this->assign('node_list', $node_list);
    	$this->assign('auth_group', $auth_group);
    	$this->assign('that_group', $that_group);
    	$this->assign('this_group', $group);
    	
    	$this->meta_title = '提成授权';
    	$this->display('union');
    }
    
    /**
     * 管理员用户组提成数据写入/更新
     * @author 朝兮夕兮
     */
    public function writeUnion()
    {
    	if (isset($_POST['union_rules'])) {
    		sort($_POST['union_rules']);
    		$_POST['union_rules'] = implode(',', array_unique($_POST['union_rules']));
    	}
    	$_POST['module'] = 'admin';
    	$_POST['type'] = AuthGroupModel::TYPE_ADMIN;
    	$AuthGroup = D('AuthGroup');
    	$data = $AuthGroup->create();
    	if ($data) {
    		$oldGroup = $AuthGroup->find($_POST['id']);
    		$data['union_rules'] = $this->getMergedRules($oldGroup['union_rules'], explode(',', $_POST['union_rules']), 'eq');
    		if (empty($data['id'])) {
    			$r = $AuthGroup->add($data);
    		} else {
    			$r = $AuthGroup->save($data);
    		}
    		if ($r === false) {
    			$this->error('操作失败' . $AuthGroup->getError());
    		} else {
    			$this->success('操作成功!');
    		}
    	} else {
    		$this->error('操作失败' . $AuthGroup->getError());
    	}
    }

    private function getMergedRules($oldRules, $rules, $isAdmin = 'neq')
    {
        $map = array('module' => array($isAdmin, 'admin'), 'status' => 1);
        $otherRules = M('AuthRule')->where($map)->field('id')->select();
        $oldRulesArray = explode(',', $oldRules);
        $otherRulesArray = getSubByKey($otherRules, 'id');

        //1.删除全部非Admin模块下的权限，排除老的权限的影响
        //2.合并新的规则
        foreach ($otherRulesArray as $key => $v) {
            if (in_array($v, $oldRulesArray)) {
                $key_search = array_search($v, $oldRulesArray);
                if ($key_search !== false)
                    array_splice($oldRulesArray, $key_search, 1);
            }
        }

        return str_replace(',,', ',', implode(',', array_unique(array_merge($oldRulesArray, $rules))));


    }

    //预处理规则，去掉未安装的模块
    public function getNodeListFromModule($modules)
    {
        $node_list = array();
        foreach ($modules as $module) {
            if ($module['is_setup']) {

                $node = array('name' => $module['name'], 'alias' => $module['alias'], 'has_tage' => $module['has_tage'] , 'tage_info' => $module['tage_info']);
                $map = array('module' => $module['name'], 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
				
                $node['child'] = M('AuthRule')->where($map)->select();
                $node_list[] = $node;
            }

        }
        return $node_list;
    }
    
    public function configRank()
    {
        $aGroupId = I('id', 0, 'intval');
        if (!$aGroupId) {
            $this->error('请选择用户组！');
        }
        $map = getGroupConfigMap('rank', $aGroupId);
        if (IS_POST) {
            $data['value'] = '';
            if (isset($_POST['ranks'])) {
                sort($_POST['ranks']);
                $data['value'] = implode(',', array_unique($_POST['ranks']));
            }
            $aReason['reason'] = I('post.reason', '', 'op_t');
            $data['data'] = json_encode($aReason, true);
            if ($this->authGroupConfigModel->where($map)->find()) {
                $result = $this->authGroupConfigModel->saveData($map, $data);
            } else {
                $data = array_merge($map, $data);
                $result = $this->authGroupConfigModel->addData($data);
            }
            if ($result) {
                $this->success('操作成功！', U('Admin/AuthManager/configrank', array('id' => $aGroupId)));
            } else {
                $this->error('操作失败！' . $this->authGroupConfigModel->getError());
            }
        } else {
            $mGroup_list = $this->authGroupModel->field('id,title')->select();
            $mGroup_list = array_combine(array_column($mGroup_list, 'id'), $mGroup_list);

            //获取默认配置值
            $group = $this->authGroupConfigModel->where($map)->field('value,data')->find();
            if ($group) {
                $group['data'] = json_decode($group['data'], true);
                if (!$group['data']['reason']) {
                    $group['data']['reason'] = "{$mGroup_list[$aGroupId]['title']}(用户组)默认拥有该头衔！";
                }
            } else {
                $group['data']['reason'] = "{$mGroup_list[$aGroupId]['title']}(用户组)默认拥有该头衔！";
                $group['value'] = array();
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
            $this->assign('reason', $group['data']['reason']);
            $this->assign('group_list', $mGroup_list);
            $this->assign('this_group', array('id' => $aGroupId, 'ranks' => $group['value']));
            $this->assign('tab', 'rank');
            $this->display('rank');
        }
    }

    public function configScore()
    {
        $aGroupId = I('id', 0, 'intval');
        if (!$aGroupId) {
            $this->error('请选择用户组！');
        }
        $map = getGroupConfigMap('score', $aGroupId);
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
            if ($this->authGroupConfigModel->where($map)->find()) {
                $result = $this->authGroupConfigModel->saveData($map, $data);
            } else {
                $data = array_merge($map, $data);
                $result = $this->authGroupConfigModel->addData($data);
            }
            if ($result) {
                $this->success('操作成功！', U('Admin/AuthManager/configScore', array('id' => $aGroupId)));
            } else {
                $this->error('操作失败！' . $this->authGroupConfigModel->getError());
            }
        } else {
            $mGroup_list = $this->authGroupModel->field('id,title')->select();

            //获取默认配置值
            $score = $this->authGroupConfigModel->where($map)->getField('value');
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

            $this->meta_title = '用户组赠送积分配置';
            $this->assign('score_keys', $score_keys);
            $this->assign('post_key', $post_key);
            $this->assign('group_list', $mGroup_list);
            $this->assign('this_group', array('id' => $aGroupId));
            $this->assign('tab', 'score');
            $this->display('score');
        }
    }
}
