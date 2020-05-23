<?php
namespace addons\dysms\model;
use think\Model;

/**
 * 分类模型
 */
class DysmsTplModel extends Model{
	
	/* 自动完成规则 */
	protected $_auto = array(
			array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
	);

	/* 用户模型自动验证 */
    protected $_validate = array(
        /* 验证action */
        array('action', '', -3, self::EXISTS_VALIDATE, 'unique'), //操作行为被占用

    );

	
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	}
	
	/*  展示数据  */
	public function action2tplcode(){
		$data = $this->where('status = 1')->order('status desc,id asc')->getField('type,action,sign,template_code');
		foreach($data as &$val){
			$result[$val['action']] = $val;
		}
		return $result;
	}
	
	/* 获取编辑数据 */
	public function detail($id){
		$data = $this->find($id);
		return $data;
	}
	
	/* 禁用 */
	public function forbidden($id){
		return $this->save(array('id'=>$id,'status'=>'0'));
	}
	
	/* 启用 */
	public function off($id){
		return $this->save(array('id'=>$id,'status'=>'1'));
	}
	
	/* 删除 */
	public function del($id){
		return $this->delete($id);
	}
	
	/**
	 * 新增或更新一个文档
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据
	 * @author qmit <tan@qmit.cn>
	 */
	public function update(){
		/* 获取数据对象 */
		$data = $this->create();
		if(empty($data)){
			return false;
		}
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			$id = $this->add(); //添加基础内容
			if(!$id){
				$this->error = '新增出错！';
				return false;
			}
		} else { //更新数据
			$status = $this->save(); //更新基础内容
			if(false === $status){
				$this->error = '更新出错！';
				return false;
			}
		}
	
		//内容添加或更新完成
		return $data;
	
	}	
	
	/* 时间处理规则 */
	protected function getCreateTime(){
		$create_time    =   I('post.create_time');
		return $create_time?strtotime($create_time):NOW_TIME;
	}
	
}