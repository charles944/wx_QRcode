<?php
namespace user\model;
use think\Model;
class UserPayLogModel extends Model
{
    protected $_validate = array(
        array('title', '1,200', '标题长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
    
    /**
     * 注：appname及之后的参数，一般情况下无需填写
     * @param        $to_uid 接受消息的用户ID
     * @param string $content 内容
     * @param string $title 标题，默认为  您有新的消息
     * @return int
     * @author 朝兮夕兮，那你自己想想
     */
    public function sendPayLog($to_uid, $type = 0, $content = '' )
    {
        if (!is_login()) {
            return 0;
        }
        if ($to_uid != is_login()){
            return 0;
        }
        $this->sendPayLogWithoutCheckSelf($to_uid, $type, $content);
    }
    
    /**
     * @param $to_uid 接受消息的用户ID
     * @param string $content 内容
     */
    public function sendPayLogWithoutCheckSelf($to_uid, $type = 0, $pay_action, $pay_type = 'gold_coin', $pay_num = 0, $content = '', $title='', $purse = 0, $purse_type = NULL, $appname = '', $apptype = 0)
    {
    	$action_ip = get_client_ip(1);
    	switch($pay_action){
    		case 'inc':
    			$pay_num = abs($pay_num);
    			$pay_num = '+'.$pay_num;
    			break;
    		case 'dec':
    			$pay_num = abs($pay_num);
    			$pay_num = '-'.$pay_num;
    			break;
    		case 'to':
    			$pay_num = '='.$pay_num;
    			break;
    		default:
    			$pay_num = 0;
    			break;
    	}
    	$to_uid = is_array($to_uid) ? $to_uid : array($to_uid);
    	foreach($to_uid as $val){
	        $log['uid'] = $val;
	        $log['username'] = get_username($val);
	        $log['pay_type'] = $pay_type;
	        $log['pay_num'] = $pay_num;
	        $log['title'] = op_t($title);
	        $log['remark'] = op_t($content);
	        $log['create_time'] = time();
	        $log['action_ip'] = $action_ip;
	        $log['type'] = $type;
	        $log['appname'] = $appname;
	        $log['apptype'] = $apptype;
	        $log['purse'] = $purse;
	        $log['purse_type'] = $purse_type;
	        $log['status'] = 1;
	        $rs = $this->add($log);
    	}
	    return $rs;
    }

}
