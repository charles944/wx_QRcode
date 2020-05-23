<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo ($version_name); ?> V<?php echo ($version); ?> 安装</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
		<link rel="stylesheet" href="/public/layui/css/layui.css">
		<link rel="stylesheet" href="/public/css/install.css">
		<script src="/public/layui/layui.js"></script>
		<script>
			layui.config({
			  version: "2.0.1"
			  ,base: 'public/js/'
			}).extend({
			  qncore: 'install'
			}).use('qncore');
		</script>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="/public/js/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>
	
<div class="container">
	<div class="logo">
        <h1>我们只为创造价值而存在</h1>
        <a href="javascript:;" class="layui-btn layui-btn-radius layui-btn-primary step1">新的开始</a>
    </div>
	<div id="agreement">
    <h3>安装协议</h3>
    <p style="text-indent:2em">版权所有 (c) 2015 - 2018，<?php echo (QN_VERSION_COPYRIGHT); ?> 保留所有权利。 <?php echo (QN_VERSION_NAME); ?>  基于<a target="_blank" href="http://www.thinkphp.cn">ThinkPHP</a>二次开发产品。感谢您选择 <?php echo (QN_VERSION_NAME); ?> ，希望我们的努力可以为您创造价值。</p>
    <p style="text-indent:2em">用户须知：</p>
    <p style="text-indent:2em">本协议是您于 <?php echo (QN_VERSION_COPYRIGHT); ?> 关于 <?php echo (QN_VERSION_NAME); ?> 产品使用的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制 <?php echo (QN_VERSION_COPYRIGHT); ?> 责任的免责条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及或 <?php echo (QN_VERSION_COPYRIGHT); ?> 随时对其的修改，您应不使用或主动取消 <?php echo (QN_VERSION_NAME); ?> 产品。否则，您的任何对 <?php echo (QN_VERSION_NAME); ?> 的相关服务的注册、登陆、下载、查看等使用行为将被视为您对本服务条款全部的完全接受，包括接受 <?php echo (QN_VERSION_COPYRIGHT); ?> 对服务条款随时所做的任何修改。</p>
    <p style="text-indent:2em">本服务条款一旦发生变更, <?php echo (QN_VERSION_COPYRIGHT); ?> 将在产品官网上公布修改内容。修改后的服务条款一旦在网站公布即有效代替原来的服务条款。您可随时登陆官网查阅最新版服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定， <?php echo (QN_VERSION_COPYRIGHT); ?> 有权随时中止或终止您对 <?php echo (QN_VERSION_NAME); ?> 产品的使用资格并保留追究相关法律责任的权利。</p>
    <p style="text-indent:2em">在理解、同意、并遵守本协议的全部条款后，方可开始使用 <?php echo (QN_VERSION_NAME); ?> 产品。您也可能与 <?php echo (QN_VERSION_COPYRIGHT); ?> 直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。</p>
    <p style="text-indent:2em"> <?php echo (QN_VERSION_COPYRIGHT); ?> 拥有 <?php echo (QN_VERSION_NAME); ?> 的知识产权，包括商标和著作权。本软件只供许可协议，并非出售。只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。</p>
	</div>
</div>

	<footer class="footer">
		<div style="text-align:center">
			

		</div>
	</footer>
	<div style="clear:both"></div>
    </body>
</html>