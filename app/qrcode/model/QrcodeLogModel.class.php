<?php
namespace qrcode\model;

use common\model\ContentHandlerModel;
use think\Model;

class QrcodeLogModel extends Model{

    public function getListByPage($map,$page=1,$order='update_time desc',$field='*',$r=20)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }

    public function getList($map,$order='view desc',$limit=5,$field='*')
    {
        $lists = $this->where($map)->order($order)->limit($limit)->field($field)->select();
        return $lists;
    }

    public function setDead($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->setField('dead_line',time());
        return $res;
    }

    public function getData($id)
    {
        if($id>0){
            $map['id']=$id;
            $data=$this->where($map)->find();
            return $data;
        }
        return null;
    }

    public function getMyData($id)
    {
        $uid = is_login();
        if($id>0){
            $map['id']=$id;
            $map['uid'] = $uid;
            $data=$this->where($map)->find();
            return $data;
        }
        return null;
    }

}