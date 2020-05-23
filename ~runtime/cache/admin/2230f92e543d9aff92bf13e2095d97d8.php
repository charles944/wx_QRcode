<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<!--[if IE 8]> <html lang="zh" class="ie8 no-js" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if IE 9]> <html lang="zh" class="ie9 no-js" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh"> 
<head>
<meta charset="utf-8"/>
<title>登录 - <?php echo modC('WEB_SITE_NAME','','CONFIG');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="/favicon.ico"/>
<?php switch($license_color): case "0": ?><style type="text/css">
html {overflow-y:scroll;filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);-webkit-filter: grayscale(100%);}
</style><?php break; endswitch;?>
<link href="/app/admin/static/layui/css/layui.css" rel="stylesheet" type="text/css"/>
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

<body class="login">
<div class="qn-user-login animated fadeInDown">

    <div class="qn-user-login-main">
      <div class="qn-user-login-box qn-user-login-header">
        <h2><?php echo modC('WEB_SITE_NAME','','CONFIG');?> - 后台管理</h2>
        <p>为美好的明天而努力</p>
      </div>
      <div class="qn-user-login-box qn-user-login-body layui-form">
      	<form action="" method="post">
        <div class="layui-form-item">
          <label class="qn-user-login-icon layui-icon layui-icon-username" for="user-login-username"></label>
          <input type="text" name="username" id="user-login-username" lay-verify="required" placeholder="用户名" class="layui-input">
        </div>
        <div class="layui-form-item">
          <label class="qn-user-login-icon layui-icon layui-icon-password" for="user-login-password"></label>
          <input type="password" name="password" id="user-login-password" lay-verify="required" placeholder="密码" class="layui-input">
        </div>
        <div class="layui-form-item" style="margin-bottom: 20px;">
          <input type="checkbox" name="remember" lay-skin="primary" title="记住密码">
          <a href="javascript:layer.alert('暂不开放此功能');" class="qn-user-jump-change qn-link" style="margin-top: 7px;">忘记密码？</a>
        </div>
        <div class="layui-form-item">
          <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="*">登 入</button>
        </div>
        </form>
      </div>
    </div>
    
    <div class="layui-trans qn-user-login-footer">
      <p>&copy; 2015~2018 <?php echo (QN_VERSION_NAME); ?> V<?php echo (QN_VERSION); ?> with <?php echo (QN_VERSION_COPYRIGHT); ?></p>
    </div>

</div>
<script src="/app/admin/static/layui/layui.js" type="text/javascript"></script>
<script>
layui.config({
  version: "2.0.1"
  ,base: '/app/admin/static/mods/'
}).extend({
  login: 'login'
}).use('login');
</script>
</body>
</html>