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

<style>
.upload-img-box .upload-pre-items img {
    width: 100px;
}
.upload-img-box .upload-pre-items .opacity {
    filter: alpha(opacity=50);
    -moz-opacity: 0.5;
    -khtml-opacity: 0.5;
    background: #000;
    opacity: 0.5;
}
.upload-img-box .upload-pre-items .each{
	width:100px;
	height:100px;
    border:1px solid #f1f1f1;
	margin-right:10px;
	margin-bottom:10px;
	display:inline-block;
	position:relative;
	float:left;
}
.upload-img-box .upload-pre-items .del_btn {
    width: 90px;
    position: absolute;
	bottom:0px;
    color: #fff;
    height: 30px;
    line-height: 30px;
    cursor: pointer;
	padding-left:10px;
}
.uploadify {position:relative;}
</style>

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
		<a href=""><cite><?php echo ($title); ?></cite></a>
	</div>
</div>
<div class="layui-fluid">
	<div class="layui-card">
		<div class="layui-card-body">
		<?php use admin\model\AuthGroupModel; ?>
		<?php if(!empty($suggest)): ?><div class="explanation" id="explanation">
			<div class="ex_tit"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示"></span></div>
			<p><?php echo ($suggest); ?></p>
		</div><?php endif; ?>
		<form action="<?php echo ($savePostUrl); ?>" id="form" class="layui-form" method="post">
		<div class="layui-tab">
		  <ul class="layui-tab-title">
			<?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vGroup): $mod = ($i % 2 );++$i;?><li id="tab_1_<?php echo ($i); ?>" class="<?php if($i == 1): ?>layui-this<?php endif; ?>"><?php echo ($key); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
		  </ul>
		  <div class="layui-tab-content">
				<?php if($group){ $p = 0; ?>
				<?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vGroup): $mod = ($i % 2 );++$i; $p = $p+1; ?>
					<div class="layui-tab-item <?php if($p == 1): ?>layui-show<?php endif; ?>" id="tab_1_<?php echo ($p); ?>">
						<?php if(is_array($keyList)): $i = 0; $__LIST__ = $keyList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i; if(in_array($field['name'],$vGroup)){ ?>
							<div class="layui-form-item">
			<label class="layui-form-label"><?php echo (htmlspecialchars($field["title"])); ?></label>
			<?php if($field['name'] == 'action'): ?><div class="layui-input-inline"><p style="color: #f00;">开发人员注意：你使用了一个名称为action的字段，由于这个字段名称会与form[action]冲突，导致无法提交表单，请换用另外一个名字。</p>
				</div><?php endif; ?>
			<?php switch($field["type"]): case "text": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "color": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "tage": ?><div class="layui-inline">
						<?php if(!empty($field['value'])){ $field['value'] = json_decode($field['value'],true); } ?>
						<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $union_rules = D('Admin/AuthGroup')->where(array('status'=>1,'type' => AuthGroupModel::TYPE_ADMIN, 'id'=> $key))->getField('union_rules'); if(!empty($union_rules) && $union_rules != '""'){ $union_rule = json_decode($union_rules, true); ?>
						<div class="layui-form-item">
						<?php $kk = $key; ?>
						<input type="hidden" value="<?php echo ($option["title"]); ?>" name="tage[<?php echo ($kk); ?>][title][]" />
						<label class="layui-form-label" style="width: 100px;"><?php echo ($option["title"]); ?>：</label>
						<div class="layui-inline">
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">一级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_1 = $union_rule[strtolower($option['modulename'])][child][1]; }else{ $union_tage_1 = $field['value'][$kk][child][1]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][1]" value="<?php echo ($union_tage_1); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">二级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_2 = $union_rule[strtolower($option['modulename'])][child][2]; }else{ $union_tage_2 = $field['value'][$kk][child][2]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][2]" value="<?php echo ($union_tage_2); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">三级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_3 = $union_rule[strtolower($option['modulename'])][child][3]; }else{ $union_tage_3 = $field['value'][$kk][child][3]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][3]" value="<?php echo ($union_tage_3); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">四级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_4 = $union_rule[strtolower($option['modulename'])][child][4]; }else{ $union_tage_4 = $field['value'][$kk][child][4]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][4]" value="<?php echo ($union_tage_4); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">五级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_5 = $union_rule[strtolower($option['modulename'])][child][5]; }else{ $union_tage_5 = $field['value'][$kk][child][5]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][5]" value="<?php echo ($union_tage_5); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">六级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_6 = $union_rule[strtolower($option['modulename'])][child][6]; }else{ $union_tage_6 = $field['value'][$kk][child][6]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][6]" value="<?php echo ($union_tage_6); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">七级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_7 = $union_rule[strtolower($option['modulename'])][child][7]; }else{ $union_tage_7 = $field['value'][$kk][child][7]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][7]" value="<?php echo ($union_tage_7); ?>" placeholder="">
							</div>
						</div>
						</div>
						</div>
						<?php } endforeach; endif; else: echo "" ;endif; ?>
					</div><?php break;?>
					<?php case "hidden": ?><input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" class="" /><?php break;?>
					<?php case "readonly": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input" readonly style="border:None">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "integer": ?><div class="layui-input-inline">
						<input type="number" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" lay-verify="number" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "uid": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
       				<?php case "select": ?><div class="layui-input-inline">
       					<select class="" name="<?php echo ($field["name"]); ?>">
							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = $field['value']==$key ? 'selected' : ''; ?>
				            <option value="<?php echo ($key); ?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars($option)); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
					<div class="layui-form-mid layui-word-aux">
       				    <?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
                    <?php case "radio": ?><div class="layui-input-block">
							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $checked = $field['value']==$key ? 'checked' : ''; $inputId = "id_$field[name]_$key"; ?>
								<input type="radio" name="<?php echo ($field["name"]); ?>" value="<?php echo ($key); ?>" title="<?php echo (htmlspecialchars($option)); ?>" <?php echo ($checked); ?>><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<div class="layui-form-mid layui-word-aux">
							<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
						</div><?php break;?>
                    <?php case "icon": ?><!-- 图标选择器 --><?php break;?>
				    <?php case "singleImage": ?><div class="layui-input-block">
							<input type="button" id="image_<?php echo ($field["name"]); ?>" class="layui-upload-button singleimage" value="上传图片" />
							<input type="button" class="layui-box layui-upload-button selectImg" style="padding:0 15px" id="selectImg_<?php echo ($field["name"]); ?>" data-name="<?php echo ($field["name"]); ?>" value="浏览服务器" data-type="0" />
							
							<input type="hidden" id="url" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
							<div class="upload_picture" style="margin-top:10px;display:block;overflow:hidden;">
							<?php if(!empty($field["value"])): ?><a href="<?php echo (get_cover($field["value"],'path')); ?>" target="_blank" title="点击查看大图" style="display:block;float:left;margin-right:10px;">
									<img src="<?php echo (get_cover($field["value"],'path')); ?>" id="upload_<?php echo ($field["name"]); ?>" style="width:100px;height:100px"/>
								</a>
							<?php else: ?>
								<a href="<?php echo (get_cover($field["value"],'path')); ?>" target="_blank" title="点击查看大图" style="display:block;float:left;margin-right:10px;">
									<img src="<?php echo (get_cover($field["value"],'path')); ?>" id="upload_<?php echo ($field["name"]); ?>" style="width:100px;height:100px"/>
								</a><?php endif; ?>
							</div>
							<div class="layui-form-mid layui-word-aux">
								<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
							</div>
						</div>
						<script type="text/javascript">
							layui.use('upload', function(){
								var upload = layui.upload;
								upload.render({
									url: '<?php echo U("File/uploadPicture",array("session_id"=>session_id()));?>'
									,elem: '#image_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
									,method: 'post' //上传接口的http类型
									,accept: 'images'
									,field: '<?php echo ($field["name"]); ?>_id'
									,before: function(input){
										//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
										layer.load(2, {shade: false});
									}
									,done: function(res, index, upload){
										layer.closeAll('loading'); //关闭loading
										upload_<?php echo ($field["name"]); ?>.src = res.url.<?php echo ($field["name"]); ?>_id.path;
										$("[name='<?php echo ($field["name"]); ?>']").val(res.url.<?php echo ($field["name"]); ?>_id.id);
									}
									,error: function(res, index, upload){
										layer.closeAll('loading'); //关闭loading
										layer.msg(res.info, {icon: 2, fixed: true, offset: '80%', time:1000});
									}
								});
							});
						</script><?php break;?>
                    
				    <?php case "multiImage": ?><div class="layui-input-block multiImage controls">
							<input type="button" id="J_selectImage_<?php echo ($field["name"]); ?>" class="layui-upload-button multiimage" value="批量上传图片" />
				        	<input type="button" id="selectImg_<?php echo ($field["name"]); ?>" class="layui-box layui-upload-button selectImg" style="padding:0 15px" data-name="<?php echo ($field["name"]); ?>" value="浏览服务器" data-type="1"/>
							
				            <input class="attach" type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
				            
				            <div class="upload-img-box" style="margin-top:10px;overflow:hidden">
				                <div class="upload-pre-items popup-gallery" id="imageview_<?php echo ($field["name"]); ?>">
				                    <?php if(!empty($field["value"])): $aIds = explode(',',$field['value']); ?>
				                        <?php if(is_array($aIds)): $i = 0; $__LIST__ = $aIds;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$aId): $mod = ($i % 2 );++$i;?><div class="each">
				                                <a href="<?php echo (get_cover($aId,'path')); ?>" title="点击查看大图">
				                                    <img src="<?php echo (get_cover($aId,'path')); ?>">
				                                </a>
				                                <div class="text-center opacity del_btn" ></div>
                                        		<div onclick="admin_image.removeImage($(this),'<?php echo ($aId); ?>')"  class="text-center del_btn">删除</div>
				                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php else: ?>
										<div class="each">
											<a href="<?php echo (get_cover($aId,'path')); ?>" title="点击查看大图">
												<img src="<?php echo (get_cover($aId,'path')); ?>">
											</a>
											<div class="text-center opacity del_btn" ></div>
											<div onclick="admin_image.removeImage($(this),'<?php echo ($aId); ?>')"  class="text-center del_btn">删除</div>
										</div><?php endif; ?>
				                </div>
				            </div>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
				        </div>
						
						<script type="text/javascript">
						layui.use('upload', function(){
							var upload = layui.upload;
							upload.render({
								url: '<?php echo U("File/uploadPicture",array("session_id"=>session_id()));?>'
								,elem: '#J_selectImage_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
								,method: 'post' //上传接口的http类型
								,accept: 'images'
								,field: '<?php echo ($field["name"]); ?>_id'
								,before: function(input){
									//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
									layer.load(2, {shade: false});
								}
								,done: function(res, index, upload){
									//console.log(res);
									layer.closeAll('loading'); //关闭loading
									var div = $('#imageview_<?php echo ($field["name"]); ?>');
									admin_image.upAttachVal('add',res.url.<?php echo ($field["name"]); ?>_id.id, $("[name='<?php echo ($field["name"]); ?>_id']"));
									div.append('<div class="each"><a href="'+ res.url.<?php echo ($field["name"]); ?>_id.path +'" title="点击查看大图"><img src="'+ res.url.<?php echo ($field["name"]); ?>_id.path +'"></a><div class="text-center opacity del_btn" ></div><div onclick="admin_image.removeImage($(this),'+res.url.<?php echo ($field["name"]); ?>_id.id +')"  class="text-center del_btn">删除</div></div>');
								}
								,error: function(res, index, upload){
									layer.closeAll('loading'); //关闭loading
									layer.msg(res.info, {icon: 2, fixed: true, offset: '80%', time:1000});
								}
							});
						});
						</script><?php break;?>
                    <?php case "checkbox": ?><div class="layui-input-block">
							<?php $field['value_array'] = explode(',', $field['value']); ?>

							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $checked = in_array($key,$field['value_array']) ? 'checked' : ''; $inputId = "id_$field[name]_$key"; ?>
							<input type="checkbox" name="<?php echo ($field["name"]); ?>" value="<?php echo ($key); ?>" id="<?php echo ($inputId); ?>" title="<?php echo (htmlspecialchars($option)); ?>" data-field-name="<?php echo ($field["name"]); ?>" lay-skin="primary" <?php echo ($checked); ?>><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
                    <?php case "editor": ?><div class="layui-input-block">
							<?php echo W('Common/Ueditor/editor',array($field['name'],$field['name'],$field['value'],$field['style']['width'],$field['style']['height'],$field['config']));?>
							<!-- 
							<textarea class="layui-textarea qn-editor" name="<?php echo ($field["name"]); ?>" lay-verify="content" id="LAY_demo_editor"><?php echo ($field['value']); ?></textarea> -->
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div><?php break;?>
				    <?php case "textarea": ?><div class="layui-input-block">
							<textarea name="<?php echo ($field["name"]); ?>" class="layui-textarea"><?php echo (htmlspecialchars($field["value"])); ?></textarea>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div><?php break;?>
                    <?php case "time": if(!$field['value']){ $field['value'] = time(); } ?>
                        <div class="layui-input-inline">
							<input type="text" id="time_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"])); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input time_layui_element" />
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
                    <?php case "date": if(!$field['value']){ $field['value'] = time(); } ?>
						<div class="layui-input-inline">
							<input type="text" id="date_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"],'Y-m-d')); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input date_layui_element" />
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
				    <?php case "datetime": if(!$field['value']){ $field['value'] = time(); } ?>
						<div class="layui-input-inline">
							<input type="text" id="datetime_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"],'Y-m-d H:i:s')); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input datetime_layui_element"/>
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
				
				    <!--添加城市选择（需安装城市联动插件,css样式不好处理排版有点怪）-->
				    <?php case "city": ?><style type="text/css">
							.layui-input {display:inline-block;width: 120px;}
						</style>
						<!--修正在编辑信息时无法正常显示已经保存的地区信息-->
						<?php echo hook('j_china_city',array('province'=>$field['value']['0'],'city'=>$field['value']['1'],'district'=>$field['value']['2'],'community'=>$field['value']['3'])); break;?>
				    
				    <?php case "singleFile": ?><div class="layui-input-block">
							<input type="button" id="file_<?php echo ($field["name"]); ?>" class="layui-upload-button" value="上传文件" />
							<input type="hidden" id="url" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
							<div class="upload_file" style="margin-top:10px;display:block;overflow:hidden;">
								<?php if(!empty($field["value"])): ?><p id="upload_<?php echo ($field["name"]); ?>"><?php echo (text($field["value"])); ?></p>
								<?php else: ?>
									<p id="upload_<?php echo ($field["name"]); ?>"><?php echo (text($field["value"])); ?></p><?php endif; ?>
							</div>
							<div class="layui-form-mid layui-word-aux">
								<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
							</div>
						</div>
						<script type="text/javascript">
						layui.use('upload', function(){
							var upload = layui.upload;
							upload.render({
								url: '<?php echo U("File/uploadFile",array("session_id"=>session_id()));?>'
								,elem: '#file_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
								,method: 'post' //上传接口的http类型
								,accept: 'file'
								,field: '<?php echo ($field["name"]); ?>_id'
								,before: function(input){
									//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
									layer.load(2, {shade: false});
								}
								,done: function(res, index, upload){
									layer.closeAll('loading'); //关闭loading
									$("#upload_<?php echo ($field["name"]); ?>").html(res.url.<?php echo ($field["name"]); ?>_id.name);
									$("[name='<?php echo ($field["name"]); ?>']").val(res.url.<?php echo ($field["name"]); ?>_id.id);
								}
								,error: function(index, upload){
									layer.closeAll('loading'); //关闭loading
									layer.msg(data.info, {icon: 2, fixed: true, offset: '80%', time:1000});
								}
							});
						});
						</script><?php break;?>
				    <?php case "multiFile": echo W('Common/UploadMultiFile/render',array(array('name'=>$field['name'],'limit'=>9,'value'=>$field['value']))); break;?>
				
				    <!--弹出窗口选择并返回值（目前只支持返回ID）开始-->
				    <?php case "dataselect": ?><input type="text" name="<?php echo ($field["name"]); ?>" id="<?php echo ($field["name"]); ?>" value="<?php echo (htmlspecialchars($field["value"])); ?>" class="text input-large layui-input" style="width: 400px;display:inline-block;"/>
						<input class="btn" style="margin-left:10px" type="button" value="选择" onclick="openwin('<?php echo ($field["opt"]); ?>','600','500')">
						<script type="text/javascript">
						//弹出窗口
						function openwin(url,width,height){
							var l=window.screen.width ;
							var w= window.screen.height;
							var al=(l-width)/2;
							var aw=(w-height)/2;
							var OpenWindow=window.open(url,"弹出窗口","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width="+width+",height="+height+",top="+aw+",left="+al+"");
							OpenWindow.focus();
							if(OpenWindow!=null){ //弹出窗口关闭事件
								//if(window.attachEvent) OpenWindow.attachEvent("onbeforeunload",   quickOut);
								if(window.attachEvent) OpenWindow.attachEvent("onunload",   quickOut);
							}
						}
						//关闭触发方法
						function quickOut()
						{
							alert("窗口已关闭");
						}
						</script><?php break;?>
					<!--弹出窗口选择并返回值（目前只支持返回ID）结束-->

				    <?php case "kanban": ?><div class="layui-input-inline">
						<link rel="stylesheet" type="text/css" href="/app/admin/static/js/kanban/jquery.nestable.css"/>
				        <input type="hidden" name="<?php echo ($field["name"]); ?>" value='<?php echo json_encode($field["value"]);?>'/>
				        <div class="kanbans" id="<?php echo ($field["name"]); ?>">
				            <?php $pb = 0; foreach($field['value'] as $key =>$kanban){ $pb = $pb + 1; ?>
				            <div class="portlet box grey-cascade" data-id="<?php echo ($kanban['data-id']); ?>" data-title="<?php echo ($kanban['title']); ?>">
								<div class="portlet-title">
									<div class="caption">
										<?php echo ($kanban['title']); ?>
									</div>
									<div class="clear"></div>
								</div>
								<div class="portlet-body">
									<div class="dd" id="nestable_list_<?php echo ($pb); ?>">
										<?php if(!empty($kanban['items'])): ?><ol class="dd-list">
											<?php if(is_array($kanban["items"])): $i = 0; $__LIST__ = $kanban["items"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="dd-item" data-id="<?php echo ($vo["data-id"]); ?>" data-title="<?php echo ($vo["title"]); ?>">
													<div class="dd-handle"><?php echo ($vo["title"]); ?></div>
												</li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ol>
										<?php else: ?>
										<div class="dd-empty"></div><?php endif; ?>
									</div>
								</div>
				            </div>
				            <?php } ?>
				        </div>
						<script>var flag = "<?php echo ($field["name"]); ?>";</script>
						<script src="/app/admin/static/js/kanban/jquery.nestable.js"></script>
						<script>
						$(document).ready(function()
						{
							$('#nestable_list_1').nestable({
								group: 1
							}).on('change', updateTotput);

							$('#nestable_list_2').nestable({
								group: 1
							}).on('change', updateTotput);

							function updateTotput(){
								var kanban =new Array();
								$('.kanbans .box').each(function (index, element) {
									if ($(element).data('id')) {
										kanban[index] =  new Object();
										kanban[index]['data-id'] =  $(element).data('id');
										kanban[index]['title'] =  $(element).data('title');
										kanban[index]['items'] =  new Array();
										var obj = $(element).find('.dd-item');
										for (var i = 0; i < obj.length; i++) {
											kanban[index]['items'][i] = new Object();
											kanban[index]['items'][i]['data-id'] = $(obj[i]).data('id');
											kanban[index]['items'][i]['title'] = $(obj[i]).data('title');
										};
									}
								});
								var kanban_str = JSON.stringify(kanban);
								$('[name="'+flag+'"]').val(kanban_str);
							};
						
						});
						</script>
					</div><?php break;?>
					<?php case "chosen": ?><div class="layui-input-inline">
							<select name="<?php echo ($field["name"]); ?>[]" class="" multiple="multiple">
								<?php if( key($field['opt']) === 0){ ?>
								<?php if(is_array($field['opt'])): $i = 0; $__LIST__ = $field['opt'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = in_array(reset($option),$field['value'])? 'selected' : ''; ?>
									<option value="<?php echo reset($option);?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars(end($option))); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								<?php }else{ foreach($field['opt'] as $optgroupkey =>$optgroup){ ?>
								<optgroup label="<?php echo ($optgroupkey); ?>">
									<?php if(is_array($optgroup)): $i = 0; $__LIST__ = $optgroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = in_array(reset($option),$field['value'])? 'selected' : ''; ?>
										<option value="<?php echo reset($option);?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars(end($option))); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</optgroup>
								<?php } } ?>
							</select>
						</div><?php break;?>

				    <?php case "multiInput": ?><div class="layui-input-block" >
				        <?php $field['name'] = is_array($field['name'])?$field['name']:explode('|',$field['name']); foreach($field['name'] as $key=>$val){ ?>
						<div class="layui-input-inline">
				        <?php switch($field['config'][$key]['type']): case "text": ?><input type="text" name="<?php echo ($val); ?>" value="<?php echo (htmlspecialchars($field['value'][$key])); ?>"
				                       class="layui-input" style="<?php echo ($field['config'][$key]['style']); ?>" placeholder="<?php echo ($field['config'][$key]['placeholder']); ?>"/><?php break;?>
				            <?php case "select": ?><select name="<?php echo ($val); ?>" class="pull-left layui-input" style="<?php echo ($field['config'][$key]['style']); ?>" >
				                    <?php foreach($field['config'][$key]['opt'] as $key_opt =>$option){ ?>
				                    <?php $selected = $field['value'][$key]==$key_opt ? 'selected' : ''; ?>
				                    <option value="<?php echo ($key_opt); ?>"<?php echo ($selected); ?>><?php echo (htmlspecialchars($option)); ?></option>
				                    <?php } ?>
				                </select><?php break; endswitch;?>
						</div>
				        <?php } ?>
				        </div><?php break;?>
                    <?php case "multijson": ?><div class="layui-input-block">
							<a href="javascript:" data-role="add_multi_json" class="layui-btn layui-btn-sm">新增</a>
							<?php if(empty($field['value'])){ ?>
							<div class="multi_json_rule">暂无</div>
							<?php }else{ $field['value'] = unserialize( $field['value'] ); ?>
							
							<table class="layui-table">
							  <tbody>
								<?php $j = 0; foreach($field['value'] as $key=>$opt2){ ?>
								<tr>
								<?php foreach($opt2 as $key2=>$opt1){ ?>
								   <td><?php echo ($opt1['title']); ?><input class="layui-input" type="text" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][value][]" value="<?php echo ($opt1["value"]); ?>" title="<?php echo ($opt1["title"]); ?>" />
								   <input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][key][]" value="<?php echo ($opt1["key"]); ?>"/>
								   <input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][title][]" value="<?php echo ($opt1["title"]); ?>"/>
								   </td>
								<?php } ?>
									<td><a href="javascript:" class="del_multi_json" data-role="del_multi_json">删除规则</a></td>
							   </tr>
							  <?php $j++; } ?>
							  </tbody>
							</table>
							<div class="multi_json_rule"></div>
							<?php } ?>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div>
						
						<script>
							$(function(){
								rebind_multi_json();
							})

							var rebind_multi_json = function(){
								add_multi_json();
								del_multi_json();
							}
							var add_multi_json = function(){
								var id=$(".del_multi_json").length+1;
								var html = '<div id="multi_json_rule_list">';
									html += '<table class="layui-table">';
									html += '<tbody>';
							  <?php foreach($field['opt'] as $key=>$opt2){ ?>
									html += '<tr>';
							  <?php foreach($opt2 as $key2=>$opt1){ ?>
									html += '<td><?php echo ($opt1['title']); ?><input class="layui-input" type="text" name="<?php echo ($field['name']); ?>['+id+'][value][]" value="<?php echo ($opt1["value"]); ?>" title="<?php echo ($opt1["title"]); ?>" />';
									html += '<input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>['+id+'][key][]" value="<?php echo ($opt1["key"]); ?>"/>';
									html += '<input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>['+id+'][title][]" value="<?php echo ($opt1["title"]); ?>"/>';
									html += '</td>';
								<?php } ?>
									html += '<td><a href="javascript:" class="del_multi_json" data-role="del_multi_json">删除规则</a></td>';
									html += '</tr>';
							  <?php } ?>
									html += '</tbody>';
									html += '</table>';
									html += '</div>';
								$('[data-role="add_multi_json"]').unbind('click');
								$('[data-role="add_multi_json"]').click(function(){
									$('.multi_json_rule').append(html);
									rebind_multi_json();
								})

							}

							var del_multi_json = function(){
								$('[data-role="del_multi_json"]').unbind('click');
								$('[data-role="del_multi_json"]').click(function(){
									$(this).closest('tr').remove();
									rebind_multi_json();
								})
							}

						</script><?php break;?>
                    <?php case "level_gold_grow": ?><table border="0" cellpadding="0" cellspacing="0" class="layui-table level_gold_grow">
                       <thead>
                       <tr>
						   <th colspan="2" style="text-align:center">提示</th>
						   <th colspan="2" style="text-align:center">级别</th>
						   <th colspan="2" style="text-align:center">金币奖励</th>
						   <th colspan="2" style="text-align:center">成长值奖励</th>
						   <th style="text-align:center">操作</th>
                       </tr>
					   </thead>
					   <tbody>
                       <?php if(!$field['value']){ ?>
                        <tr style="height:40px; line-height:40px;">
                            <td style="text-align:right" width="68">提示1：</td>
						    <td style="text-align:left;" width="188">
						    <input name="level_prompt[]" type="text" id="level_prompt1" size="20" value="" maxlength="40" class="layui-input"></td>
							<td style="text-align:right" width="68">Lv1：</td>
						    <td style="text-align:left;" width="98">
						    <input name="l[]" type="text" id="l1" size="12" value="" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="73">Glod1：</td>
							<td style="text-align:left;" width="98">
							<input name="glod[]" type="text" id="Glod1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td style="text-align:right" width="75">Grow1：</td>
							<td style="text-align:left;" width="98">
							<input name="grow[]" type="text" id="Grow1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td>
							<input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
							<input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px">
							</td>
						</tr>
                        <?php }else{ $lsgs=json_decode($field['value'],true); foreach($lsgs as $k=>$v){ $j=$k+1; echo '
						<tr style="height:40px; line-height:40px;">
						            <td style="text-align:right" width="68">提示'.$j.'：</td>
						            <td style="text-align:left;" width="188">
						            <input name="level_prompt[]" type="text" id="level_prompt'.$j.'" size="20" value="'.$v['level_prompt'].'" maxlength="40" class="layui-input"></td>
									<td style="text-align:right" width="68">Lv'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="l[]" type="text" id="l'.$j.'" size="12" value="'.$v['level'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="73">Glod'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="glod[]" type="text" id="Glod'.$j.'" size="12" value="'.$v['glod'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="75">Grow'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="grow[]" type="text" id="Grow'.$j.'" size="12" value="'.$v['grow'].'" maxlength="12" class="layui-input"></td>
									<td>
									<input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
									<input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px">
									</td>
								</tr>'; } } ?>
						</tbody>
						</table>
						<script type="text/javascript">
						function add_tr($obj){
							$id=$(".del_lgg_btn").length+1;
							$tr_html='<tr style="height:40px; line-height:40px;"><td style="text-align:right" width="68">提示';
							$tr_html =$tr_html+$id+'：</td><td style="text-align:left;" width="188"><input name="level_prompt[]" type="text" id="level_prompt';
							$tr_html =$tr_html+$id+'" size="20" value="" maxlength="40" class="layui-input"></td><td width="68" style="text-align:right">Lv';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" maxlength="12" value="0" size="12" id="L';
							$tr_html =$tr_html+$id+'" name="l[]" class="layui-input"></td><td width="73" style="text-align:right">Glod';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="Glod';
							$tr_html =$tr_html+$id+'" name="glod[]" class="layui-input"></td><td width="75" style="text-align:right">Grow';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="Grow';
							$tr_html =$tr_html+$id+'" name="grow[]" class="layui-input"></td><td><input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px"><input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px"></td></tr>';
							$obj.parents('table:eq(0)').append($tr_html);
							}
						function del_tr($obj){
							if($('.del_lgg_btn').length>1){
								$obj.parents('table:eq(0)').find("tr:last").remove();
							}else{
								alert("最后一个了，真的不能再删除了！！！");
								}
							}
						</script><?php break;?>
                    <?php case "match_level_gold": ?><table border="0" cellpadding="0" cellspacing="0" class="match_level_gold layui-table">
                       <thead>
                       <tr>
						   <th colspan="2" style="text-align:center">名次</th>
						   <th colspan="2" style="text-align:center">等级</th>
						   <th colspan="2" style="text-align:center">金币奖励</th>
						   <th colspan="2" style="text-align:center">角色名</th>
						   <th colspan="2" style="text-align:center">领取时间</th>
						   <th style="text-align:center">操作</th>
                       </tr>
					   </thead>
					   <tbody>
                       <?php if(!$field['value']){ ?>
                        <tr style="height:40px; line-height:40px;">
                            <td style="text-align:right" width="68">名次1：</td>
						    <td style="text-align:left;" width="188">
						    <input name="matchranking[]" type="text" id="matchranking1" size="20" value="0" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="68">等级1：</td>
						    <td style="text-align:left;" width="98">
						    <input name="matchl[]" type="text" id="matchl1" size="12" value="0" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="73">奖励1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchglod[]" type="text" id="matchglod1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td style="text-align:right" width="75">角色名1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchrole[]" type="text" id="matchrole1" size="12" maxlength="40" value="" class="layui-input"></td>
							<td style="text-align:right" width="75">时间1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchtime[]" type="text" id="matchtime1" size="12" maxlength="12" value="" class="layui-input"></td>
							<td>
							<input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
							<input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px">
							</td>
						</tr>
                        <?php }else{ $mgs=json_decode($field['value'],true); foreach($mgs as $k=>$v){ $m=$k+1; echo '
								<tr style="height:40px; line-height:40px;">
						            <td style="text-align:right" width="68">名次'.$m.'：</td>
						            <td style="text-align:left;" width="188">
						            <input name="matchranking[]" type="text" id="matchranking'.$m.'" size="20" value="'.$v['matchranking'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="68">等级'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchl[]" type="text" id="matchl'.$m.'" size="12" value="'.$v['matchlevel'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="73">奖励'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchglod[]" type="text" id="matchglod'.$m.'" size="12" value="'.$v['matchglod'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="75">角色名'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchrole[]" type="text" id="matchrole'.$m.'" size="12" value="'.$v['matchrole'].'" maxlength="40" class="layui-input"></td>
									<td style="text-align:right" width="75">时间'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchtime[]" type="text" id="matchtime'.$m.'" size="12" value="'.$v['matchtime'].'" maxlength="12" class="layui-input"></td>
									<td>
									<input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
									<input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px"></td>
								</tr>'; } } ?>
						</tbody>
						</table>
						<script type="text/javascript">
							function add_tr_match($obj){
								$id=$(".del_mgg_btn").length+1;
								$tr_html='<tr style="height:40px; line-height:40px;"><td style="text-align:right" width="68">名次';
								$tr_html =$tr_html+$id+'：</td><td style="text-align:left;" width="188"><input name="matchranking[]" type="text" id="matchranking';
								$tr_html =$tr_html+$id+'" size="20" value="0" maxlength="12" class="layui-input"></td><td width="68" style="text-align:right">等级';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" maxlength="12" value="0" size="12" id="matchl';
								$tr_html =$tr_html+$id+'" name="matchl[]" class="layui-input"></td><td width="73" style="text-align:right">奖励';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="matchglod';
								$tr_html =$tr_html+$id+'" name="matchglod[]" class="layui-input"></td><td width="75" style="text-align:right">角色名';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="" size="12" maxlength="40" id="matchrole';
								$tr_html =$tr_html+$id+'" name="matchrole[]" class="layui-input"></td><td width="75" style="text-align:right">时间';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="" size="12" maxlength="12" id="matchtime';
								$tr_html =$tr_html+$id+'" name="matchtime[]" class="layui-input"></td><td><input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px"><input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px"></td></tr>';
								$obj.parents('table:eq(0)').append($tr_html);
								}
							
							function del_tr_match($obj){
								if($('.del_mgg_btn').length>1){
									$obj.parents('table:eq(0)').find("tr:last").remove();
								}else{
									alert("最后一个了，真的不能再删除了！！！");
								}
							}
						</script><?php break;?>
                    <?php case "userDefined": echo ($field["definedHtml"]); break;?>
                    <?php default: ?>
                    <span style="color: #f00;">错误：未知字段类型 <?php echo ($field["type"]); ?></span>
                    <input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo (htmlspecialchars($field["value"])); ?>"/><?php endswitch;?>
</div>
							<?php } endforeach; endif; else: echo "" ;endif; ?>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php }else{ ?>
				<div class="layui-tab-item layui-show">
				<?php if(is_array($keyList)): $i = 0; $__LIST__ = $keyList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i;?><div class="layui-form-item">
			<label class="layui-form-label"><?php echo (htmlspecialchars($field["title"])); ?></label>
			<?php if($field['name'] == 'action'): ?><div class="layui-input-inline"><p style="color: #f00;">开发人员注意：你使用了一个名称为action的字段，由于这个字段名称会与form[action]冲突，导致无法提交表单，请换用另外一个名字。</p>
				</div><?php endif; ?>
			<?php switch($field["type"]): case "text": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "color": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "tage": ?><div class="layui-inline">
						<?php if(!empty($field['value'])){ $field['value'] = json_decode($field['value'],true); } ?>
						<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $union_rules = D('Admin/AuthGroup')->where(array('status'=>1,'type' => AuthGroupModel::TYPE_ADMIN, 'id'=> $key))->getField('union_rules'); if(!empty($union_rules) && $union_rules != '""'){ $union_rule = json_decode($union_rules, true); ?>
						<div class="layui-form-item">
						<?php $kk = $key; ?>
						<input type="hidden" value="<?php echo ($option["title"]); ?>" name="tage[<?php echo ($kk); ?>][title][]" />
						<label class="layui-form-label" style="width: 100px;"><?php echo ($option["title"]); ?>：</label>
						<div class="layui-inline">
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">一级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_1 = $union_rule[strtolower($option['modulename'])][child][1]; }else{ $union_tage_1 = $field['value'][$kk][child][1]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][1]" value="<?php echo ($union_tage_1); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">二级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_2 = $union_rule[strtolower($option['modulename'])][child][2]; }else{ $union_tage_2 = $field['value'][$kk][child][2]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][2]" value="<?php echo ($union_tage_2); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">三级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_3 = $union_rule[strtolower($option['modulename'])][child][3]; }else{ $union_tage_3 = $field['value'][$kk][child][3]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][3]" value="<?php echo ($union_tage_3); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">四级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_4 = $union_rule[strtolower($option['modulename'])][child][4]; }else{ $union_tage_4 = $field['value'][$kk][child][4]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][4]" value="<?php echo ($union_tage_4); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">五级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_5 = $union_rule[strtolower($option['modulename'])][child][5]; }else{ $union_tage_5 = $field['value'][$kk][child][5]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][5]" value="<?php echo ($union_tage_5); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">六级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_6 = $union_rule[strtolower($option['modulename'])][child][6]; }else{ $union_tage_6 = $field['value'][$kk][child][6]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][6]" value="<?php echo ($union_tage_6); ?>" placeholder="">
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label" style="width: 100px;">七级提成(%)：</label>
							<div class="layui-input-inline" style="min-width: 100px;">
							<?php if(empty($field['value'])){ $union_tage_7 = $union_rule[strtolower($option['modulename'])][child][7]; }else{ $union_tage_7 = $field['value'][$kk][child][7]; } ?>
							<input type="text" class="layui-input" name="tage[<?php echo ($kk); ?>][child][7]" value="<?php echo ($union_tage_7); ?>" placeholder="">
							</div>
						</div>
						</div>
						</div>
						<?php } endforeach; endif; else: echo "" ;endif; ?>
					</div><?php break;?>
					<?php case "hidden": ?><input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" class="" /><?php break;?>
					<?php case "readonly": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input" readonly style="border:None">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "integer": ?><div class="layui-input-inline">
						<input type="number" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" lay-verify="number" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
					<?php case "uid": ?><div class="layui-input-inline">
						<input type="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field["value"]); ?>" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid layui-word-aux">
						<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
       				<?php case "select": ?><div class="layui-input-inline">
       					<select class="" name="<?php echo ($field["name"]); ?>">
							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = $field['value']==$key ? 'selected' : ''; ?>
				            <option value="<?php echo ($key); ?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars($option)); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
					<div class="layui-form-mid layui-word-aux">
       				    <?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
					</div><?php break;?>
                    <?php case "radio": ?><div class="layui-input-block">
							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $checked = $field['value']==$key ? 'checked' : ''; $inputId = "id_$field[name]_$key"; ?>
								<input type="radio" name="<?php echo ($field["name"]); ?>" value="<?php echo ($key); ?>" title="<?php echo (htmlspecialchars($option)); ?>" <?php echo ($checked); ?>><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<div class="layui-form-mid layui-word-aux">
							<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
						</div><?php break;?>
                    <?php case "icon": ?><!-- 图标选择器 --><?php break;?>
				    <?php case "singleImage": ?><div class="layui-input-block">
							<input type="button" id="image_<?php echo ($field["name"]); ?>" class="layui-upload-button singleimage" value="上传图片" />
							<input type="button" class="layui-box layui-upload-button selectImg" style="padding:0 15px" id="selectImg_<?php echo ($field["name"]); ?>" data-name="<?php echo ($field["name"]); ?>" value="浏览服务器" data-type="0" />
							
							<input type="hidden" id="url" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
							<div class="upload_picture" style="margin-top:10px;display:block;overflow:hidden;">
							<?php if(!empty($field["value"])): ?><a href="<?php echo (get_cover($field["value"],'path')); ?>" target="_blank" title="点击查看大图" style="display:block;float:left;margin-right:10px;">
									<img src="<?php echo (get_cover($field["value"],'path')); ?>" id="upload_<?php echo ($field["name"]); ?>" style="width:100px;height:100px"/>
								</a>
							<?php else: ?>
								<a href="<?php echo (get_cover($field["value"],'path')); ?>" target="_blank" title="点击查看大图" style="display:block;float:left;margin-right:10px;">
									<img src="<?php echo (get_cover($field["value"],'path')); ?>" id="upload_<?php echo ($field["name"]); ?>" style="width:100px;height:100px"/>
								</a><?php endif; ?>
							</div>
							<div class="layui-form-mid layui-word-aux">
								<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
							</div>
						</div>
						<script type="text/javascript">
							layui.use('upload', function(){
								var upload = layui.upload;
								upload.render({
									url: '<?php echo U("File/uploadPicture",array("session_id"=>session_id()));?>'
									,elem: '#image_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
									,method: 'post' //上传接口的http类型
									,accept: 'images'
									,field: '<?php echo ($field["name"]); ?>_id'
									,before: function(input){
										//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
										layer.load(2, {shade: false});
									}
									,done: function(res, index, upload){
										layer.closeAll('loading'); //关闭loading
										upload_<?php echo ($field["name"]); ?>.src = res.url.<?php echo ($field["name"]); ?>_id.path;
										$("[name='<?php echo ($field["name"]); ?>']").val(res.url.<?php echo ($field["name"]); ?>_id.id);
									}
									,error: function(res, index, upload){
										layer.closeAll('loading'); //关闭loading
										layer.msg(res.info, {icon: 2, fixed: true, offset: '80%', time:1000});
									}
								});
							});
						</script><?php break;?>
                    
				    <?php case "multiImage": ?><div class="layui-input-block multiImage controls">
							<input type="button" id="J_selectImage_<?php echo ($field["name"]); ?>" class="layui-upload-button multiimage" value="批量上传图片" />
				        	<input type="button" id="selectImg_<?php echo ($field["name"]); ?>" class="layui-box layui-upload-button selectImg" style="padding:0 15px" data-name="<?php echo ($field["name"]); ?>" value="浏览服务器" data-type="1"/>
							
				            <input class="attach" type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
				            
				            <div class="upload-img-box" style="margin-top:10px;overflow:hidden">
				                <div class="upload-pre-items popup-gallery" id="imageview_<?php echo ($field["name"]); ?>">
				                    <?php if(!empty($field["value"])): $aIds = explode(',',$field['value']); ?>
				                        <?php if(is_array($aIds)): $i = 0; $__LIST__ = $aIds;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$aId): $mod = ($i % 2 );++$i;?><div class="each">
				                                <a href="<?php echo (get_cover($aId,'path')); ?>" title="点击查看大图">
				                                    <img src="<?php echo (get_cover($aId,'path')); ?>">
				                                </a>
				                                <div class="text-center opacity del_btn" ></div>
                                        		<div onclick="admin_image.removeImage($(this),'<?php echo ($aId); ?>')"  class="text-center del_btn">删除</div>
				                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php else: ?>
										<div class="each">
											<a href="<?php echo (get_cover($aId,'path')); ?>" title="点击查看大图">
												<img src="<?php echo (get_cover($aId,'path')); ?>">
											</a>
											<div class="text-center opacity del_btn" ></div>
											<div onclick="admin_image.removeImage($(this),'<?php echo ($aId); ?>')"  class="text-center del_btn">删除</div>
										</div><?php endif; ?>
				                </div>
				            </div>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
				        </div>
						
						<script type="text/javascript">
						layui.use('upload', function(){
							var upload = layui.upload;
							upload.render({
								url: '<?php echo U("File/uploadPicture",array("session_id"=>session_id()));?>'
								,elem: '#J_selectImage_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
								,method: 'post' //上传接口的http类型
								,accept: 'images'
								,field: '<?php echo ($field["name"]); ?>_id'
								,before: function(input){
									//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
									layer.load(2, {shade: false});
								}
								,done: function(res, index, upload){
									//console.log(res);
									layer.closeAll('loading'); //关闭loading
									var div = $('#imageview_<?php echo ($field["name"]); ?>');
									admin_image.upAttachVal('add',res.url.<?php echo ($field["name"]); ?>_id.id, $("[name='<?php echo ($field["name"]); ?>_id']"));
									div.append('<div class="each"><a href="'+ res.url.<?php echo ($field["name"]); ?>_id.path +'" title="点击查看大图"><img src="'+ res.url.<?php echo ($field["name"]); ?>_id.path +'"></a><div class="text-center opacity del_btn" ></div><div onclick="admin_image.removeImage($(this),'+res.url.<?php echo ($field["name"]); ?>_id.id +')"  class="text-center del_btn">删除</div></div>');
								}
								,error: function(res, index, upload){
									layer.closeAll('loading'); //关闭loading
									layer.msg(res.info, {icon: 2, fixed: true, offset: '80%', time:1000});
								}
							});
						});
						</script><?php break;?>
                    <?php case "checkbox": ?><div class="layui-input-block">
							<?php $field['value_array'] = explode(',', $field['value']); ?>

							<?php if(is_array($field["opt"])): $i = 0; $__LIST__ = $field["opt"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $checked = in_array($key,$field['value_array']) ? 'checked' : ''; $inputId = "id_$field[name]_$key"; ?>
							<input type="checkbox" name="<?php echo ($field["name"]); ?>" value="<?php echo ($key); ?>" id="<?php echo ($inputId); ?>" title="<?php echo (htmlspecialchars($option)); ?>" data-field-name="<?php echo ($field["name"]); ?>" lay-skin="primary" <?php echo ($checked); ?>><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
                    <?php case "editor": ?><div class="layui-input-block">
							<?php echo W('Common/Ueditor/editor',array($field['name'],$field['name'],$field['value'],$field['style']['width'],$field['style']['height'],$field['config']));?>
							<!-- 
							<textarea class="layui-textarea qn-editor" name="<?php echo ($field["name"]); ?>" lay-verify="content" id="LAY_demo_editor"><?php echo ($field['value']); ?></textarea> -->
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div><?php break;?>
				    <?php case "textarea": ?><div class="layui-input-block">
							<textarea name="<?php echo ($field["name"]); ?>" class="layui-textarea"><?php echo (htmlspecialchars($field["value"])); ?></textarea>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div><?php break;?>
                    <?php case "time": if(!$field['value']){ $field['value'] = time(); } ?>
                        <div class="layui-input-inline">
							<input type="text" id="time_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"])); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input time_layui_element" />
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
                    <?php case "date": if(!$field['value']){ $field['value'] = time(); } ?>
						<div class="layui-input-inline">
							<input type="text" id="date_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"],'Y-m-d')); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input date_layui_element" />
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
				    <?php case "datetime": if(!$field['value']){ $field['value'] = time(); } ?>
						<div class="layui-input-inline">
							<input type="text" id="datetime_<?php echo ($field["name"]); ?>" name="<?php echo ($field["name"]); ?>" value="<?php echo (time_format($field["value"],'Y-m-d H:i:s')); ?>" placeholder="请选择时间" autocomplete="off" class="layui-input datetime_layui_element"/>
						</div>
						<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div><?php break;?>
				
				    <!--添加城市选择（需安装城市联动插件,css样式不好处理排版有点怪）-->
				    <?php case "city": ?><style type="text/css">
							.layui-input {display:inline-block;width: 120px;}
						</style>
						<!--修正在编辑信息时无法正常显示已经保存的地区信息-->
						<?php echo hook('j_china_city',array('province'=>$field['value']['0'],'city'=>$field['value']['1'],'district'=>$field['value']['2'],'community'=>$field['value']['3'])); break;?>
				    
				    <?php case "singleFile": ?><div class="layui-input-block">
							<input type="button" id="file_<?php echo ($field["name"]); ?>" class="layui-upload-button" value="上传文件" />
							<input type="hidden" id="url" name="<?php echo ($field["name"]); ?>" value="<?php echo ($field['value']); ?>"/>
							<div class="upload_file" style="margin-top:10px;display:block;overflow:hidden;">
								<?php if(!empty($field["value"])): ?><p id="upload_<?php echo ($field["name"]); ?>"><?php echo (text($field["value"])); ?></p>
								<?php else: ?>
									<p id="upload_<?php echo ($field["name"]); ?>"><?php echo (text($field["value"])); ?></p><?php endif; ?>
							</div>
							<div class="layui-form-mid layui-word-aux">
								<?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?>
							</div>
						</div>
						<script type="text/javascript">
						layui.use('upload', function(){
							var upload = layui.upload;
							upload.render({
								url: '<?php echo U("File/uploadFile",array("session_id"=>session_id()));?>'
								,elem: '#file_<?php echo ($field["name"]); ?>' //指定原始元素，默认直接查找class="layui-upload-file"
								,method: 'post' //上传接口的http类型
								,accept: 'file'
								,field: '<?php echo ($field["name"]); ?>_id'
								,before: function(input){
									//layer.msg('文件上传中', {icon: 16, fixed: true, offset: '80%', time:2000});
									layer.load(2, {shade: false});
								}
								,done: function(res, index, upload){
									layer.closeAll('loading'); //关闭loading
									$("#upload_<?php echo ($field["name"]); ?>").html(res.url.<?php echo ($field["name"]); ?>_id.name);
									$("[name='<?php echo ($field["name"]); ?>']").val(res.url.<?php echo ($field["name"]); ?>_id.id);
								}
								,error: function(index, upload){
									layer.closeAll('loading'); //关闭loading
									layer.msg(data.info, {icon: 2, fixed: true, offset: '80%', time:1000});
								}
							});
						});
						</script><?php break;?>
				    <?php case "multiFile": echo W('Common/UploadMultiFile/render',array(array('name'=>$field['name'],'limit'=>9,'value'=>$field['value']))); break;?>
				
				    <!--弹出窗口选择并返回值（目前只支持返回ID）开始-->
				    <?php case "dataselect": ?><input type="text" name="<?php echo ($field["name"]); ?>" id="<?php echo ($field["name"]); ?>" value="<?php echo (htmlspecialchars($field["value"])); ?>" class="text input-large layui-input" style="width: 400px;display:inline-block;"/>
						<input class="btn" style="margin-left:10px" type="button" value="选择" onclick="openwin('<?php echo ($field["opt"]); ?>','600','500')">
						<script type="text/javascript">
						//弹出窗口
						function openwin(url,width,height){
							var l=window.screen.width ;
							var w= window.screen.height;
							var al=(l-width)/2;
							var aw=(w-height)/2;
							var OpenWindow=window.open(url,"弹出窗口","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width="+width+",height="+height+",top="+aw+",left="+al+"");
							OpenWindow.focus();
							if(OpenWindow!=null){ //弹出窗口关闭事件
								//if(window.attachEvent) OpenWindow.attachEvent("onbeforeunload",   quickOut);
								if(window.attachEvent) OpenWindow.attachEvent("onunload",   quickOut);
							}
						}
						//关闭触发方法
						function quickOut()
						{
							alert("窗口已关闭");
						}
						</script><?php break;?>
					<!--弹出窗口选择并返回值（目前只支持返回ID）结束-->

				    <?php case "kanban": ?><div class="layui-input-inline">
						<link rel="stylesheet" type="text/css" href="/app/admin/static/js/kanban/jquery.nestable.css"/>
				        <input type="hidden" name="<?php echo ($field["name"]); ?>" value='<?php echo json_encode($field["value"]);?>'/>
				        <div class="kanbans" id="<?php echo ($field["name"]); ?>">
				            <?php $pb = 0; foreach($field['value'] as $key =>$kanban){ $pb = $pb + 1; ?>
				            <div class="portlet box grey-cascade" data-id="<?php echo ($kanban['data-id']); ?>" data-title="<?php echo ($kanban['title']); ?>">
								<div class="portlet-title">
									<div class="caption">
										<?php echo ($kanban['title']); ?>
									</div>
									<div class="clear"></div>
								</div>
								<div class="portlet-body">
									<div class="dd" id="nestable_list_<?php echo ($pb); ?>">
										<?php if(!empty($kanban['items'])): ?><ol class="dd-list">
											<?php if(is_array($kanban["items"])): $i = 0; $__LIST__ = $kanban["items"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="dd-item" data-id="<?php echo ($vo["data-id"]); ?>" data-title="<?php echo ($vo["title"]); ?>">
													<div class="dd-handle"><?php echo ($vo["title"]); ?></div>
												</li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ol>
										<?php else: ?>
										<div class="dd-empty"></div><?php endif; ?>
									</div>
								</div>
				            </div>
				            <?php } ?>
				        </div>
						<script>var flag = "<?php echo ($field["name"]); ?>";</script>
						<script src="/app/admin/static/js/kanban/jquery.nestable.js"></script>
						<script>
						$(document).ready(function()
						{
							$('#nestable_list_1').nestable({
								group: 1
							}).on('change', updateTotput);

							$('#nestable_list_2').nestable({
								group: 1
							}).on('change', updateTotput);

							function updateTotput(){
								var kanban =new Array();
								$('.kanbans .box').each(function (index, element) {
									if ($(element).data('id')) {
										kanban[index] =  new Object();
										kanban[index]['data-id'] =  $(element).data('id');
										kanban[index]['title'] =  $(element).data('title');
										kanban[index]['items'] =  new Array();
										var obj = $(element).find('.dd-item');
										for (var i = 0; i < obj.length; i++) {
											kanban[index]['items'][i] = new Object();
											kanban[index]['items'][i]['data-id'] = $(obj[i]).data('id');
											kanban[index]['items'][i]['title'] = $(obj[i]).data('title');
										};
									}
								});
								var kanban_str = JSON.stringify(kanban);
								$('[name="'+flag+'"]').val(kanban_str);
							};
						
						});
						</script>
					</div><?php break;?>
					<?php case "chosen": ?><div class="layui-input-inline">
							<select name="<?php echo ($field["name"]); ?>[]" class="" multiple="multiple">
								<?php if( key($field['opt']) === 0){ ?>
								<?php if(is_array($field['opt'])): $i = 0; $__LIST__ = $field['opt'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = in_array(reset($option),$field['value'])? 'selected' : ''; ?>
									<option value="<?php echo reset($option);?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars(end($option))); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								<?php }else{ foreach($field['opt'] as $optgroupkey =>$optgroup){ ?>
								<optgroup label="<?php echo ($optgroupkey); ?>">
									<?php if(is_array($optgroup)): $i = 0; $__LIST__ = $optgroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i; $selected = in_array(reset($option),$field['value'])? 'selected' : ''; ?>
										<option value="<?php echo reset($option);?>" <?php echo ($selected); ?>><?php echo (htmlspecialchars(end($option))); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</optgroup>
								<?php } } ?>
							</select>
						</div><?php break;?>

				    <?php case "multiInput": ?><div class="layui-input-block" >
				        <?php $field['name'] = is_array($field['name'])?$field['name']:explode('|',$field['name']); foreach($field['name'] as $key=>$val){ ?>
						<div class="layui-input-inline">
				        <?php switch($field['config'][$key]['type']): case "text": ?><input type="text" name="<?php echo ($val); ?>" value="<?php echo (htmlspecialchars($field['value'][$key])); ?>"
				                       class="layui-input" style="<?php echo ($field['config'][$key]['style']); ?>" placeholder="<?php echo ($field['config'][$key]['placeholder']); ?>"/><?php break;?>
				            <?php case "select": ?><select name="<?php echo ($val); ?>" class="pull-left layui-input" style="<?php echo ($field['config'][$key]['style']); ?>" >
				                    <?php foreach($field['config'][$key]['opt'] as $key_opt =>$option){ ?>
				                    <?php $selected = $field['value'][$key]==$key_opt ? 'selected' : ''; ?>
				                    <option value="<?php echo ($key_opt); ?>"<?php echo ($selected); ?>><?php echo (htmlspecialchars($option)); ?></option>
				                    <?php } ?>
				                </select><?php break; endswitch;?>
						</div>
				        <?php } ?>
				        </div><?php break;?>
                    <?php case "multijson": ?><div class="layui-input-block">
							<a href="javascript:" data-role="add_multi_json" class="layui-btn layui-btn-sm">新增</a>
							<?php if(empty($field['value'])){ ?>
							<div class="multi_json_rule">暂无</div>
							<?php }else{ $field['value'] = unserialize( $field['value'] ); ?>
							
							<table class="layui-table">
							  <tbody>
								<?php $j = 0; foreach($field['value'] as $key=>$opt2){ ?>
								<tr>
								<?php foreach($opt2 as $key2=>$opt1){ ?>
								   <td><?php echo ($opt1['title']); ?><input class="layui-input" type="text" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][value][]" value="<?php echo ($opt1["value"]); ?>" title="<?php echo ($opt1["title"]); ?>" />
								   <input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][key][]" value="<?php echo ($opt1["key"]); ?>"/>
								   <input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>[<?php echo ($j); ?>][title][]" value="<?php echo ($opt1["title"]); ?>"/>
								   </td>
								<?php } ?>
									<td><a href="javascript:" class="del_multi_json" data-role="del_multi_json">删除规则</a></td>
							   </tr>
							  <?php $j++; } ?>
							  </tbody>
							</table>
							<div class="multi_json_rule"></div>
							<?php } ?>
							<div class="layui-form-mid layui-word-aux"><?php if(!empty($field["subtitle"])): echo (htmlspecialchars($field["subtitle"])); endif; ?></div>
						</div>
						
						<script>
							$(function(){
								rebind_multi_json();
							})

							var rebind_multi_json = function(){
								add_multi_json();
								del_multi_json();
							}
							var add_multi_json = function(){
								var id=$(".del_multi_json").length+1;
								var html = '<div id="multi_json_rule_list">';
									html += '<table class="layui-table">';
									html += '<tbody>';
							  <?php foreach($field['opt'] as $key=>$opt2){ ?>
									html += '<tr>';
							  <?php foreach($opt2 as $key2=>$opt1){ ?>
									html += '<td><?php echo ($opt1['title']); ?><input class="layui-input" type="text" name="<?php echo ($field['name']); ?>['+id+'][value][]" value="<?php echo ($opt1["value"]); ?>" title="<?php echo ($opt1["title"]); ?>" />';
									html += '<input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>['+id+'][key][]" value="<?php echo ($opt1["key"]); ?>"/>';
									html += '<input class="layui-input" type="hidden" name="<?php echo ($field['name']); ?>['+id+'][title][]" value="<?php echo ($opt1["title"]); ?>"/>';
									html += '</td>';
								<?php } ?>
									html += '<td><a href="javascript:" class="del_multi_json" data-role="del_multi_json">删除规则</a></td>';
									html += '</tr>';
							  <?php } ?>
									html += '</tbody>';
									html += '</table>';
									html += '</div>';
								$('[data-role="add_multi_json"]').unbind('click');
								$('[data-role="add_multi_json"]').click(function(){
									$('.multi_json_rule').append(html);
									rebind_multi_json();
								})

							}

							var del_multi_json = function(){
								$('[data-role="del_multi_json"]').unbind('click');
								$('[data-role="del_multi_json"]').click(function(){
									$(this).closest('tr').remove();
									rebind_multi_json();
								})
							}

						</script><?php break;?>
                    <?php case "level_gold_grow": ?><table border="0" cellpadding="0" cellspacing="0" class="layui-table level_gold_grow">
                       <thead>
                       <tr>
						   <th colspan="2" style="text-align:center">提示</th>
						   <th colspan="2" style="text-align:center">级别</th>
						   <th colspan="2" style="text-align:center">金币奖励</th>
						   <th colspan="2" style="text-align:center">成长值奖励</th>
						   <th style="text-align:center">操作</th>
                       </tr>
					   </thead>
					   <tbody>
                       <?php if(!$field['value']){ ?>
                        <tr style="height:40px; line-height:40px;">
                            <td style="text-align:right" width="68">提示1：</td>
						    <td style="text-align:left;" width="188">
						    <input name="level_prompt[]" type="text" id="level_prompt1" size="20" value="" maxlength="40" class="layui-input"></td>
							<td style="text-align:right" width="68">Lv1：</td>
						    <td style="text-align:left;" width="98">
						    <input name="l[]" type="text" id="l1" size="12" value="" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="73">Glod1：</td>
							<td style="text-align:left;" width="98">
							<input name="glod[]" type="text" id="Glod1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td style="text-align:right" width="75">Grow1：</td>
							<td style="text-align:left;" width="98">
							<input name="grow[]" type="text" id="Grow1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td>
							<input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
							<input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px">
							</td>
						</tr>
                        <?php }else{ $lsgs=json_decode($field['value'],true); foreach($lsgs as $k=>$v){ $j=$k+1; echo '
						<tr style="height:40px; line-height:40px;">
						            <td style="text-align:right" width="68">提示'.$j.'：</td>
						            <td style="text-align:left;" width="188">
						            <input name="level_prompt[]" type="text" id="level_prompt'.$j.'" size="20" value="'.$v['level_prompt'].'" maxlength="40" class="layui-input"></td>
									<td style="text-align:right" width="68">Lv'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="l[]" type="text" id="l'.$j.'" size="12" value="'.$v['level'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="73">Glod'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="glod[]" type="text" id="Glod'.$j.'" size="12" value="'.$v['glod'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="75">Grow'.$j.'：</td>
									<td style="text-align:left;" width="98">
									<input name="grow[]" type="text" id="Grow'.$j.'" size="12" value="'.$v['grow'].'" maxlength="12" class="layui-input"></td>
									<td>
									<input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
									<input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px">
									</td>
								</tr>'; } } ?>
						</tbody>
						</table>
						<script type="text/javascript">
						function add_tr($obj){
							$id=$(".del_lgg_btn").length+1;
							$tr_html='<tr style="height:40px; line-height:40px;"><td style="text-align:right" width="68">提示';
							$tr_html =$tr_html+$id+'：</td><td style="text-align:left;" width="188"><input name="level_prompt[]" type="text" id="level_prompt';
							$tr_html =$tr_html+$id+'" size="20" value="" maxlength="40" class="layui-input"></td><td width="68" style="text-align:right">Lv';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" maxlength="12" value="0" size="12" id="L';
							$tr_html =$tr_html+$id+'" name="l[]" class="layui-input"></td><td width="73" style="text-align:right">Glod';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="Glod';
							$tr_html =$tr_html+$id+'" name="glod[]" class="layui-input"></td><td width="75" style="text-align:right">Grow';
							$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="Grow';
							$tr_html =$tr_html+$id+'" name="grow[]" class="layui-input"></td><td><input onclick="add_tr($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px"><input onclick="del_tr($(this))" type="button" value="删除" id="del_lgg_btn" class="layui-btn layui-btn-xs del_lgg_btn" style="margin-bottom:0;margin-right:0px"></td></tr>';
							$obj.parents('table:eq(0)').append($tr_html);
							}
						function del_tr($obj){
							if($('.del_lgg_btn').length>1){
								$obj.parents('table:eq(0)').find("tr:last").remove();
							}else{
								alert("最后一个了，真的不能再删除了！！！");
								}
							}
						</script><?php break;?>
                    <?php case "match_level_gold": ?><table border="0" cellpadding="0" cellspacing="0" class="match_level_gold layui-table">
                       <thead>
                       <tr>
						   <th colspan="2" style="text-align:center">名次</th>
						   <th colspan="2" style="text-align:center">等级</th>
						   <th colspan="2" style="text-align:center">金币奖励</th>
						   <th colspan="2" style="text-align:center">角色名</th>
						   <th colspan="2" style="text-align:center">领取时间</th>
						   <th style="text-align:center">操作</th>
                       </tr>
					   </thead>
					   <tbody>
                       <?php if(!$field['value']){ ?>
                        <tr style="height:40px; line-height:40px;">
                            <td style="text-align:right" width="68">名次1：</td>
						    <td style="text-align:left;" width="188">
						    <input name="matchranking[]" type="text" id="matchranking1" size="20" value="0" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="68">等级1：</td>
						    <td style="text-align:left;" width="98">
						    <input name="matchl[]" type="text" id="matchl1" size="12" value="0" maxlength="12" class="layui-input"></td>
							<td style="text-align:right" width="73">奖励1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchglod[]" type="text" id="matchglod1" size="12" maxlength="12" value="0" class="layui-input"></td>
							<td style="text-align:right" width="75">角色名1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchrole[]" type="text" id="matchrole1" size="12" maxlength="40" value="" class="layui-input"></td>
							<td style="text-align:right" width="75">时间1：</td>
							<td style="text-align:left;" width="98">
							<input name="matchtime[]" type="text" id="matchtime1" size="12" maxlength="12" value="" class="layui-input"></td>
							<td>
							<input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
							<input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px">
							</td>
						</tr>
                        <?php }else{ $mgs=json_decode($field['value'],true); foreach($mgs as $k=>$v){ $m=$k+1; echo '
								<tr style="height:40px; line-height:40px;">
						            <td style="text-align:right" width="68">名次'.$m.'：</td>
						            <td style="text-align:left;" width="188">
						            <input name="matchranking[]" type="text" id="matchranking'.$m.'" size="20" value="'.$v['matchranking'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="68">等级'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchl[]" type="text" id="matchl'.$m.'" size="12" value="'.$v['matchlevel'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="73">奖励'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchglod[]" type="text" id="matchglod'.$m.'" size="12" value="'.$v['matchglod'].'" maxlength="12" class="layui-input"></td>
									<td style="text-align:right" width="75">角色名'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchrole[]" type="text" id="matchrole'.$m.'" size="12" value="'.$v['matchrole'].'" maxlength="40" class="layui-input"></td>
									<td style="text-align:right" width="75">时间'.$m.'：</td>
									<td style="text-align:left;" width="98">
									<input name="matchtime[]" type="text" id="matchtime'.$m.'" size="12" value="'.$v['matchtime'].'" maxlength="12" class="layui-input"></td>
									<td>
									<input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px">
									<input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px"></td>
								</tr>'; } } ?>
						</tbody>
						</table>
						<script type="text/javascript">
							function add_tr_match($obj){
								$id=$(".del_mgg_btn").length+1;
								$tr_html='<tr style="height:40px; line-height:40px;"><td style="text-align:right" width="68">名次';
								$tr_html =$tr_html+$id+'：</td><td style="text-align:left;" width="188"><input name="matchranking[]" type="text" id="matchranking';
								$tr_html =$tr_html+$id+'" size="20" value="0" maxlength="12" class="layui-input"></td><td width="68" style="text-align:right">等级';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" maxlength="12" value="0" size="12" id="matchl';
								$tr_html =$tr_html+$id+'" name="matchl[]" class="layui-input"></td><td width="73" style="text-align:right">奖励';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="0" size="12" maxlength="12" id="matchglod';
								$tr_html =$tr_html+$id+'" name="matchglod[]" class="layui-input"></td><td width="75" style="text-align:right">角色名';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="" size="12" maxlength="40" id="matchrole';
								$tr_html =$tr_html+$id+'" name="matchrole[]" class="layui-input"></td><td width="75" style="text-align:right">时间';
								$tr_html =$tr_html+$id+'：</td><td width="98" style="text-align:left;"><input type="text" value="" size="12" maxlength="12" id="matchtime';
								$tr_html =$tr_html+$id+'" name="matchtime[]" class="layui-input"></td><td><input onclick="add_tr_match($(this))" type="button" value="添加" class="layui-btn layui-btn-xs" style="margin-bottom:0;margin-right:0px"><input onclick="del_tr_match($(this))" type="button" value="删除" id="del_mgg_btn" class="layui-btn layui-btn-xs del_mgg_btn" style="margin-bottom:0;margin-right:0px"></td></tr>';
								$obj.parents('table:eq(0)').append($tr_html);
								}
							
							function del_tr_match($obj){
								if($('.del_mgg_btn').length>1){
									$obj.parents('table:eq(0)').find("tr:last").remove();
								}else{
									alert("最后一个了，真的不能再删除了！！！");
								}
							}
						</script><?php break;?>
                    <?php case "userDefined": echo ($field["definedHtml"]); break;?>
                    <?php default: ?>
                    <span style="color: #f00;">错误：未知字段类型 <?php echo ($field["type"]); ?></span>
                    <input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo (htmlspecialchars($field["value"])); ?>"/><?php endswitch;?>
</div><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
				<?php } ?>
		  </div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label"></label>
			<div class="layui-input-inline">
			<?php if(is_array($buttonList)): $i = 0; $__LIST__ = $buttonList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$button): $mod = ($i % 2 );++$i;?><button <?php echo ($button["attr"]); ?>><?php echo ($button["title"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		</form>
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