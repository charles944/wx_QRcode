<?php
namespace addons\smsbao\controller;

use admin\controller\AddonsController;
use admin\builder\AdminListBuilder;
use admin\builder\AdminConfigBuilder;
use admin\builder\AdminTreeListBuilder;

class AdminController extends AddonsController
{
    /* 添加短信模板 */
	public function add(){
		$this->meta_title = '添加短信模板';
		$current = U('adminlist',array('name'=>'smsbao'));
		$this->assign('current',$current);
		$this->display(T('addons://smsbao@smsbao/edit'));
	}
	
	/* 编辑短信模板 */
	public function edit(){
		$this->meta_title = '修改短信模板';
		$id     =   I('get.id','');
		$current = U('adminlist',array('name'=>'smsbao'));
		$detail = D('Addons://smsbao/SmsbaoTpl')->detail($id);
		$this->assign('info',$detail);
		$this->assign('current',$current);
		$this->display(T('addons://smsbao@smsbao/edit'));
	}
	
	/* 禁用短信模板 */
	public function forbidden(){
		$this->meta_title = '禁用短信模板';
		$id     =   I('get.id','');
		if(D('Addons://smsbao/SmsbaoTpl')->forbidden($id)){
			$this->success('成功禁用该模板', U('/admin/addons/adminlist/name/smsbao'));
		}else{
			$this->error(D('addons://smsbao/SmsbaoTpl')->getError());
		}
	}
	
	/* 启用短信模板 */
	public function off(){
		$this->meta_title = '启用短信模板';
		$id     =   I('get.id','');
		if(D('Addons://smsbao/SmsbaoTpl')->off($id)){
			$this->success('成功启用该模板', U('/admin/addons/adminlist/name/smsbao'));
		}else{
			$this->error(D('Addons://smsbao/SmsbaoTpl')->getError());
		}
	}
	
	/* 删除短信模板 */
	public function del(){
		$this->meta_title = '删除短信模板';
		$id     =   I('get.id','');
		if(D('Addons://smsbao/SmsbaoTpl')->del($id)){
			$this->success('删除成功', U('/admin/addons/adminlist/name/smsbao'));
		}else{
			$this->error(D('Addons://smsbao/SmsbaoTpl')->getError());
		}
	}
	
	/* 更新模板 */
	public function update(){
		$this->meta_title = '更新短信模板';
		$res = D('Addons://smsbao/SmsbaoTpl')->update();
		if(!$res){
			$this->error(D('Addons://smsbao/SmsbaoTpl')->getError());
		}else{
			if($res['id']){
				$this->success('更新成功',  Cookie('__forward__'));
			}else{
				$this->success('新增成功',  Cookie('__forward__'));
			}
		}
	}
}
