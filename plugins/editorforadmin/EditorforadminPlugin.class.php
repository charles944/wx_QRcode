<?php

namespace plugins\editorforadmin;
use common\controller\Plugin;

/**
 * 编辑器插件
 * @author
 */

class EditorforadminPlugin extends Plugin{

	public $info = array(
		'name'=>'editorforadmin',
		'title'=>'后台编辑器',
		'description'=>'用于增强整站长文本的输入和显示',
		'status'=>1,
		'author'=>'靑年',
		'version'=>'1.2.1'
	);

	public function install(){
		return true;
	}

	public function uninstall(){
		return true;
	}

	//编辑器挂载的后台文档模型文章内容钩子
	public function admineditor($data){
		$this->assign('addons_data', $data);
		$this->assign('addons_config', $this->getConfig());
		$this->display('content');
	}
}
