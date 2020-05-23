<?php

namespace plugins\systeminfo;
use common\controller\Plugin;

class SysteminfoPlugin extends Plugin{

	public $info = array(
		'name'=>'systeminfo',
		'title'=>'系统环境信息',
		'description'=>'用于显示一些服务器的信息',
		'status'=>1,
		'author'=>'靑年',
		'version'=>'2.1.1'
	);

	public function install(){
		return true;
	}

	public function uninstall(){
		return true;
	}
	/**
	 * 获取mysql数据库大小
	 */
	public function mysql_db_size()
	{
	//$Model = self::_model();
	$sql = "SHOW TABLE STATUS FROM ".C('DB_NAME');
	$tblPrefix = C('DB_PREFIX');
	if($tblPrefix != null) {
		$sql .= " LIKE '{$tblPrefix}%'";
	}
	$row = M()->query($sql);
	$size = 0;
	foreach($row as $value) {
		$size += $value["Data_length"] + $value["Index_length"];
	}
	return round(($size/1048576),2).'M';
	}
	
	/**
	 * 转换b,kb,mb,gb,tb,pb
	 */
	private function convert($size){
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	public function adminindex($param){
		error_reporting(0); //抑制所有错误信息
		$config = $this->getConfig();
		
		$map['status'] = array('egt',0);
		$maps['is_read'] = array('eq',0);
		$domain_url = get_domain();
		$s = S($domain_url.'_qn_admin_auth');
		$this->assign('addons_config', $config);
		if($config['display']){
			$info['mysqlsize']  =   $this->mysql_db_size();
			$this->assign('info',$info);
			$this->assign('license', $s['msg']);
			$this->display('widget');
		}
	}
}