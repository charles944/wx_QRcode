<?php

namespace plugins\chinacity;
use common\controller\Plugin;

/**
 * 中国省市区三级联动插件
 */

class ChinacityPlugin extends Plugin{

	public $info = array(
		'name'=>'chinacity',
		'title'=>'中国省市区三级联动',
		'description'=>'每个系统都需要的一个中国省市区三级联动插件，将镇级地区移除',
		'status'=>1,
		'author'=>'靑年',
		'version'=>'1.2.0'
	);

	public function install(){
		$this->getisHook('j_china_city', $this->info['name'], $this->info['description']);

		$install_sql = './plugins/chinacity/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		/* if(count(M()->query("SHOW TABLES LIKE '".$this->table_name()."Advertising'")) != 1){
			session('addons_install_error', ',AdvsType表未创建成功，请手动检查插件中的sql，修复后重新安装');
			return false;
		}*/
		return true;
	}

	public function uninstall(){
		
		$uninstall_sql = './plugins/chinacity/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

	public function j_china_city($param){
		$this->assign('param', $param);
		$this->display('chinacity');
	}

	//获取插件所需的钩子是否存在
	public function getisHook($str, $addons, $msg=''){
		$hook_mod = M('Hooks');
		$where['name'] = $str;
		$gethook = $hook_mod->where($where)->find();
		if(!$gethook || empty($gethook) || !is_array($gethook)){
			$data['name'] = $str;
			$data['description'] = $msg;
			$data['type'] = 1;
			$data['update_time'] = NOW_TIME;
			$data['addons'] = $addons;
			if( false !== $hook_mod->create($data) ){
				$hook_mod->add();
			}
		}
	}
}