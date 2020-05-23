<?php
namespace admin\controller;
use admin\builder\AdminConfigBuilder;

class HomeController extends AdminController
{
	public function config()
	{
		$builder = new AdminConfigBuilder();
		$data = $builder->handleConfig();

		$data['OPEN_LOGIN_PANEL'] = $data['OPEN_LOGIN_PANEL'] ? $data['OPEN_LOGIN_PANEL'] : 1;

		$builder->title('首页设置');
		$builder->keyMultiImage('PIC1', '图片');
		$builder->keyTextArea('URL1', '链接','链接以换行隔开');
		$builder->keyTextArea('TITLE1', '标题','标题以换行隔开');
		$builder->keyRadio('TARGET1', '新窗口打开', '', array('_blank' => '新窗口', '_self' => '本窗口'));
		$builder->keyText('SLIDE_WIDTH','幻灯宽度','单位：px');
		$builder->keyText('SLIDE_HEIGHT','幻灯高度','单位：px');
		$builder->keyRadio('SLIDE_IMG_TYPE','图片模式','是否裁切', array('0' => '原图', '1' => '自动裁切'));
		$builder->group('首页幻灯', 'PIC1,URL1,TITLE1,TARGET1,SLIDE_IMG_TYPE,SLIDE_WIDTH,SLIDE_HEIGHT');
		
		$modules = D('Common/Module')->getAll();
		foreach ($modules as $m) {
			if ($m['is_setup'] == 1 && $m['entry'] != '') {
				if (file_exists(APP_PATH . $m['name'] . '/widget/HomeBlockWidget.class.php')) {
					$module[] = array('data-id' => $m['name'], 'title' => $m['alias']);
				}
			}
		}
		$module[] = array('data-id' => 'slider', 'title' => '轮播');

		$default = array(array('data-id' => 'disable', 'title' => '禁用', 'items' => $module), array('data-id' => 'enable', 'title' => '启用', 'items' => array()));
		$builder->keyKanban('BLOCK', '展示模块');
		$data['BLOCK'] = $builder->parseKanbanArray($data['BLOCK'], $module, $default);
		$builder->group('展示模块', 'BLOCK');

		$show_blocks = get_kanban_config('BLOCK_SORT', 'enable', array(), 'Home');

		$builder->buttonSubmit();
		$builder->data($data);
		$builder->display();
	}
}
