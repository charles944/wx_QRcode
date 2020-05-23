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
		<a href=""><cite><?php echo (htmlspecialchars($title)); if($suggest): ?>（<?php echo (htmlspecialchars($suggest)); ?>）<?php endif; ?></cite></a>
	</div>
</div>
<div class="layui-fluid">
	<div class="layui-card">
	<div class="layui-card-body">
		<?php foreach($searches as $search){ if($_REQUEST[$search['name']]) { $show=1; } } ?>
		<div style="margin-bottom: 10px; display:none" <?php if(($show) == "1"): ?>class="show"<?php endif; ?> id="search_form">
		<form id="searchForm" method="get" action="<?php echo ($searchPostUrl); ?>" class="layui-form layui-form-pane form-dont-clear-url-param">
			<?php if(is_array($searches)): $i = 0; $__LIST__ = $searches;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$search): $mod = ($i % 2 );++$i;?><!--判断搜索选项是TEXT还是SELECT-->
<?php switch($search['type']): case "select": ?><div class="layui-form-item">
	<label class="layui-form-label"><?php echo ($search["title"]); ?></label>
	<div class="layui-input-inline">
	  <select name="<?php echo ($search['name']); ?>" lay-filter="aihao">
		<option value="">全部</option>
		<?php if(is_array($search['arrvalue'])): $i = 0; $__LIST__ = $search['arrvalue'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$svo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($svo["id"]); ?>" <?php if(($svo["id"]) == $_GET[$search['name']]): ?>selected<?php endif; ?>><?php echo ($svo["value"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	  </select>
	</div>
	<div class="layui-form-mid layui-word-aux"><?php echo ($search["des"]); ?></div>
</div><?php break;?>
<?php case "time": $value = I($search['name']); if($value){ $value = time_format($value); } ?>
<div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label"><?php echo ($search["title"]); ?></label>
      <div class="layui-input-block">
	    <input type="hidden" name="<?php echo ($search['name']); ?>" value="<?php echo ($value); ?>"/>
        <input type="text" name="<?php echo ($search['name']); ?>" id="date-<?php echo ($search['name']); ?>" autocomplete="off" class="layui-input date_layui_element" value="<?php echo ($value); ?>">
      </div>
    </div>
	<div class="layui-form-mid layui-word-aux"><?php echo ($search["des"]); ?></div>
</div><?php break;?>
<?php default: ?>
<div class="layui-form-item">
	<label class="layui-form-label"><?php echo ($search["title"]); ?></label>
	<div class="layui-input-inline">
		<input type="text" name="<?php echo ($search["name"]); ?>" lay-verify="required" placeholder="请输入" value="<?php echo I($search['name']);?>" autocomplete="off" class="layui-input">
	</div>
    <div class="layui-form-mid layui-word-aux"><?php echo ($search["des"]); ?></div>
</div><?php endswitch; endforeach; endif; else: echo "" ;endif; ?>
			<div class="layui-form-item">
				  <input type="submit" class="layui-btn layui-btn-sm green" value="确定"/>
			</div>
			
		</form>
		<div style="border-top:1px solid #ccc;border-bottom: 1px solid white"></div>
		</div>
		<div class="portlet">
			<?php if(!empty($suggest)): ?><div class="explanation" id="explanation" style="margin-bottom:10px">
				<div class="ex_tit"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示"></span></div>
				<p><?php echo ($suggest); ?></p>
			</div><?php endif; ?>
			<div class="portlet-title">
				<div class="actions">
					<?php if(count($searches) > 0): ?><button class="layui-btn layui-btn-xs toggle_search fbutton" id=""><i class="layui-icon">&#xe615;</i> 搜索</button><?php endif; ?>
					<?php if(is_array($buttonList)): $i = 0; $__LIST__ = $buttonList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$button): $mod = ($i % 2 );++$i;?><<?php echo ($button["tag"]); ?> <?php echo ($button["attr"]); ?>><?php echo (op_h($button["title"])); ?></<?php echo ($button["tag"]); ?>><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
				<?php foreach($selects as $select){ if($_REQUEST[$select['name']]){ $show=1; } } ?>
				<!-- 选择框select -->
				<div class="actions">
					<form id="selectForm" method="get" action="<?php echo ($selectPostUrl); ?>" class="layui-form layui-form-pane filter">
						<?php if(is_array($selects)): $i = 0; $__LIST__ = $selects;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$select): $mod = ($i % 2 );++$i;?><div class="oneselect">
								<div class="title"><?php echo ($select["title"]); ?></div>
								<div class="select_box">
								<select name="<?php echo ($select['name']); ?>" data-role="select_text" lay-filter="select_text" class="form-control input-small">
									<?php if(is_array($select['arrvalue'])): $i = 0; $__LIST__ = $select['arrvalue'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$svo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($svo["id"]); ?>" <?php if(($svo["id"]) == $_GET[$select['name']]): ?>selected<?php endif; ?>><?php echo ($svo["value"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
								</div>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
					</form>
				</div>
				<div class="clear"></div>
			</div>
			<div class="portlet-body">
				<div class="table-scrollable layui-form">
					<table class="layui-table" lay-skin="line">
						<thead>
						<tr>
						<th width="2%">
							<input type="checkbox" class="group-checkable check-all" lay-skin="primary" lay-filter="allChoose">
						</th>
						<?php if(is_array($keyList)): $i = 0; $__LIST__ = $keyList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i;?><th><?php echo (htmlspecialchars($field["title"])); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
						</tr>
						</thead>
						<tbody>
							<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$e): $mod = ($i % 2 );++$i;?><tr>
									<td class="check">
										<input name="ids[]" type="checkbox" value="<?php echo ($e['id']); ?>" class="ids" lay-skin="primary" lay-filter="ids"/>
									</td>
									<?php if(is_array($keyList)): $i = 0; $__LIST__ = $keyList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i;?><td><?php echo ($e[$field['name']]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
				</div>
					<!-- 分页 -->
				<div class="layui-table-page">
					<?php echo ($pagination); ?>
				</div>
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