<?php
namespace home\controller;
use think\Controller;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 * @author 乾坤网络有限公司
 */
class IndexController extends Controller
{
	/** 空操作，用于输出404页面 **/
	public function _empty(){
		$this->redirect('Index/index');
	}
	
	/** assignSelf  输出当前登录用户信息 **/
	private function assignSelf()
	{
		$self = query_user(array('title', 'avatar128','avatar64', 'nickname', 'uid', 'space_url', 'icons_html', 'rank_link'));
		$this->assign('self', $self);
	}
	
	//系统首页
	public function index()
	{
		$uid = is_login();
		if(empty($uid)){
			$this->redirect('home/member/login');
		}
		hook('homeindex');
		$default_url = C('DEFUALT_HOME_URL');//获得配置，如果为空则显示聚合，否则跳转
		if ($default_url != ''&&strtolower($default_url)!='home/index/index') {
			redirect(get_nav_url($default_url));
		}
	
		$show_blocks = get_kanban_config('BLOCK', 'enable', array(), 'Home');
	
		$this->assign('showBlocks', $show_blocks);
		$enter = modC('ENTER_URL', '', 'Home');
		$this->assign('enter', get_nav_url($enter));
		$this->display();
	}
	
	protected function _initialize()
	{
		/*读取站点配置*/
		/* $config = api('Config/lists');
		C($config); */ //添加配置

		if (!C('WEB_SITE_CLOSE')) {
			$this->error(C('WEB_SITE_CLOSE_INFO'));
		}
		$this->assignSelf();
	}

}