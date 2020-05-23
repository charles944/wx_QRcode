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
	
<div class="container" id="step2">
	<div class="logo">
        <h1>准备好激活程序，开启创造价值之旅？</h1>
        <form action="/install.php?s=/install/step2.html" method="post" class="layui-form layui-form-pane" style="max-width:500px;margin:0 auto;">
            <div class="layui-form-item">
                <label class="layui-form-label">当前IP</label>
                <div class="layui-input-block">
                  <input type="text" name="vdomain" required value="<?php echo gethostbyname($_SERVER['SERVER_NAME']);?>" readonly="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">当前域名</label>
                <div class="layui-input-block">
                  <input type="text" name="vip" readonly="" required value="<?php echo ($_SERVER['SERVER_NAME']); ?>" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">激活码</label>
                <div class="layui-input-block">
                  <input type="text" name="vcode" required  lay-verify="required" placeholder="请输入激活码" autocomplete="off" class="layui-input" value="<?php echo C('QN_ACTIVATION_CODE');?>">
                </div>
            </div>
			<div class="layui-form-item">
				<button class="layui-btn layui-btn-radius layui-btn-normal" lay-submit="" lay-filter="install">立刻激活程序</button>
			</div>
		</form>
    </div>
</div>

	<footer class="footer">
		<div style="text-align:center">
			

		</div>
	</footer>
	<div style="clear:both"></div>
    </body>
</html>