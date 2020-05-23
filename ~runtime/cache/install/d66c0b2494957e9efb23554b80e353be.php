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
	
<div class="container" id="step4">
	<div class="logo"  style="">
         <h3>完成</h3>
        <p style="text-align:center;margin-bottom:30px;">已经安装完成，感谢您的使用，请选择访问首页或者登陆后台！</p>
        <?php if(isset($info)): echo ($info); endif; ?>
        <div class="footer">
            <a class="layui-btn layui-btn-radius layui-btn-normal" target="_blank" href="index.php?s=/manage">登录后台</a>
            <a class="layui-btn layui-btn-radius layui-btn-primary" target="_blank" href="index.php">访问首页</a>
        </div>
    </div>
</div>

	<footer class="footer">
		<div style="text-align:center">
			
    

		</div>
	</footer>
	<div style="clear:both"></div>
    </body>
</html>