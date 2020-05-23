<?php
namespace user\model;
use think\Model;
class UserConfigModel extends Model
{
    public function addData($data=array())
    {
        $res=$this->add($data);
        return $res;
    }
    public function findData($map=array())
    {
        $res=$this->where($map)->find();
        return $res;
    }
    public function saveValue($map=array(),$value='')
    {
        $res=$this->where($map)->setField('value',$value);
        return $res;
    }
}