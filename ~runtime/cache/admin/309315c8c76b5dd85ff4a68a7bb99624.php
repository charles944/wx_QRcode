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
		<a href=""><cite>会员中心导航管理</cite></a>
	</div>
</div>
<div class="layui-fluid">
	<div class="layui-card">
		<div class="layui-card-body">

			<div class="portlet-title">
				<div class="actions">
						<a class="layui-btn layui-btn-xs fbutton" href="<?php echo U('add','pid='.$pid);?>">新 增</a>
						<button class="layui-btn layui-btn-xs fbutton list_sort" url="<?php echo U('sort',array('pid'=>I('get.pid',0)),'');?>">排序</button>
				</div>
				<div class="clear"></div>
			</div>
				<div class="portlet-body">
				<div class="table-scrollable layui-form">
					<table class="layui-table" lay-even lay-skin="line">
						<thead>
						<th width="2%" class="check">
							<input type="checkbox" class="group-checkable" lay-skin="primary" lay-filter="allChoose">
						</th>
						<th>ID</th>
						<th>导航名称</th>
						<th>导航地址</th>
						<th>分组</th>
						<th>新窗口打开</th>
						<th>排序</th>
						<th>操作</th>
						</thead>
						<tbody>
							<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$channel): $mod = ($i % 2 );++$i;?><tr>
								<td class="check ">
									<input class="ids" type="checkbox" name="ids[]" value="<?php echo ($channel['id']); ?>" lay-skin="primary" lay-filter="ids"/>
								</td>
								<td><?php echo ($channel["id"]); ?></td>
								<td><a href="<?php echo U('index?pid='.$channel['id']);?>"><?php echo ($channel["title"]); ?></a></td>
								<td><?php echo ($channel["url"]); ?></td>
								<td><?php echo ($channel["group"]); ?></td>
								<td><?php if(($channel["target"]) == "1"): ?>是<?php else: ?>不是<?php endif; ?></td>
								<td><?php echo ($channel["sort"]); ?></td>
								<td>
									<a href="<?php echo U('setStatus?ids='.$channel['id'].'&status='.abs(1-$channel['status']));?>" class="ajax-get layui-btn layui-btn-xs hbutton btn_trash"><?php echo (show_status_op($channel["status"])); ?></a>
									<a title="编辑" class="layui-btn layui-btn-xs hbutton btn_edit" href="<?php echo U('edit?id='.$channel['id'].'&pid='.$pid);?>">编辑</a>
									<a class="ajax-get layui-btn layui-btn-xs hbutton btn_trash" title="删除" href="<?php echo U('del?id='.$channel['id']);?>">删除</a>
								</td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
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

<script>
$('.list_sort').click(function(){
	var url = $(this).attr('url');
	var ids = $('.ids:checked');
	var param = '';
	if(ids.length > 0){
		var str = new Array();
		ids.each(function(){
			str.push($(this).val());
		});
		param = str.join(',');
	}

	if(url != undefined && url != ''){
		window.location.href = url + '/ids/' + param;
	}
});
</script>

<?php echo hook('adminpagefooter');?>
</body>
</html>