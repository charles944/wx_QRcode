<?php

/**
 * 参数数量任意，返回第一个非空参数
 * @return mixed|null
 */
function alt() {
    for($i = 0 ; $i < func_num_args(); $i++) {
        $arg = func_get_arg($i);
        if($arg) {
            return $arg;
        }
    }
    return null;
}

function saveMobileInSession($mobile) {
    session_start();
    session('send_sms', array('mobile'=>$mobile));
}

function getMobileFromSession() {
    return session('send_sms.mobile');
}