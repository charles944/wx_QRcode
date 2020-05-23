<?php
namespace user\model;
use think\Model;

class UserScoreLogModel extends Model
{
    
    /**
     * 获取所有明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getAllmingxi($mod, $type, $lit = 99999, $remark = null)
    {
        $remark = op_t($remark);
        $map['type'] = $type;
        $map['model'] = $mod;
        if ($remark != null){
            $map['remark'] = array("like","%".$remark."%");
            $messages = $this->where($map)->order('id desc')->limit($lit)->select();
        }else{
            $messages = $this->where($map)->order('id desc')->limit($lit)->select();
        }
        foreach ($messages as &$v) {
            $v['ctime'] = friendlyDate($v['create_time']);
            $v['remark'] = op_t($v['remark']);
        }
        unset($v);
        return $messages;
    }
    /**
     * 获取所有明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getChartsmingxi($mod, $type = 0, $litdate=0, $lit = 99999, $remark = null)
    {
        $remark = op_t($remark);
        if($litdate != 0){
        $map = find_createtime($litdate);
        }
        $map['type'] = $type;
        $map['model'] = $mod;
        if ($remark != null){
            $map['remark'] = array(array("like","%".$remark."%"),array("notlike","%额外%"),array("notlike","%排行榜%"),array("notlike","%提成%"),'and');
            $messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('sumgold desc')->limit($lit)->select();
        }else{
        	$map['remark'] = array(array("notlike","%排行榜%"),array("notlike","%额外%"),array("notlike","%提成%"),'and');
            $messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('sumgold desc')->limit($lit)->select();
        }
        foreach ($messages as &$v) {
            $v['ctime'] = friendlyDate($v['create_time']);
            $v['remark'] = op_t($v['remark']);
        }
        unset($v);
        return $messages;
    }
    
    
    /**
     * 获取所有明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getUnionChartsmingxi($remark = null, $litdate=0, $lit = 99999, $type = 0)
    {
    	$remark = op_t($remark);
    	if($litdate != 0){
    		$map = find_createtime($litdate);
    	}
    	$map['type'] = $type;
    	if ($remark != null){
    		$map['remark'] = array(array("like","%".$remark."%"),array("notlike","%排行榜%"),array("notlike","%提成%"),'and');
    		$messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('sumgold desc')->limit($lit)->select();
    	}else{
    		$map['remark'] = array(array("notlike","%排行榜%"),array("notlike","%提成%"),'and');
    		$messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('sumgold desc')->limit($lit)->select();
    	}
    	foreach ($messages as &$v) {
    		$v['ctime'] = friendlyDate($v['create_time']);
    		$v['remark'] = op_t($v['remark']);
    	}
    	unset($v);
    	return $messages;
    }
    /**
     * 获取个人明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getUsermingxi($uid, $mod, $type)
    {
        $messages = $this->where('uid=' . $uid . ' and type=' .$type .' and model=' . $mod)->order('id desc')->limit(99999)->select();
        foreach ($messages as &$v) {
            $v['ctime'] = friendlyDate($v['create_time']);
            $v['remark'] = op_t($v['remark']);
        }
        unset($v);
        return $messages;
    }
    
    /**
     * 获取个人明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getSumUsermingxi($uid, $mod, $type, $litdate=0, $remark = null)
    {
        if (!is_login()) {
            return 0;
        }
        if ($uid != is_login()){
            return 0;
        }
        $remark = op_t($remark);
        if($litdate != 0){
            $map = find_createtime($litdate);
        }
        $map['uid'] = $uid;
        $map['type'] = $type;
        $map['model'] = $mod;
        if ($remark != null){
            $map['remark'] = array(array("like","%".$remark."%"),array("notlike","%排行榜%"),array("notlike","%额外%"),array("notlike","%提成%"),'and');
            $messages = $this->where($map)->group("uid")->order('id desc')->SUM('gold_coin');
        }else{
        	$map['remark'] = array(array("notlike","%排行榜%"),array("notlike","%额外%"),array("notlike","%提成%"),'and');
            $messages = $this->where($map)->group("uid")->order('id desc')->SUM('gold_coin');
        }
        unset($v);
        return $messages;
    }
    
    /**
     * 获取个人明细信息
     * @param $uid 会员ID
     * @param $mod 模型
     * @param $type 类型
     */
    public function getUnionSumUsermingxi($uid, $type, $litdate=0, $remark = null)
    {
    	if (!is_login()) {
    		return 0;
    	}
    	if ($uid != is_login()){
    		return 0;
    	}
    	$remark = op_t($remark);
    	if($litdate != 0){
    		$map = find_createtime($litdate);
    	}
    	$map['uid'] = $uid;
    	$map['type'] = $type;
    	if ($remark != null){
    		$map['remark'] = array(array("like","%".$remark."%"),array("notlike","%排行榜%"),array("notlike","%提成%"),'and');
    		$messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('id desc')->select();
    	}else{
    		$map['remark'] = array(array("notlike","%排行榜%"),array("notlike","%提成%"),'and');
    		$messages = $this->field("sum(gold_coin) as sumgold,uid,nickname,create_time,remark")->where($map)->group("uid")->order('id desc')->select();
    	}
    	foreach ($messages as &$v) {
    		$v['ctime'] = friendlyDate($v['create_time']);
    		$v['remark'] = op_t($v['remark']);
    	}
    	unset($v);
    	return $messages;
    }
    /**
     * 添加明细
     * 
     */
    /**
     * 注：appname及之后的参数，一般情况下无需填写
     * @param        $to_uid 接受消息的用户ID
     * @param string $content 内容
     * @param string $title 标题，默认为  您有新的消息
     * @return int
     * @author 朝兮夕兮，那你自己想想
     */
    public function sendmingxi($to_uid, $type = 0, $mod = '', $aAction, $aMark, $aValue, $content = '', $action_id = 0)
    {
        if (!is_login()) {
            return 0;
        }
        if ($to_uid != is_login()){
            return 0;
        }
        $this->sendmingxiWithoutCheckSelf($to_uid, $type, $mod, $aAction, $aMark, $aValue, $content, $action_id);
    }
    
    /**
     * @param $to_uid 接受消息的用户ID
     * @param string $content 内容
     */
    public function sendmingxiWithoutCheckSelf($to_uid, $type = 0, $mod = '', $aAction, $aMark, $aValue, $content = '', $action_id = 0)
    {
    	$to_uid = is_array($to_uid) ? $to_uid : array($to_uid);
    	foreach($to_uid as $val){
	    	$action_ip = ip2long(get_client_ip());
	        $message['uid'] = $val;
	        $message['nickname'] = get_nickname($val);
	        $reMark = D('User/Score')->getUserScore($val, $aMark);
	        switch($aAction){
	        	case 'inc':
	        		$score = abs($aValue);
	        		$message[$aMark] = '+'.$score;
	        		$message['re_'.$aMark] = $reMark;
	        		break;
	        	case 'dec':
	        		$score = abs($aValue);
	        		$message[$aMark] = '-'.$score;
	        		$message['re_'.$aMark] = $reMark;
	        		break;
	        	case 'to':
	        		$message[$aMark] = '='.$score;
	        		$message['re_'.$aMark] = $reMark;
	        		break;
	        	default:
	        		$message[$aMark] = 0;
	        		$message['re_'.$aMark] = $reMark;
	        		break;
	        }
	        $message['remark'] = op_t($content);
	        $message['create_time'] = time();
	        $message['action_ip'] = $action_ip;
	        $message['model'] = $mod;
	        $message['type'] = $type;
	        $message['action_id'] = $action_id;
	        
	        $rs = $this->add($message);
    	}
	    return $rs;
    }

}