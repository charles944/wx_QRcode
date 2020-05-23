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
	
<div class="container" id="step3">
	<div id="logo"  style="min-height:50px">&nbsp;</div>
	<div class="main">
		<?php
 defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_HOST_M', '127.0.0.1'); defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_PORT', '3306'); ?>
		<fieldset class="layui-elem-field layui-field-title">
		  <legend>创建数据库</legend>
		</fieldset>
		<form action="/install.php?s=/install/step3.html" method="post" class="layui-form layui-form-pane">
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据库连接类型</label>
				<div class="layui-input-block">
				<select name="dbtype" lay-filter="aihao">
					<option>mysql</option>
					<option>mysqli</option>
				</select>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">数据库服务器</label>
				<div class="layui-input-block">
					<input type="text" name="dbhost" lay-verify="required" required autocomplete="off" placeholder="请输入数据库地址" value="<?php if(defined("SAE_MYSQL_HOST_M")): echo (SAE_MYSQL_HOST_M); else: ?>127.0.0.1<?php endif; ?>" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据库名</label>
				<div class="layui-input-block">
					<input type="text" name="dbname" lay-verify="required" required autocomplete="off" placeholder="请输入数据库名" value="<?php if(defined("SAE_MYSQL_DB")): echo (SAE_MYSQL_DB); endif; ?>" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据库用户名</label>
				<div class="layui-input-block">
					<input type="text" name="dbuser" lay-verify="required" required autocomplete="off" placeholder="请输入数据库用户名" value="<?php if(defined("SAE_MYSQL_USER")): echo (SAE_MYSQL_USER); endif; ?>" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据库密码</label>
				<div class="layui-input-block">
					<input type="text" name="dbpass" lay-verify="required" required autocomplete="off" placeholder="请输入数据库密码" value="<?php if(defined("SAE_MYSQL_PASS")): echo (SAE_MYSQL_PASS); endif; ?>" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据库端口</label>
				<div class="layui-input-block">
					<input type="text" name="dbport" lay-verify="required" required autocomplete="off" placeholder="请输入数据库端口" value="<?php if(defined("SAE_MYSQL_PORT")): echo (SAE_MYSQL_PORT); else: ?>3306<?php endif; ?>" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">数据表前缀</label>
				<div class="layui-input-block">
					<input type="text" name="dbpre" lay-verify="required" required autocomplete="off" placeholder="请输入数据表前缀" value="qn_" class="layui-input">
				</div>
			</div>
			<fieldset class="layui-elem-field layui-field-title">
			  <legend>管理员设置</legend>
			</fieldset>
			<div class="layui-form-item">
				<label class="layui-form-label">用户名</label>
				<div class="layui-input-block">
					<input type="text" name="adminuser" lay-verify="title" required autocomplete="off" placeholder="请输入用户名" value="" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">密码</label>
				<div class="layui-input-block">
					<input type="text" name="adminpass" lay-verify="pass" required autocomplete="off" placeholder="请输入密码" value="" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">确认密码</label>
				<div class="layui-input-block">
					<input type="text" name="adminpass1" lay-verify="pass" autocomplete="off" required placeholder="请输入密码" value="" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">邮箱</label>
				<div class="layui-input-block">
					<input type="text" name="adminemail" lay-verify="email" autocomplete="off" required placeholder="请输入标题" value="" class="layui-input">
				</div>
			</div>
		<fieldset class="layui-elem-field layui-field-title">
		  <legend></legend>
		</fieldset>
		<div class="layui-form-item">
			<button class="layui-btn layui-btn-radius layui-btn-primary" onclick="javascript:location.href='<?php echo U('Install/step2');?>'">返回上一步</button>
			<button class="layui-btn layui-btn-radius layui-btn-normal" lay-submit="" lay-filter="install">立即提交</button>
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