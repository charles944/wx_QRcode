<?php
namespace user\model;
use think\Model;
class UserScoreTmpModel extends Model{
	
	protected $tableName = 'user_score_tmp';
	private $ScoreTmpModel =null;
    protected function _initialize()
    {
        parent::_initialize();
        $this->ScoreTmpModel =  M('user_score_tmp');
    }
    public function getListByPage($map,$page=1,$order='id desc',$field='*',$r=20)
    {
        $totalCount=$this->ScoreTmpModel->where($map)->count();
        if($totalCount){
            $list=$this->ScoreTmpModel->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }
    public function getList($map,$order='view desc',$limit=5,$field='*')
    {
        $lists = $this->ScoreTmpModel->where($map)->order($order)->limit($limit)->field($field)->select();
        return $lists;
    }
} 