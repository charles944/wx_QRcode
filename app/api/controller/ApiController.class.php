<?php
namespace api\controller;
use api\exception\ReturnException;
use think\Controller;

require_once(dirname(__FILE__).'/../common/function.php');

abstract class ApiController extends Controller {
    protected $api;
    protected $isInternalCall;

    public function _initialize() {
        //读取站点信息
        $config = api('Config/lists');
        C($config);

        //站点关闭，显示关闭消息
        if(!C('WEB_SITE_CLOSE')){
            $this->apiError(403, '站点已经关闭，请稍后访问~');
        }

        $this->$isInternalCall = false;
    }

    public function setInternalCallApi($value=true) {
        $this->isInternalCall = $value ? true : false;
    }
    
    public function setApiLog($action_name){
    	$data['url'] = GetCurUrl();
    	$data['create_time'] = TIME();
    	$data['action_name'] = $action_name;
    	$data['ip'] = get_client_ip(1);
    	D('ApiLog')->add($data);
    }

    /**
     * 找不到接口时调用该函数
     */
    public function _empty() {
        $this->apiError(404, "找不到该接口");
    }

    protected function apiReturn($success, $error_code=0, $message=null, $redirect=null, $extra=null) {
        //生成返回信息
        $result = array();
        $result['success'] = $success;
        $result['error_code'] = $error_code;
        if($message !== null) {
            $result['message'] = $message;
        }
        if($redirect !== null) {
            $result['redirect'] = $redirect;
        }
        foreach($extra as $key=>$value) {
            $result[$key] = $value;
        }
        //将返回信息进行编码
        $format = $_REQUEST['format'] ? $_REQUEST['format'] : 'json';//返回值格式，默认json
        if($this->isInternalCall) {
            throw new ReturnException($result);
        } else if($format == 'json') {
            echo json_encode($result);
            exit;
        } else if($format == 'xml') {
            echo xml_encode($result);
            exit;
        } else {
            $_GET['format'] = 'json';
            $_REQUEST['format'] = 'json';
            return $this->apiError(400, "format参数错误");
        }
    }

    protected function apiSuccess($message, $redirect=null, $extra=null) {
        return $this->apiReturn(true, 0, $message, $redirect, $extra);
    }

    protected function apiError($error_code, $message, $redirect=null, $extra=null) {
        return $this->apiReturn(false, $error_code, $message, $redirect, $extra);
    }

    protected function getUid() {
        return is_login();
    }

    protected function requireLogin() {
        $uid = $this->getUid();
        if(!$uid) {
            $this->apiError(401,"需要登录");
        }
    }

    protected function updateUser($uid, $data) {
        //检查参数
        if(!$uid || !$data) {
            $this->apiError(400,'参数不能为空');
        }
        //将数据分配到两张表中
        $split = $this->splitUserFields($data);
        $home_user= $split['home'];
        $ucenter_user = $split['ucenter'];
        //写入数据库
        $api = $this->api;
        if($ucenter_user && !$api->updateUserNoPassword($uid, $ucenter_user)) {
            $this->apiError(0,$api->getError());
        }
        if($home_user) {
            $map = array();
            $map['uid'] = $uid;
            $model = D('Home/Member');
            $result = $model->where($map)->save($home_user);
            if(!$result) {
                $this->apiError(0,'写入数据库错误');
            }
        }
        //返回成功
        return true;
    }

    protected function getRegisterErrorMessage($error_code) {
        switch ($error_code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            case -12:$error='用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';break;
            default:  $error = '未知错误';
        }
        return $error;
    }

    protected function getRegisterErrorCode($error_code) {
        switch ($error_code) {
            case -1:  return 701;
            case -2:  return 702;
            case -3:  return 703;
            case -4:  return 704;
            case -5:  return 705;
            case -6:  return 706;
            case -7:  return 707;
            case -8:  return 708;
            case -9:  return 709;
            case -10: return 710;
            case -11: return 711;
            default:  return 700;
        }
    }

    protected function splitUserFields($data) {
        $result = array();
        $home_fields = array('nickname','sex','name','signature','birthday');
        $result['home'] = array_gets($data, $home_fields);
        $ucenter_fields = array('email','password','mobile');
        $result['ucenter'] = array_gets($data, $ucenter_fields);
        return $result;
    }

    protected function verifyPassword($uid, $password) {
        $result = D('User/UcenterMember')->verifyUser($uid, $password);
        if(!$result) {
            $this->apiError(0,'密码错误');
        }
    }
}