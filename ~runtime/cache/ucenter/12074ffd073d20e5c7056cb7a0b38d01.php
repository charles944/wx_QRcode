<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 8]><html lang="zh" class="ie8 no-js"><![endif]-->
<!--[if IE 9]><html lang="zh" class="ie9 no-js"><![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml">
<!--<![endif]-->
<head>
	<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<?php echo hook('syncmeta');?>
<?php $qn_seo_meta = get_seo_meta($vars,$seo); ?>
<?php if($qn_seo_meta['title']): ?><title><?php echo ($qn_seo_meta['title']); ?></title>
<?php elseif(!empty($seo['title'])): ?>
<title><?php echo ($seo['title']); ?> - <?php echo modC('WEB_SITE_NAME','暂未设置','Config');?></title>
<?php else: ?>
<title><?php echo modC('WEB_SITE_NAME','暂未设置','Config');?></title><?php endif; ?>
<?php if($qn_seo_meta['keywords']): ?><meta name="keywords" content="<?php echo ($qn_seo_meta['keywords']); ?>"/>
<?php elseif(!empty($seo['keywords'])): ?>
<meta name="keywords" content="<?php echo ($seo['keywords']); ?>"/><?php endif; ?>
<?php if($qn_seo_meta['description']): ?><meta name="description" content="<?php echo ($qn_seo_meta['description']); ?>"/>
<?php elseif(!empty($seo['description'])): ?>
<meta name="description" content="<?php echo ($seo['description']); ?>"/><?php endif; ?>
<link rel="shortcut icon" href="/favicon.ico">


    <link href="/app/ucenter/static/css/index.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" href="/public/layui/css/layui.css">
<script src="/public/js/lib.js"></script>
<script src="/public/layui/layui.js"></script>
<link type="text/css" rel="stylesheet" href="/public/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/public/css/control.css"/>

<!-- /public/js.php?f=js/jquery-2.0.3.min.js,js/com/com.functions.js,js/core.js,js/com/com.ucard.js -->
<?php $config = api('Config/lists'); C($config); $count_code=C('COUNT_CODE'); ?>
<script type="text/javascript">
    var ThinkPHP = window.Think = {
        "ROOT": "", //当前网站地址
        "APP": "", //当前项目地址
        "PUBLIC": "/public", //项目公共目录地址
        "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
        "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
        "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"],
        'URL_MODEL': "<?php echo C('URL_MODEL');?>",
    }
	var cookie_config={
        "prefix":"<?php echo C('COOKIE_PREFIX');?>"
    }
</script>
<?php $open_quick_login = modC('OPEN_QUICK_LOGIN', 0, 'USERCONFIG'); $open_quick_register = modC('OPEN_QUICK_REGISTER', 0, 'USERCONFIG'); $register_type = modC('REGISTER_TYPE','normal','UserConfig'); $register_type = explode(',',$register_type); $only_open_register = 0; if(in_array('invite',$register_type)&&!in_array('normal',$register_type)){ $only_open_register=1; } ?>
<script>
//全局内容的定义
var _ROOT_ = "";
var MID = "<?php echo is_login();?>";
var MODULE_NAME="<?php echo MODULE_NAME; ?>";
var ACTION_NAME="<?php echo ACTION_NAME; ?>";
var OPEN_QUICK_LOGIN = "<?php echo ($open_quick_login); ?>";
var OPEN_QUICK_REGISTER = "<?php echo ($open_quick_register); ?>";
var ONLY_OPEN_REGISTER = "<?php echo ($only_open_register); ?>";
</script>
<audio id="music" src="" autoplay="autoplay"></audio>
<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageheader');?>
</head>
<body class="layui-layout-body">
<div id="qn_app" class="">
<div class="layui-layout layui-layout-admin">
	<?php need_login(); $unreadMessage=D('Common/Message')->getHaventReadMeassageAndToasted(is_login()); ?>
<style>
.badge-text{position:relative !important;top:0 !important;margin:0 !important;}
</style>
<div class="layui-header" style="background-color:<?php echo modC('HEADER_NAV_COLOR', '', 'CONFIG');?>">
	<ul class="layui-nav layui-layout-left">
	</ul>
	<ul class="layui-nav layui-layout-right" lay-filter="layout-right">
		<?php if(is_login()): $common_header_user = query_user(array('nickname','avatar32')); $scoreModel = D('User/Score'); $scores = $scoreModel->getTypeList(array('status'=>1)); foreach ($scores as &$v) { $v['value'] = $scoreModel->getUserScore(is_login(), $v['id']); } unset($v); ?>
			<li class="layui-nav-item layui-hide-xs">
				<a href="<?php echo U('Ucenter/Message/Message');?>"><cite><?php echo ($common_header_user["nickname"]); ?>
				<?php if(count($unreadMessage) > 0 and count($unreadMessage) < 99): ?><span id="" class="badge-text layui-badge"><?php echo count($unreadMessage);?></span>
				<?php elseif(count($unreadMessage) >= 99): ?>
					<span id="" class="badge-text layui-badge">99+</span><?php endif; ?>
				</cite>
			</a>
			</li>
			<li class="layui-nav-item layui-hide-xs">
				<a href="javascript:;">
					<cite><?php echo get_login_group_name();?></cite>
				</a>
			</li>
			<li class="layui-nav-item layui-hide-xs">
				<a event-node="logout" href="javascript:void(0);">退出</a>
			</li><?php endif; ?>
		
	</ul>
	
</div>

<!-- <div class="layadmin-fixed" lay-templateid="TPL_layout"> -->
	<div class="layui-side layui-side-menu">
		<div class="layui-side-scroll">
			<div class="layui-logo" lay-href="">
				<a><?php echo modC('WEB_SITE_NAME','','Config');?></a> 
			</div>
			
			<ul class="layui-nav layui-nav-tree">
				<?php $__NAV__ = D('Common/Channel')->lists(true);$__NAV__ = list_to_tree($__NAV__, "id", "pid", "_"); if(is_array($__NAV__)): $i = 0; $__LIST__ = $__NAV__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i; if(($nav['_']) != ""): if(check_auth($nav['url'])){ ?>
						<li class="layui-nav-item <?php if((get_nav_active($nav["url"])) == "1"): ?>layui-nav-itemed<?php endif; ?>">
							<a href="javascript:;" title="<?php echo ($nav["title"]); ?>"><?php if(!empty($nav["band_text"])): ?><i class="layui-icon"><?php echo ($nav["band_text"]); ?></i><?php endif; ?><cite><?php echo ($nav["title"]); ?></cite></a>
							<dl class="layui-nav-child">
								<?php if(is_array($nav["_"])): $i = 0; $__LIST__ = $nav["_"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subnav): $mod = ($i % 2 );++$i; if(check_auth($subnav['url'])){ ?>
									<dd>
									<a style="<?php if(!empty($subnav["color"])): ?>color:<?php echo ($subnav["color"]); endif; ?>" href="<?php echo (get_nav_url($subnav["url"])); ?>" target="<?php if(($subnav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>">
									<?php echo ($subnav["title"]); ?> <?php if(!empty($subnav["band_text"])): ?><span class="layui-badge"><?php echo ($subnav["band_text"]); ?></span><?php endif; ?>
									</a>
									</dd>
									<?php } endforeach; endif; else: echo "" ;endif; ?>
							</dl>
						</li>
						<?php } ?>
						<?php else: ?>
						<?php if(check_auth($nav['url'])){ ?>
						<li class="layui-nav-item <?php if((get_nav_active($nav["url"])) == "1"): ?>layui-nav-itemed<?php endif; ?>">
							<a href="<?php echo (get_nav_url($nav["url"])); ?>" target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>" title="<?php echo ($nav["title"]); ?>" style="<?php if((get_nav_active($nav["url"])) != "1"): endif; ?>"><?php if(!empty($nav["band_text"])): ?><span class="layui-badge"><?php echo ($nav["band_text"]); ?></span><?php endif; ?><cite><?php echo ($nav["title"]); ?></cite></a>
						</li>
						<?php } endif; endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
	</div>
<!-- </div> -->
<?php echo hook('aftertop');?>

	<div class="layui-body" id="qn_app_body">
		


<div id="page">
	<div class="ucenter-module-hd">
		<h2>我的资料</h2>
		<div class="operation">
		</div>
	</div>

	<div class="ucenter-module-layout clearfix" id="ucenter-module-layout">

	<script>
		function center_toggle(name) {
			var show = $('#' + name + '_panel').css('display');
			$('.center_panel').hide();
			$('.center_arrow_right').show();
			$('.center_arrow_bottom').hide()
			if (show == 'none') {
				$('#' + name + '_panel').show();
				$('#' + name + '_toggle_right').hide();
				$('#' + name + '_toggle_bottom').show()
			} else {
				$('#' + name + '_toggle_right').show();
				$('#' + name + '_toggle_bottom').hide()
			}

		}
	</script>
	<div id="center_account">
		<div class="layui-tab">
		  <ul class="layui-tab-title">
			<li class="layui-this">基本资料</li>
			<?php if(is_array($profile_group_list)): $i = 0; $__LIST__ = $profile_group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vl): $mod = ($i % 2 );++$i;?><li><?php echo ($vl["profile_name"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
		  </ul>
		  <div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<div class="layui-form layui-form-pane mine-view">
				<form class="form-horizontal center_info layui-form" role="form" action="<?php echo U('ucenter/config/index');?>" method="post">
				<div class="layui-form-item">
					<label for="aUsername" class="layui-form-label">用户名</label>
					<div class="layui-input-inline">
						<?php if($self['username']){ ?>
						<input type="text" id="aUsername" name="aUsername" placeholder="还未设置用户名，赶快来设置吧~" autocomplete="off" value="<?php echo ($self['username']); ?>" class="layui-input" disabled>
						<span class="lh32"></span>
						<?php }else{ ?>
						<input type="text" id="aUsername" name="aUsername" placeholder="还未设置用户名，赶快来设置吧~" autocomplete="off" value="" class="layui-input">
						<a class="pull-left lh32 saveUsername" style="margin-left: 10px">设置</a>
						<script>
							$(function () {
								$('.saveUsername').click(function () {
									var username = $(this).prev().val();
									if (!username) {
										layer.msg('用户名不能为空！', {icon: 2, fixed: true, offset: '80%', time: 1000});
										return false;
									}
									if (confirm('用户名一经设置就不能修改，确定要设置么？')) {
										$.post("<?php echo U('ucenter/config/saveUsername');?>", {username: username}, function (res) {
											layui.qn.handleAjax(res);
										})
									}
								})
							})
						</script>
						<?php } ?>
					</div>
				</div>

				<div class="layui-form-item">
					<label for="aEmail" class="layui-form-label">邮箱</label>
					<div class="layui-input-inline">
						<?php if(empty($self["email"])): ?><input type="text" id="aEmail" name="aEmail" placeholder="" autocomplete="off" value="<?php echo ((isset($self["email"]) && ($self["email"] !== ""))?($self["email"]):'暂无'); ?>" class="layui-input" disabled>
						<?php else: ?>
						<input type="text" id="aEmail" name="aEmail" placeholder="" autocomplete="off" value="<?php echo (substr_replace($self["email"],'****',3,4)); ?>" class="layui-input" disabled><?php endif; ?>
						
					</div>
					<div class="layui-form-mid layui-word-aux" style="padding:4px 0">
						<a class="layui-btn layui-btn-sm layui-btn-primary" href="<?php echo U('ucenter/config/email');?>" target="_blank">修改邮箱</a>
					</div>
				</div>

				<div class="layui-form-item">
					<label for="aMobile" class="layui-form-label">手机</label>
					<div class="layui-input-inline">
						<?php if(empty($self["mobile"])): ?><input type="text" id="aMobile" name="aMobile" placeholder="" autocomplete="off" value="<?php echo ((isset($self["mobile"]) && ($self["mobile"] !== ""))?($self["mobile"]):'暂无'); ?>" class="layui-input" disabled>
						<?php else: ?>
						<input type="text" id="aMobile" name="aMobile" placeholder="" autocomplete="off" value="<?php echo (substr_replace($self["mobile"],'****',3,4)); ?>" class="layui-input" disabled><?php endif; ?>
						
					</div>
					<div class="layui-form-mid layui-word-aux" style="padding:4px 0">
						<a class="layui-btn layui-btn-sm layui-btn-primary" href="<?php echo U('ucenter/config/mobile');?>" target="_blank">修改手机</a>
					</div>
				</div>
				
				<div class="layui-form-item">
					<label for="nickname" class="layui-form-label">昵称</label>
					<div class="layui-input-inline">
						<input type="text" id="nickname" name="nickname" placeholder="" autocomplete="off" value="<?php echo (op_t($self["nickname"])); ?>"  placeholder="昵称" class="layui-input" >
					</div>
				</div>
				<div class="layui-form-item" pane="" style="width:400px">
					<label for="nickname" class="layui-form-label">性别</label>
				
					<div class="layui-input-block">
						<input name="sex" type="radio" value="1" title="男" <?php if(($self['sex']) == "1"): ?>checked<?php endif; ?> > 
						<input name="sex" type="radio" value="2" title="女" <?php if(($self['sex']) == "2"): ?>checked<?php endif; ?> >
						<input name="sex" type="radio" value="0" title="保密" <?php if(($self['sex']) == "0"): ?>checked<?php endif; ?>> 
					</div>
				</div>
				
				<div class="layui-form-item">
					<label for="aQQ" class="layui-form-label">QQ</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" id="qq" name="qq" value="<?php echo (op_t($self["qq"])); ?>" placeholder="QQ">
					</div>
				</div>

				<div class="layui-form-item position">
					<label for="aprovince" class="layui-form-label">所在地</label>
					<div class="layui-input-block">	<?php echo hook('j_china_city',array('province'=>$self['pos_province'],'city'=>$self['pos_city'],'district'=>$self['pos_district'],'community'=>$self['pos_community']));?>
					</div>
				</div>
				
				<div class="layui-form-item layui-form-text">
					<label for="signature" class="layui-form-label">签名</label>
					<div class="layui-input-block">
						<textarea placeholder="随便写些什么刷下存在感" id="signature" name="signature" autocomplete="off" class="layui-textarea" style="height: 80px;"><?php echo (htmlspecialchars($self["signature"])); ?></textarea>
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" key="set-mine" lay-filter="*" lay-submit="">保存</button>
				</div>
				</form>
				</div>
				
			</div>
			
			<?php if(is_array($profile_group_list)): $i = 0; $__LIST__ = $profile_group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="layui-tab-item" id="expand_tab_<?php echo ($vo["id"]); ?>">
					<form action="<?php echo U('Config/edit_expandinfo');?>" method="post" class="ajax-form">
						<div class="with-padding">
							<div class="row">
								<div class="col-xs-8">
									<input type="hidden" name="profile_group_id" value="<?php echo ($vo["id"]); ?>">
									<div>
										<?php if(is_array($vo["fields"])): $i = 0; $__LIST__ = $vo["fields"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vl): $mod = ($i % 2 );++$i;?><dl>
												<?php echo W('InputRender/inputRender',array($vl,'personal'));?>
											</dl><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
									<?php if(count($vo['fields']) != 0): ?><input type="submit" value="保存" id="submit_btn"
											   class="btn btn-primary expandinfo-sumbit">
										<?php else: ?>
										<span class="expandinfo-noticeinfo">该扩展信息分组没有信息！</span><?php endif; ?>
								</div>
							</div>
						</div>
					</form>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		  </div>
		</div>
	</div>
	<script type="text/javascript" src="/app/ucenter/static/js/expandinfo-form.js"></script>

	</div>
</div>
</div>

<script>
layui.config({version: "2.0.1",base: '/public/js/'}).extend({ qn: 'qn'}).use('qn');
layui.cache.user = {uid: parseInt(MID)};
</script>
<?php echo hook('pagefooter', 'widget');?>
<div class="hidden">
    <?php echo C('COUNT_CODE');?>
</div>
</body>
</html>