<?php
namespace common\model;
use think\Model;
class VerifyModel extends Model
{
    protected $tableName = 'verify';
    protected $_auto = array(array('create_time', NOW_TIME, self::MODEL_INSERT));
    public function addVerify($account,$type,$uid=0)
    {
        $uid = $uid?$uid:is_login();
        if ($type == 'mobile' ||  $type == 'email') {
            $verify = create_rand(6, 'num');
        }
        $this->where(array('account'=>$account,'type'=>$type))->delete();
        $data['verify'] = $verify;
        $data['account'] = $account;
        $data['type'] = $type;
        $data['uid'] = $uid;
        $data = $this->create($data);
        $res = $this->add($data);
        if(!$res){
            return false;
        }
        return $verify;
    }
    public function getVerify($id){
        $verify = $this->where(array('id'=>$id))->getField('verify');
        return $verify;
    }
    public function checkVerify($account,$type,$verify,$uid){
        if(empty($uid)){
            $verify = $this->where(array('account'=>$account,'type'=>$type,'verify'=>$verify))->find();
        }else{
            $verify = $this->where(array('account'=>$account,'type'=>$type,'verify'=>$verify,'uid'=>$uid))->find(); 
        }
        
        if(!$verify){
            return false;
        }
        $this->where(array('account'=>$account,'type'=>$type))->delete();
        return true;
    }
}