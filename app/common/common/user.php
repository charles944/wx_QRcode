<?php
/**
 * check_username  根据type或用户名来判断注册使用的是用户名、邮箱或者手机
 * @param $username
 * @return bool

 */
function check_username($username)
{
    if (preg_match("/[a-z0-9_\-\.]+@([a-z0-9_\-]+?\.)+[a-z]{2,3}/i", $username)) {
        $type = 2;
    } elseif (preg_match("/^(1[0-9])[0-9]{9}$/", $username)) {
        $type = 3;
    } else {
        $type = 1;
    }
    return $type;
}

/**
 * check_reg_type  验证注册格式是否开启
 * @param $type
 * @return bool
 */
function check_reg_type($type){
    $t[1] = $t['username'] ='username';
    $t[2] = $t['email'] ='email';
    $t[3] = $t['mobile'] ='mobile';

    $switch = modC('REG_SWITCH','','USERCONFIG');
    if($switch){
        $switch = explode(',',$switch);
        if(in_array($t[$type],$switch)){
           return true;
        }
    }
    return false;

}


/**
 * check_login_type  验证登录提示信息是否开启
 * @param $type
 * @return bool
 */
function check_login_type($type){
    $t[1] = $t['username'] ='username';
    $t[2] = $t['email'] ='email';
    $t[3] = $t['mobile'] ='mobile';

    $switch = modC('LOGIN_SWITCH','username','USERCONFIG');
    if($switch){
        $switch = explode(',',$switch);
        if(in_array($t[$type],$switch)){
            return true;
        }
    }
    return false;

}


/**
 * set_user_status   设置用户状态
 * @param $uid
 * @param $status
 */
function set_user_status($uid,$status){
    //D('User/Member')->where(array('uid'=>$uid))->setField('status',$status);
    D('User/UcenterMember')->where(array('id'=>$uid))->setField('status',$status);
    return true;
}

function set_user_field($uid, $field, $value){
    //D('User/Member')->where(array('uid'=>$uid))->setField('status',$status);
    D('User/UcenterMember')->where(array('id'=>$uid))->setField($field,$value);
    return true;
}

/**
 * set_users_status   批量设置用户状态
 * @param $map
 * @param $status
 */
function set_users_status($map,$status){
    //('User/Member')->where($map)->setField('status',$status);
    D('User/UcenterMember')->where($map)->setField('status',$status);
    return true;
}
