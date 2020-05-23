<?php
namespace user\model;
use think\Model;

/**
 * Class ScoreModel   用户积分模型
 * @package Ucenter\Model
 * @author:朝兮夕兮
 */
class ScoreModel extends Model
{

    private $typeModel =null;
    protected function _initialize()
    {
        parent::_initialize();
        $this->typeModel =  M('user_score_type');
    }

    /**
     * getTypeList  获取类型列表
     * @param string $map
     * @return mixed
     * @author:朝兮夕兮
     */
    public function getTypeList($map=''){
       $list = $this->typeModel->where($map)->order('id asc')->select();

       return $list;
   }

    /**
     * getType  获取单个类型
     * @param string $map
     * @return mixed
     * @author:朝兮夕兮
     */
    public function getType($map=''){
        $type = $this->typeModel->where($map)->find();
        return $type;
    }

    /**
     * addType 增加积分类型
     * @param $data
     * @return mixed
     * @author:朝兮夕兮
     */
    public function addType($data){
        $db_prefix = C('DB_PREFIX');
       $res = $this->typeModel->add($data);
       $query = "ALTER TABLE `{$db_prefix}member` ADD `".$data['mark']."` FLOAT NOT NULL DEFAULT '0' COMMENT  '".$data['title']."'";
       D()->execute($query);
       $query1 = "ALTER TABLE `{$db_prefix}user_score_log` ADD `".$data['mark']."` FLOAT NOT NULL DEFAULT '0'";
       D()->execute($query1);
       $query2 = "ALTER TABLE `{$db_prefix}user_score_log` ADD `"."re_".$data['mark']."` FLOAT NOT NULL DEFAULT '0'";
       D()->execute($query2);
       return $res;
    }

    /**
     * delType  删除分类
     * @param $ids
     * @return mixed
     * @author:朝兮夕兮
     */
    public function delType($ids){
        $db_prefix = C('DB_PREFIX');
        $data = $this->typeModel->where(array('id'=>array(array('in',$ids),array('gt',5),'and')))->select();
        foreach($data as $v){
            if($v['id']>5){
                $query = "ALTER TABLE `{$db_prefix}member` drop column ".$v['mark'];
                D()->execute($query);
                $query1 = "ALTER TABLE `{$db_prefix}user_score_log` drop column ".$v['mark'];
                D()->execute($query1);
                $query2 = "ALTER TABLE `{$db_prefix}user_score_log` drop column `re_".$v['mark']."`";
                D()->execute($query2);
            }
        }
        $res = $this->typeModel->where(array('id'=>array(array('in',$ids),array('gt',5),'and')))->delete();
        return $res;
    }

    /**
     * editType  修改积分类型
     * @param $data
     * @return mixed
     * @author:朝兮夕兮
     */
    public function editType($data){
        $db_prefix = C('DB_PREFIX');
        $data_old = $this->typeModel->where(array('id'=>array(array('eq',$data['id']),array('gt',5),'and')))->select();
        //var_dump($data_old);
        if($data_old){
        	foreach($data_old as $v){
		        $res = $this->typeModel->save($data);
		        //$query = "alter table `{$db_prefix}member` modify column `".$data['mark']."` FLOAT comment '".$data['title']."';";
		        $query = "ALTER TABLE `{$db_prefix}member` change `".$v['mark']."` `".$data['mark']."` FLOAT NOT NULL DEFAULT '0'";
		        D()->execute($query);
		        $query1 = "ALTER TABLE `{$db_prefix}user_score_log` change `".$v['mark']."` `".$data['mark']."` FLOAT NOT NULL DEFAULT '0'";
		        D()->execute($query1);
		        $query2 = "ALTER TABLE `{$db_prefix}user_score_log` change `"."re_".$v['mark']."` `"."re_".$data['mark']."` FLOAT NOT NULL DEFAULT '0'";
		        D()->execute($query2);
        	}
        	
        }else{
        	$data_rec = $this->typeModel->where(array('id'=>array(array('eq',$data['id']),array('elt',5),'and')))->select();
        	if($data_rec){
        		foreach($data_rec as $v){
        			$data_new['id'] = $data['id'];
        			$data_new['title'] = $data['title'];
        			$data_new['status'] = $data['status'];
        			$data_new['unit'] = $data['unit'];
        			$data_new['icon'] = $data['icon'];
					$data_new['cash'] = $data['cash'];
					$data_new['minimum'] =$data['minimum'];
					$data_new['maxmum'] = $data['maxmum'];
					$data_new['exchange'] = $data['exchange'];
        			$res = $this->typeModel->save($data_new);
        		}
        	}
        }
        return $res;
    }
    /**
     * getTypeName  获取货币类型名
     * @param 类型 $type 数字：类型ID，字符：类型名
     * @return Ambigous <\think\mixed, NULL, unknown, multitype:unknown , mixed, object>
     */
    public function getTypeName($type){
    	if(is_numeric($type)){
            $mark = $this->typeModel->where('id ='.$type)->getField('title');
        }else{
            $mark = $this->typeModel->where(array('mark'=>$type))->getField('title');
        }
    	return $mark;
    }
    /**
     * 获取货币类型图标
     * @param unknown $type
     * @return unknown
     */
    public function getTypeIcon($type){
    	if(is_numeric($type)){
    		$markicon = $this->typeModel->where('id ='.$type)->getField('icon');
    	}else{
    		$markicon = $this->typeModel->where(array('mark'=>$type))->getField('icon');
    	}
    	return $markicon;
    }
    /**
     * 
     * @param unknown $id
     * @return Ambigous <string, \think\mixed, NULL, unknown, mixed, multitype:unknown , object>
     */
    public function getTypemarkById($id){
    	if(is_numeric($id)){
    		$mark = $this->typeModel->where('id ='.$id)->getField('mark');
    	}else{
    		$mark = '';
    	}
    	return $mark;
    }
    /**
     * 
     * @param unknown $type
     * @return unknown
     */
    public function getTypeUnit($type){
    	if(is_numeric($type)){
    		$markunit = $this->typeModel->where('id ='.$type)->getField('unit');
    	}else{
    		$markunit = $this->typeModel->where(array('mark'=>$type))->getField('unit');
    	}
    	return $markunit;
    }


    /**
     * getUserScore  获取用户的积分
     * @param int $uid
     * @param int $type 值为id或者货币mark标识
     * @return mixed
     * @author:朝兮夕兮
     */
    public function getUserScore($uid,$type){
        $model = D('User/Member');
        if(is_numeric($type)){
            $mark = $this->typeModel->where('id ='.$type)->getField('mark');
            $score = $model->where(array('uid'=>$uid))->getField($mark);
        }else{
            $score = $model->where(array('uid'=>$uid))->getField($type);
        }
        return $score;
    }

    /**
     * setUserScore  设置用户的积分
     * @param $uids
     * @param $score
     * @param $type 积分类型里面的 mark 直接显示标识,比如 gold_coin,jifenbao
     * @param string $action
     * @author:朝兮夕兮
     */
    public function setUserScore($uids,$action='inc', $type, $score){

        $model = D('User/Member');
        $uids = is_array($uids) ? $uids : explode(',',$uids);
        switch($action){
            case 'inc':
                $score = abs($score);
                $res = $model->where(array('uid'=>array('in',$uids)))->setInc($type,$score);
                break;
            case 'dec':
                $score = abs($score);
                $res = $model->where(array('uid'=>array('in',$uids)))->setDec($type,$score);
                break;
            case 'to':
                $res = $model->where(array('uid'=>array('in',$uids)))->setField($type,$score);
                break;
            default:
                $res = false;
                break;
        }
        $this->cleanUserCache($uids,$type);
        return $res;
    }
    
    public function cleanUserCache($uid,$type){
    	$uid = is_array($uid) ? $uid : explode(',',$uid);
    	$type = is_array($type)?$type:explode(',',$type);
    	foreach($uid as $val){
    		foreach($type as $v){
    			clean_query_user_cache($val, $v);
    			clean_query_user_cache($val, 'title');
    		}
    	}
    }
    
    public function getAllScore($uid)
    {
    	$typeList = $this->getTypeList(array('status'=>1));
    	$return = array();
    	foreach($typeList as $key => &$v){
    		$v['value'] = $this->getUserScore($uid,$v['id']);
    		$return[$v['id']] = $v;
    
    	}
    	unset($v);
    	return $return;
    }
}