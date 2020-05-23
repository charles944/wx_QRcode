<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<!--[if IE 8]> <html lang="zh" class="ie8 no-js" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if IE 9]> <html lang="zh" class="ie9 no-js" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title>首页 - <?php echo modC('WEB_SITE_NAME','','CONFIG');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta content="<?php echo modC('WEB_SITE_NAME','','CONFIG');?>" name="description"/>
<meta content="<?php echo modC('WEB_SITE_NAME','','CONFIG');?>" name="author"/>
<link rel="shortcut icon" href="/favicon.ico"/>
<link href="/app/admin/static/layui/css/layui.css" rel="stylesheet" type="text/css" />
<link href="/app/admin/static/css/global_new.css" rel="stylesheet" type="text/css" />
<link href="/app/admin/static/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/app/admin/static/js/jquery-1.11.2.js"></script>
<script type="text/javascript" src="/app/admin/static/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/app/admin/static/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/app/admin/static/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/app/admin/static/js/jquery.bgColorSelector.js"></script>
<script type="text/javascript" src="/app/admin/static/layui/layui.js"></script>
<script>
$(function(){
layui.cache.user = {username: '游客',uid: -1};
layui.config({version: "2.0.1",base: '/app/admin/static/mods/'}).extend({qn: 'index', echarts: 'echarts', echartsTheme: 'echartsTheme'}).use('qn');
});
</script>
<script type="text/javascript">
var ThinkPHP = window.Think = {
	"ROOT": "", //当前网站地址
	"APP": "/index.php?s=", //当前项目地址
	"PUBLIC": "/public", //项目公共目录地址
	"DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
	"MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
	"VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"],
	'URL_MODEL': "<?php echo C('URL_MODEL');?>",
}
</script>
<link rel="shortcut icon" href="/favicon.ico"/>


<?php $user_arr = array(); $main_arr = array(); $other_arr = array(); $qk_arr = array(); $pay_arr = array(); ?>
<?php if(is_array($__SMENU__["main"])): $key = 0; $__LIST__ = $__SMENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($key % 2 );++$key; if(stripos($menu['url'],'cloud') !== false){ $qk_arr[] = $menu; }else if(stripos($menu['url'],'index/') !== false){ $home_arr[] = $menu; }else if(stripos($menu['url'],'pay/') !== false){ $pay_arr[] = $menu; }else if(stripos($menu['url'],'config/') !== false || stripos($menu['url'],'actionlimit') !== false || stripos($menu['url'],'authorize') !== false){ $main_arr[] = $menu; }else if(stripos($menu['url'],'user/') !== false || stripos($menu['url'],'message/') !== false || stripos($menu['url'],'invite/') !== false){ $user_arr[] = $menu; }else{ $other_arr[] = $menu; } endforeach; endif; else: echo "" ;endif; ?>
</head>
<body class="layui-layout-body">
<div class="bgSelector"></div>
<div id="qn_app" class="">
<div class="layui-layout layui-layout-admin">
	<div class="layui-header">
		<div class="layui-logo" lay-href="">
		  <a><?php echo modC('WEB_SITE_NAME','','Config');?></a> 
		</div>
		<ul class="layui-nav layui-layout-left">
    <li class="layui-nav-item qn-flexible">
        <a href="javascript:;" qn-event="flexible" title="侧边伸缩">
          <i class="layui-icon layui-icon-shrink-right" id="qn_app_flexible"></i>
        </a>
    </li>
	
	<li class="layui-nav-item layui-this layui-hide-xs layui-hide-sm layui-show-md-inline-block"><a href="<?php echo U('/manage');?>" data-show="home">控制面板</a></li>
	<li class="layui-nav-item layui-hide-xs layui-hide-sm layui-show-md-inline-block">
        <a href="/" target="_blank" title="">
          前台
        </a>
    </li>
    <span class="layui-nav-bar" style="width: 0px; left: 0px; opacity: 0;"></span>
</ul>
<ul class="layui-nav layui-layout-right" lay-filter="layout-right">
	<?php $common_header_user = query_user(array('nickname','avatar64')); $mymsgmap['to_uid'] = UID; $mymsgmap['is_read'] = 0; $mymsgcount = M('Message')->where($mymsgmap)->count(); ?>
	<li class="layui-nav-item layui-hide-xs">
		<a href="<?php echo U('user/mymsg');?>" qn-event="message">
			<i class="layui-icon layui-icon-notice"></i>
			<?php if($mymsgcount > 0): ?><span class="layui-badge-dot"></span><?php endif; ?>
		</a>
	</li>
	<li class="layui-nav-item layui-hide-xs">
		<a href="javascript:void(0);" id="trace_show">
			<i class="layui-icon" style="font-size:20px">&#xe61b;</i>
		</a>
	</li>
	<li class="layui-nav-item layui-hide-xs">
		<a href="javascript:;" qn-event="note">
			<i class="layui-icon layui-icon-note"></i>
		</a>
	</li>
	<script>
	function clear_cache() {$.get('/clearcache.php');layer.msg('清理缓存成功',{icon: 1, time: 1000, fixed: true, offset: '80%'});}
	</script>
	<li class="layui-nav-item" style="margin-right:40px">
		<a href="javascript:;">
			<cite><?php echo ($common_header_user["nickname"]); ?></cite>
		</a>
		<dl class="layui-nav-child">
			<dd><a ><?php if(UID == 1): ?>超级管理员<?php else: echo ($group_name); endif; ?></a></dd>
			<dd><a href="<?php echo U('user/account');?>">基本资料</a></dd>
			<dd><a href="<?php echo U('action/actionlog');?>">操作日志</a></dd>
			<dd><a href="javascript:void(0);" onclick="clear_cache();">清理缓存</a></dd>
			<dd><a id="lock_screen" href="javascript:void(0);">锁定屏幕</a></dd>
			<hr>
			<dd><a href="<?php echo U('public/logout');?>" class="ajax-get">退出</a></dd>
		</dl>
	</li>
</ul>
	</div>

	<div class="qn-fixed" lay-templateid="TPL_layout">
		<!-- 左侧主菜单 -->
<div class="layui-side layui-side-menu">
	<div class="layui-side-scroll">
		<ul id="qn_menuItemElem" lay-filter="qn-system-menu">
			<li class=""><a href="javascript:;" data-show="home">首页</a></li>
			<li class=""><a href="javascript:;" data-show="user">用户</a></li>
			<li class=""><a href="javascript:;" data-show="pay">财务</a></li>
			<li class=""><a href="javascript:;" data-show="setting">设置</a></li>
			<?php if(is_array($other_arr)): $key = 0; $__LIST__ = $other_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($key % 2 );++$key; $menu_sub_tmp = explode('/',$menu['url']); if(!empty($menu_sub_tmp)){ $menu_sub = strtolower($menu_sub_tmp[0]); } ?>
				<li class="">
					<a href="javascript:;" data-show="mod<?php echo ($menu_sub); ?>" style=""><?php echo ($menu["title"]); ?></a>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>
<!-- 根据二级菜单情况，自动切换伸缩状态 -->
<!-- 左侧子菜单 -->
<div class="layui-side layui-side-child">
	<div class="layui-side-scroll" id="qn_menuContentElem">
		<!-- 首页管理 -->
		<div class="layui-menu-item" data-show="home">
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<?php if(is_array($home_arr)): $ka = 0; $__LIST__ = $home_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($ka % 2 );++$ka;?><li class="layui-nav-item layui-nav-itemed" style="" data-show="home-<?php echo ($ka); ?>">
						<a href="javascript:;"><?php echo ($menu["title"]); ?><span class="layui-nav-more"></span></a>
						<?php if(!empty($menu['child'])): ?><dl class="layui-nav-child">
						<?php if(is_array($menu['child'])): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if(!empty($sub_menu)): $j=0; ?>
							<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu): $mod = ($i % 2 );++$i; $j++; ?>
							<dd class="<?php echo ((isset($smenu["class"]) && ($smenu["class"] !== ""))?($smenu["class"]):''); ?>" data-show="home-<?php echo ($ka); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu["url"])); ?>"><i class="<?php echo ($smenu["icon"]); ?>"></i><?php echo ($smenu["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</dl><?php endif; ?>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		
		<div class="layui-menu-item " data-show="user">
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<!-- 用户管理 -->
				<?php if(is_array($user_arr)): $ka = 0; $__LIST__ = $user_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($ka % 2 );++$ka;?><li class="layui-nav-item" style="" data-show="user-<?php echo ($ka); ?>">
						<a href="javascript:;"><i class="<?php echo ($menu["icon"]); ?>"></i><?php echo ($menu["title"]); ?></a>
						<?php if(!empty($menu['child'])): ?><dl class="layui-nav-child">
						<?php if(is_array($menu['child'])): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if(!empty($sub_menu)): $j=0; ?>
							<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu): $mod = ($i % 2 );++$i; $j++; ?>
							<dd class="<?php echo ((isset($smenu["class"]) && ($smenu["class"] !== ""))?($smenu["class"]):''); ?>" data-show="user-<?php echo ($ka); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu["url"])); ?>"><i class="<?php echo ($smenu["icon"]); ?>"></i><?php echo ($smenu["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</dl><?php endif; ?>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div class="layui-menu-item " data-show="pay" >
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<!-- 充值管理 -->
				<?php if(is_array($pay_arr)): $ka = 0; $__LIST__ = $pay_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($ka % 2 );++$ka;?><li class="layui-nav-item"  style="" data-show="pay-<?php echo ($ka); ?>">
						<a href="javascript:;"><?php echo ($menu["title"]); ?></a>
						<?php if(!empty($menu['child'])): ?><dl class="layui-nav-child">
						<?php if(is_array($menu['child'])): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if(!empty($sub_menu)): $j=0; ?>
						<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu): $mod = ($i % 2 );++$i; $j++; ?>
						<dd class="<?php echo ((isset($smenu["class"]) && ($smenu["class"] !== ""))?($smenu["class"]):''); ?>" data-show="pay-<?php echo ($ka); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu["url"])); ?>"><i class="<?php echo ($smenu["icon"]); ?>"></i><?php echo ($smenu["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</dl><?php endif; ?>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
				
		<div class="layui-menu-item " data-show="setting">
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<!-- 系统设置 -->
				<?php if(is_array($main_arr)): $ka = 0; $__LIST__ = $main_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($ka % 2 );++$ka;?><li class="layui-nav-item "  style="" data-show="setting-<?php echo ($ka); ?>">
						<a href="javascript:;"><i class="<?php echo ($menu["icon"]); ?>"></i><?php echo ($menu["title"]); ?></a>
						<?php if(!empty($menu['child'])): ?><dl class="layui-nav-child">
						<?php if(is_array($menu['child'])): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if(!empty($sub_menu)): $j=0; ?>
						<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu): $mod = ($i % 2 );++$i; $j++; ?>
						<dd class="<?php echo ((isset($smenu["class"]) && ($smenu["class"] !== ""))?($smenu["class"]):''); ?>" data-show="setting-<?php echo ($ka); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu["url"])); ?>"><i class="<?php echo ($smenu["icon"]); ?>"></i><?php echo ($smenu["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</dl><?php endif; ?>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
				<!-- 接口设置 -->
				<?php if(!empty($qk_arr)): if(is_array($qk_arr)): $ka = 0; $__LIST__ = $qk_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($ka % 2 );++$ka;?><li class="layui-nav-item"  style="" data-show="qn-<?php echo ($ka); ?>">
					<a href="javascript:;"><?php echo ($vo["title"]); ?></a>
					<dl class="layui-nav-child">
					<?php if(is_array($vo['child'])): $i = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu1): $mod = ($i % 2 );++$i; if(!empty($sub_menu1)): $j=0; ?>
					<?php if(is_array($sub_menu1)): $i = 0; $__LIST__ = $sub_menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu1): $mod = ($i % 2 );++$i; $j++; ?>
					<dd class="<?php echo ((isset($smenu1["class"]) && ($smenu1["class"] !== ""))?($smenu1["class"]):''); ?>" data-show="qn-<?php echo ($ka); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu1["url"])); ?>"><i class="<?php echo ($smenu1["icon"]); ?>"></i><?php echo ($smenu1["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
					</dl>
					</li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				<!-- 插件后台列表 -->
				<?php if(!empty($_extra_menu)): ?><li class="layui-nav-item" data-show="addons-1" style="">
						<a href="javascript:;"><i class=""></i> 插件后台配置</a>
						<?php if(!empty($_extra_menu)): ?><dl class="layui-nav-child">
								<?php $j=0; ?>
								<?php if(is_array($_extra_menu)): $i = 0; $__LIST__ = $_extra_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$extra_vo): $mod = ($i % 2 );++$i; $j++; ?>
									<dd class="<?php echo ((isset($extra_vo["class"]) && ($extra_vo["class"] !== ""))?($extra_vo["class"]):''); ?>" data-show="addons-1-<?php echo ($j); ?>"><a href="<?php echo ($extra_vo["url"]); ?>"><i class=""></i><?php echo ($extra_vo["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
							</dl><?php endif; ?>
					</li><?php endif; ?>
			</ul>
		</div>

		<?php if(is_array($other_arr)): $key = 0; $__LIST__ = $other_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($key % 2 );++$key; $menu_sub_tmp = explode('/',$menu['url']); if(!empty($menu_sub_tmp)){ $menu_sub = strtolower($menu_sub_tmp[0]); } ?>
		<div class="layui-menu-item " data-show="mod<?php echo ($menu_sub); ?>">
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<!-- 模块设置 -->
				
				<li class="layui-nav-item" data-show="mod<?php echo ($menu_sub); ?>" style="">
					<a href="javascript:;"><i class="<?php echo ($menu["icon"]); ?>"></i> <?php echo ($menu["title"]); ?></a>
					<?php if(!empty($menu['child'])): ?><dl class="layui-nav-child">
						<?php if(is_array($menu['child'])): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if(!empty($sub_menu)): $j=0; ?>
						<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$smenu): $mod = ($i % 2 );++$i; $j++; ?>
						<dd class="<?php echo ((isset($smenu["class"]) && ($smenu["class"] !== ""))?($smenu["class"]):''); ?>" data-show="mod<?php echo ($menu_sub); ?>-<?php echo ($j); ?>"><a href="<?php echo (u($smenu["url"])); ?>"><i class="<?php echo ($smenu["icon"]); ?>"></i><?php echo ($smenu["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</dl><?php endif; ?>
				</li>
				
			</ul>
		</div><?php endforeach; endif; else: echo "" ;endif; ?>

		<div class="layui-menu-item ">
			<ul class="layui-nav layui-nav-tree" lay-filter="qn-side-child">
				<span class="layui-nav-bar"></span>
			</ul>
		</div>
	</div>
</div>
	</div>
  
	<div class="layui-body" id="qn_app_body">
		
<div class="layui-card qn-header">
	<div class="layui-breadcrumb">
		<a href="">首页</a>
		<a href=""><cite>用户列表</cite></a>
	</div>
</div>
<div class="layui-fluid">
	<div class="layui-card">
		<div class="layui-card-body">
		
<?php if($_REQUEST['nickname']||$_REQUEST['email']||$_REQUEST['qq']||$_REQUEST['mobile']) { $show=1; } ?>
<div style="margin-bottom: 10px; display:none" <?php if(($show) == "1"): ?>class="show"<?php endif; ?> id="search_form">
<style>
.tb_search td{padding: 5px 10px;}
</style>
<form id="searchForm" method="get" action="<?php echo U();?>" class="layui-form layui-form-pane form-dont-clear-url-param">
    <div class="search-form  cf " style="margin-bottom: 10px">		 
		<div class="layui-form-item">
			<label class="layui-form-label">用户昵称或用户ID</label>
			<div class="layui-input-inline">
				 <input type="text" name="nickname" id="nickname" autocomplete="off" class="layui-input" value="<?php echo I('nickname');?>" />
			</div>
		</div>
		
		<div class="layui-form-item">
			<label class="layui-form-label">邮箱</label>
			<div class="layui-input-inline">
				 <input type="text" name="email" id="email" autocomplete="off" class="layui-input" value="<?php echo I('email');?>" />
			</div>
		</div>
		
		<div class="layui-form-item">
			<label class="layui-form-label">手机</label>
			<div class="layui-input-inline">
				 <input type="text" name="mobile" id="mobile" autocomplete="off" class="layui-input" value="<?php echo I('mobile');?>" />
			</div>
		</div>

		<div class="layui-form-item">
			<label class="layui-form-label">QQ</label>
			<div class="layui-input-inline">
				 <input type="text" name="qq" id="qq" autocomplete="off" class="layui-input" value="<?php echo I('qq');?>" />
			</div>
		</div>
		 
		<div class="layui-form-item">
			<label class="layui-form-label">排序</label>
			<div class="layui-input-inline">
			<?php $vst = I('vsort',0); ?>
				 <select style="float: none" name="vsort" class="search-input form-control form-input-width" >
					 <option value="uid" <?php if(($vst) == "uid"): ?>selected="selected"<?php endif; ?>>ID</option>
					 <option value="reg_time" <?php if(($vst) == "reg_time"): ?>selected="selected"<?php endif; ?>>注册日期</option>
					 <option value="last_login_time" <?php if(($vst) == "last_login_time"): ?>selected="selected"<?php endif; ?>>登录日期</option>
					 <option value="lower_num" <?php if(($vst) == "lower_num"): ?>selected="selected"<?php endif; ?>>下线数量</option>
					 <option value="login" <?php if(($vst) == "lower_num"): ?>selected="selected"<?php endif; ?> >登录次数</option>
					 <option value="lower_gold_coin" <?php if(($vst) == "lower_gold_coin"): ?>selected="selected"<?php endif; ?>>下线提成</option>
					 <option value="pay_money" <?php if(($vst) == "pay_money"): ?>selected="selected"<?php endif; ?>>总提现</option>
					 <?php if(is_array($scoretypelist)): $i = 0; $__LIST__ = $scoretypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v['mark']); ?>" <?php if(($vst) == $v['mark']): ?>selected="selected"<?php endif; ?>><?php echo getScoreTypeName($v['mark']);?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				 </select>
			</div>
		</div>
		 
		<div class="layui-form-item">
			<label class="layui-form-label">升降</label>
			<div class="layui-input-inline">
				<?php $st = I('st',0); ?>
				 <select style="float: none" name="st" class="search-input form-control form-input-width" >
					 <option value="asc" <?php if(($st) == "asc"): ?>selected="selected"<?php endif; ?> >升序</option>
					 <option value="desc" <?php if(($st) == "desc"): ?>selected="selected"<?php endif; ?>>降序</option>
				 </select>
			</div>
		</div>
		<div class="layui-form-item">
			  <input type="submit" class="layui-btn layui-btn-sm green" value="确定"/>
			  <button class="layui-btn layui-btn-sm layui-btn-primary toggle_search" id="">关闭</button>
		</div>
	</div>
</form>
<div style="border-top:1px solid #ccc;border-bottom: 1px solid white"></div>
</div>
<div class="portlet">
	<div class="portlet-title">
		<div class="actions">
				<button class="layui-btn layui-btn-xs toggle_search fbutton"><i class="layui-icon">&#xe615;</i> 搜索</button>
				<a class="layui-btn layui-btn-xs fbutton" href="<?php echo U('User/mingxi');?>">查看收入明细</a>
				<a class="layui-btn layui-btn-xs fbutton green" href="<?php echo U('User/add');?>">新 增 用 户</a>
                
	            <button class="ajax-post layui-btn layui-btn-xs fbutton" url="<?php echo U('User/changeStatus',array('method'=>'resumeUser'));?>" target-form="ids"><i class="fa fa-check"></i> 启 用</button>
	            <button class="ajax-post layui-btn layui-btn-xs fbutton" url="<?php echo U('User/changeStatus',array('method'=>'forbidUser'));?>" target-form="ids"><i class="fa fa-ban"></i> 冻 结</button>
	            <button class="layui-btn layui-btn-xs ajax-post fbutton" url="<?php echo U('User/changeStatus',array('method'=>'deleteUser'));?>" target-form="ids"><i class="fa fa fa-trash-o"></i> 删 除</button>
	            <a class="layui-btn layui-btn-xs fbutton" href="<?php echo U('User/import');?>"><i class="fa fa-file-excel-o"></i> 导入会员</a>
	            <button class="layui-btn layui-btn-xs ajax-post fbutton" data-confirm="密码重置为“123456”" url="<?php echo U('User/initPass');?>" target-form="ids"><i class="fa fa-refresh"></i> 重置密码：123456</button>
                <a class="layui-btn layui-btn-xs fbutton" href="<?php echo U('Userlog/index');?>"><i class="fa fa-search"></i> 查看违规记录</a>
				
				<!-- <button data-title="群发邮件" target-form="ids" can_null="true" class="layui-btn layui-btn-xs fbutton ajax-post layui-btn layui-btn-xs" data-url="<?php echo U('Message/sendMessage', array('user_group' => $aUserGroup, 'role' => $aRole));?>" data-role="modal" style="border-color: rgb(70, 142, 51); color: rgb(70, 142, 51);"><i class="layui-icon"></i> 群发邮件</button> -->
				
				<!-- <button data-title="群发短信" target-form="ids" can_null="true" class="layui-btn layui-btn-xs fbutton ajax-post layui-btn layui-btn-xs" data-url="<?php echo U('Message/sendMessage', array('user_group' => $aUserGroup, 'role' => $aRole));?>" data-role="modal" style="border-color: rgb(70, 142, 51); color: rgb(70, 142, 51);"><i class="layui-icon"></i> 群发短信</button> -->
		</div>
		<?php foreach($selects as $select){ if($_REQUEST[$select['name']]){ $show=1; } } ?>
            <!-- 选择框select -->
		<div class="actions">
			<form id="selectForm" method="get" action="<?php echo U();?>" class="layui-form layui-form-pane filter">
					<div class="oneselect">
						<div class="title">显示：</div>
						<div class="select_box">
						<select name="r" data-role="select_text" lay-filter="select_text" class="form-control input-small">
								<option value="50" <?php if(($r) == "50"): ?>selected<?php endif; ?>>显示50行</option>
								<option value="100" <?php if(($r) == "100"): ?>selected<?php endif; ?>>显示100行</option>
								<option value="200" <?php if(($r) == "200"): ?>selected<?php endif; ?>>显示200行</option>
						</select>
						</div>
					</div>
					<div class="oneselect">
						<div class="title">状态：</div>
						<div class="select_box">
						<select name="dead" data-role="select_text" lay-filter="select_text" class="form-control input-small">
								<option value="0" <?php if(($dead) == "0"): ?>selected<?php endif; ?>>全部</option>
								<option value="1" <?php if(($dead) == "1"): ?>selected<?php endif; ?>>正常</option>
								<option value="2" <?php if(($dead) == "2"): ?>selected<?php endif; ?>>冻结</option>
								<option value="3" <?php if(($dead) == "3"): ?>selected<?php endif; ?>>未激活</option>
								<option value="4" <?php if(($dead) == "4"): ?>selected<?php endif; ?>>已删除</option>
						</select>
						</div>
					</div>
					<div class="oneselect">
						<div class="title">身份：</div>
						<div class="select_box">
						<select name="urole" data-role="select_text" lay-filter="select_text" class="form-control input-small">
							<?php if(is_array($user_role)): $i = 0; $__LIST__ = $user_role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($urole) == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["value"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						</div>
					</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<div class="portlet-body">
	<div class="table-scrollable layui-form">
		<table class="layui-table" lay-even lay-skin="line" lay-filter="userlist" id="">
			<thead>
			<tr>
			<th>
				<input type="checkbox" class="group-checkable" lay-skin="primary" lay-filter="allChoose">
			</th>
            <th>UID</th>
			<th>昵称/用户名</th>
            <th>基本信息</th>
            <th>邮箱/手机号</th>
            <th>注册/登录时间（次数 & IP）</th>
            <th>状态</th>
            <th width="99px">操作</th>
			</tr>
			</thead>
			<tbody>
				<?php if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr <?php switch($vo["status"]): case "0": ?>style="background-color:#FFFFE5"<?php break; case "3": ?>style="background-color:#FFE5E5"<?php break; endswitch;?>>
						<td class="check">
							<input class="ids" type="checkbox" name="ids[]" value="<?php echo ($vo["uid"]); ?>" lay-skin="primary" lay-filter="ids"/>
						</td>
						<td><?php echo ($vo["uid"]); ?></td>
						<td>
							<a href="<?php echo U('User/mingxi',array('nickname'=>$vo['uid']));?>" class="layui-btn layui-btn-sm layui-btn-danger" style="line-height:25px;height:25px;font-size:14px;padding:0 5px"><?php echo (op_t($vo["nickname"])); ?></a><br><?php echo (op_t($vo["username"])); ?><br/>
							<?php if(is_array($vo["user_role"])): $i = 0; $__LIST__ = $vo["user_role"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vx): $mod = ($i % 2 );++$i;?>身份：<?php echo ($role_arr[$vx['role_id']]); ?><br/><?php endforeach; endif; else: echo "" ;endif; ?>
						<td>
							<?php if(is_array($scoretypelist)): $i = 0; $__LIST__ = $scoretypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; echo getScoreTypeName($v['mark']);?>：<?php echo ($vo[$v['mark']]); ?><br /><?php endforeach; endif; else: echo "" ;endif; ?>
							提现：
							<a href="<?php echo U('pay/payoffer',array('title'=>$vo['username']));?>" ><?php echo ((isset($vo["pay_money"]) && ($vo["pay_money"] !== ""))?($vo["pay_money"]):0); ?> 元</a>
							<br />
							下线：
							<a href="<?php echo U('User/userlower',array('uid'=>$vo['uid']));?>" ><?php echo ((isset($vo["lower_num"]) && ($vo["lower_num"] !== ""))?($vo["lower_num"]):0); ?> 个</a>
							<br />
							提成：
							<?php echo ((isset($vo["lower_gold_coin"]) && ($vo["lower_gold_coin"] !== ""))?($vo["lower_gold_coin"]):0); ?> 元
						</td>
						<td>
							<?php echo ($vo["email"]); ?>（<?php echo ($vo["email_ver"]); ?>）
							<br />
							<?php echo ($vo["mobile"]); ?>（<?php echo ($vo["mobile_ver"]); ?>）
						</td>
						<td>
							<span>注册：<?php echo (time_format($vo["reg_time"])); ?></span><br />
							<span>登录：<?php echo (time_format($vo["last_login_time"])); ?></span><br />
							<span>登录：<?php echo ($vo["login"]); ?>次</span><br />
							<span>IP：<?php echo (long2ip($vo['last_login_ip'])); ?></span><br/>
							<span>地址：<?php echo (ipfrom(long2ip($vo['last_login_ip']))); ?></span>
						</td>
						<td><?php echo ($vo["status_txt"]); ?></td>
						<td class="handle">
							<div class="ht_tdiv">
								<a href="<?php echo U('User/expandinfo_details',array('uid'=>$vo['uid']));?>" class="layui-btn layui-btn-xs hbutton btn_see">查看资料</a>
								<a href="<?php echo U('User/initPass?ids='.$vo['uid']);?>" class="ajax-get layui-btn layui-btn-xs hbutton btn_trash">重置密码</a>
								<?php if($vo['status'] == 1): ?><a href="<?php echo U('User/changeStatus?method=forbidUser&ids='.$vo['uid']);?>" class="ajax-get layui-btn layui-btn-xs hbutton btn_trash">冻结账号</a>
								<?php elseif($vo['status'] == 0): ?>
									<a href="<?php echo U('User/changeStatus?method=resumeUser&ids='.$vo['uid']);?>" class="ajax-get layui-btn layui-btn-xs hbutton btn_see">解冻</a><?php endif; ?>
								<!-- <a href="<?php echo U('role/userlist?uid='.$vo['uid']);?>" class="authorize layui-btn layui-btn-xs hbutton btn_see">管理身份</a> -->
								<a href="<?php echo U('User/freshuserlower?ids='.$vo['uid']);?>" class="ajax-get layui-btn layui-btn-xs hbutton btn_see">刷新上下级</a>
								<a href="<?php echo U('Userlog/index?nickname='.$vo['uid']);?>" class="layui-btn layui-btn-xs hbutton btn_see">查看违规记录</a>
							</div>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			</table>
			</div>
	</div>
	<div class="layui-table-page">
	<?php echo ($_page); ?>
	</div>
</div>
</div>
</div>
</div>

	</div>
</div>
</div>
<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
  <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
  <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<?php echo hook('adminpagefooter');?>
</body>
</html>