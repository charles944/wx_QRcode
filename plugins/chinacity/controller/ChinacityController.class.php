<?php
namespace plugins\chinacity\controller;
use home\controller\AddonsController;
/**
 * 中国省市区三级联动插件
 */
class ChinacityController extends AddonsController{
	
	//获取中国省份信息
	public function getprovince(){
		if (IS_AJAX){
			$pid = I('pid');  //默认的省份id

			if( !empty($pid) ){
				//$map['id'] = $pid;
			}
			$map['level'] = 1;
			$map['upid'] = 0;
			$list = D('Plugins://Chinacity/District')->_list($map);

			$data = "<option value =''>-省份-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $pid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//获取城市信息
	public function getcity(){
		if (IS_AJAX){
			$cid = I('cid');  //默认的城市id
			$pid = I('pid');  //传过来的省份id

			if( !empty($cid) ){
				//$map['id'] = $cid;
			}
			$map['level'] = 2;
			$map['upid'] = $pid;

			$list = D('Plugins://Chinacity/District')->_list($map);

			$data = "<option value =''>-城市-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $cid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//获取区县市信息
	public function getdistrict(){
		if (IS_AJAX){
			$did = I('did');  //默认的城市id
			$cid = I('cid');  //传过来的城市id

			if( !empty($did) ){
				//$map['id'] = $did;
			}
			$map['level'] = 3;
			$map['upid'] = $cid;

			$list = D('Plugins://Chinacity/District')->_list($map);

			$data = "<option value =''>-州县-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $did == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

}