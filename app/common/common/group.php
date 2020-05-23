<?php
/**
 * 获取当前用户登录的身份的名
 * @return varchar 身份名
 * @author 
 */
function get_login_group_name(){
	$user = session('user_auth');
    if (empty($user)) {
        return '普通会员';
    } else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['group_name'] : 0;
	}
}
/**
 * 获取当前用户登录的身份的标识
 * @return int 身份id
 * @author 
 */
function get_login_group()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['group_id'] : 0;
    }
}

function get_login_group_endtime(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        $user_group_id = session('user_auth_sign') == data_auth_sign($user) ? $user['group_id'] : 0;
        if($user_group_id > 1){
            $user_auth_group_data = D('AuthGroupAccess')->where(array('uid'=>$user['uid'],'status'=>1,'group_id'=>$user_group_id))->find();
            if(!empty($user_auth_group_data)){
                return date('Y-m-d H:i:s', $user_auth_group_data['end_time']);
            }
        }
    }
}

/**
 * 获取当前用户登录的用户组是否审核
 * @return int 身份id
 * @author 
 */
function get_login_group_audit()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['audit'] : 0;
    }
}

/**
 * 根据用户uid获取角色id
 * @param int $uid
 * @return int
 * @author 
 */
function get_group_id($uid=0)
{
    !$uid&&$uid=is_login();
    if($uid==is_login()){//自己
        $group_id=get_login_group();
    }else{//不是自己
        $group_id=query_user(array('show_group'),$uid);
        $group_id=$group_id['show_group'];
    }
    return $group_id;
}

/**
 * 获取角色配置表 D('AuthGroupConfig')查询条件
 * @param $type 类型
 * @param int $role_id 角色id
 * @return mixed 查询条件 $map
 * @author 
 */
function getGroupConfigMap($type,$group_id=0){
    $map['group_id']=$group_id;
    $map['name']=$type;
    switch($type){
        case 'score'://积分
            break;
        case 'rank'://默认头衔
            break;
        default:;
    }
    return $map;
}

/**
 * 清除角色缓存
 * @param int $role_id 角色id
 * @param $type 要清除的缓存，空：清除所有；字符串（Role_Expend_Info_）：清除一个缓存；数组array('Role_Expend_Info_','Role_Avatar_Id_','Role_Register_Expend_Info_')：清除多个缓存
 * @return bool
 * @author 
 */
function clear_group_cache($role_id=0,$type){
    if(isset($type)){
        if(is_array($type)){
            foreach($type as $val){
                S($val.$role_id,null);
            }
            unset($val);
        }else{
            S($type.$role_id,null);
        }
    }else{
        S('GROUP_Expend_Info_'.$role_id,null);
        S('GROUP_Avatar_Id_'.$role_id,null);
        S('GROUP_Register_Expend_Info_'.$role_id,null);
    }
    return true;
}