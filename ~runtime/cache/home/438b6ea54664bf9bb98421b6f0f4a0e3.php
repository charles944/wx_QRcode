<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
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
        "APP": "/app", //当前项目地址
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
    <style>
	body,html {height:100%}
	.layui-layout-body {overflow:auto}
	#user-login,.qn-user-dispshow {display:block!important}
	.qn-user-login {position:relative;left:0;top:0;padding:110px 0;min-height:100%;box-sizing:border-box}
	.qn-user-login-main {width:375px;margin:0 auto;box-sizing:border-box}
	.qn-user-login-box {padding:20px}
	.qn-user-login-header {text-align:center}
	.qn-user-login-header h2 {margin-bottom:10px;font-weight:300;font-size:30px;color:#000}
	.qn-user-login-header p {font-weight:300;color:#999}
	.qn-user-login-body .layui-form-item {position:relative}
	.qn-user-login-icon {position:absolute;left:1px;top:1px;width:38px;line-height:36px;text-align:center;color:#d2d2d2}
	.qn-user-login-body .layui-form-item .layui-input {padding-left:38px}
	.qn-user-login-codeimg {max-height:38px;width:100%;cursor:pointer;box-sizing:border-box}
	.qn-user-login-other {position:relative;font-size:0;line-height:38px;padding-top:20px}
	.qn-user-login-other>* {display:inline-block;vertical-align:middle;margin-right:10px;font-size:14px}
	.qn-user-login-other .layui-icon {position:relative;top:2px;font-size:26px}
	.qn-user-login-other a:hover {opacity:.8}
	.qn-user-jump-change {float:right}
	.qn-user-login-footer {position:absolute;left:0;bottom:0;width:100%;line-height:30px;padding:20px;text-align:center;box-sizing:border-box;color:rgba(0,0,0,.5)}
	.qn-user-login-footer span {padding:0 5px}
	.qn-user-login-footer a {padding:0 5px;color:rgba(0,0,0,.5)}
	.qn-user-login-footer a:hover {color:rgba(0,0,0,1)}
	.qn-user-login-main[bgimg] {background-color:#fff;box-shadow:0 0 5px rgba(0,0,0,.05)}
	.ladmin-user-login-theme {position:fixed;bottom:0;left:0;width:100%;text-align:center}
	.ladmin-user-login-theme ul {display:inline-block;padding:5px;background-color:#fff}
	.ladmin-user-login-theme ul li {display:inline-block;vertical-align:top;width:64px;height:43px;cursor:pointer;transition:all .3s;-webkit-transition:all .3s;background-color:#f2f2f2}
	.ladmin-user-login-theme ul li:hover {opacity:.9}
	@media screen and (max-width:768px) {.qn-user-login {padding-top:60px}
	.qn-user-login-main {width:300px}
	.qn-user-login-box {padding:10px}
	}
	</style>
</head>
<body>
<div class="main main_login layui-clear" style="margin:0">
	<?php echo W('login/login',array('type'=>"login"));?>
</div>
<script src="/public/layui/layui.js"></script>
<script>
layui.config({version: "2.0.1",base: '/public/js/'}).extend({ qn: 'qn'}).use('qn');
layui.cache.user = {uid: parseInt(MID)};
</script>
</body>
</html>