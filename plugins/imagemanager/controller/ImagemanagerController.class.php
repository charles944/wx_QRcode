<?php

namespace plugins\imagemanager\controller;
use home\controller\AddonsController;
//use Admin\Controller\PluginsController;

class ImagemanagerController extends AddonsController{
	public function imagemanager(){
		$name          = I("name");
		$id            = I("id");
		$times         = I("times", 0, "intval");
		$type		   = I("t");
		$where         = array();
		if (!empty($times)) {
			$where = $this->getTimeMap($times);
		}
		$strTimes      = $this->getPictureTimes();
		$PictureResult = $this->getAllPictureData($where);
		$config = get_addon_config("Imagemanager");
		
		$this->assign("addon_path", "/plugins/imagemanager/");
		$this->assign("curtime",    $times);
		$this->assign("strTimes",   $strTimes);
		$this->assign("name",       $name);
		$this->assign("id", $id);
		$this->assign("t", $type);
		$this->assign("config", $config);
		$this->assign($PictureResult);
		$this->display(T('Plugins://Imagemanager@Imagemanager/index'));
	}
        
	public function deleteImage() {
		$config = get_addon_config("Imagemanager");
		if ($config['delete_switch'] != 1) {
			$this->error('没有开启删除选项！');
		}
		$id = I("id", 0, "intval");
		if ($config['delete_mode'] == 1) {
			$pic = M("Picture")->find($id);
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . getRootUrl() . $pic['path'])) {
				@unlink($_SERVER['DOCUMENT_ROOT'] . getRootUrl() . $pic['path']);
			}
			M("Picture")->delete($id);
		}else {
			M("Picture")->where(array('id' => $id))->setField("status", 0);
		}
		$this->success('ok');
	}
	
	/**
	 * @return type
	 */
	private function getAllPictureData (array $where = array()) {
		$config = get_addon_config("Imagemanager");
		$listrow = 15;
		$p = I("page", 1, "intval");
		if (is_numeric($config['page_size']) && $config['page_size'] > 0) {
			$listrow = intval($config['page_size']);
		}
		$result['count'] = $count = M("Picture")->where($where)->count('id');
		$page = new \think\Page($count, $listrow);
		$result['_page'] = $page->show();
		$result['_list'] = M("Picture")->where($where)->where("status=1")->page($p, $listrow)->select();
		return $result;
	}
	
	/**
	 * 获取所有图片上传时间去重复
	 * @param string $format    时间格式
	 * @return array
	 */
	private function getPictureTimes ($format = "Y年m月") {
		$times = M("Picture")->field("create_time")->select();
		$strTimes = array();
		foreach ($times as $time) {
		   $strTimes[$time['create_time']] = date($format, $time['create_time']); 
		}
		return array_unique($strTimes);
	}
	
	private function getTimeMap ($time) {
		$start_time = strtotime(date("Y-m-1 00:00:00", $time));
		$end_time   = strtotime(date("Y-m-t 23:59:59", $time));
		$where['create_time'] = array("BETWEEN", array($start_time, $end_time));
		return $where;
	}
}
