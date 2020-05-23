<?php if (!defined('THINK_PATH')) exit();?><div class="qn-user-login qn-user-display-show" id="user-login">
    <div class="qn-user-login-main">
      <div class="qn-user-login-box qn-user-login-header">
        <h2><?php echo modC('WEB_SITE_NAME','','CONFIG');?></h2>
        <p>感谢你的再次使用</p>
      </div>
      <div class="qn-user-login-box qn-user-login-body layui-form">
      	<form method="post" action="/login">
        <div class="layui-form-item">
          <label class="qn-user-login-icon layui-icon layui-icon-username" for="user-login-username"></label>
          <input type="text" id="user-login-username" class="layui-input"  lay-verify="required" placeholder="请输入<?php echo ($ph); ?>" ajaxurl="/member/checkUserNameUnique.html" errormsg="请填写4-32位用户名" nullmsg="请填写用户名" datatype="*4-32" value="" name="username" autocomplete="off" />
        </div>
        <div class="layui-form-item">
          <label class="qn-user-login-icon layui-icon layui-icon-password" for="user-login-password"></label>
          <input type="password" id="user-login-password" class="layui-input" lay-verify="required" autocomplete="off" placeholder="请输入密码" errormsg="密码为6-30位" nullmsg="请填写密码" datatype="*6-30" name="password" />
        </div>
        <?php if(check_verify_open('login')): ?><div class="layui-form-item">
          <div class="layui-row">
            <div class="layui-col-xs7">
              <label class="qn-user-login-icon layui-icon layui-icon-vercode" for="user-login-vercode"></label>
              <input type="text" class="layui-input" id="verifyCode" required="" lay-verify="required" autocomplete="off" placeholder="验证码" errormsg="请填写验证码" nullmsg="请填写验证码" datatype="*5-5" name="verify" />
            </div>
            <div class="layui-col-xs5">
              <div style="margin-left: 10px;">
                <img alt="点击切换" src="<?php echo U('verify');?>" class="verifyimg reloadverify qn-user-login-codeimg" id="user-get-vercode" style="height:38px">
              </div>
            </div>
          </div>
        </div><?php endif; ?>
        <div class="layui-form-item" style="margin-bottom: 20px;">
          <input type="checkbox" name="remember" value="1" checked lay-skin="primary" title="记住密码">
          <a href="<?php echo U('home/member/mi');?>" class="qn-user-jump-change qn-link" style="margin-top: 7px;">忘记密码？</a>
        </div>
        <div class="layui-form-item">
        	<input name="from" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">
			<?php session('login_http_referer',$_SERVER['HTTP_REFERER']); ?>
          <button class="layui-btn layui-btn-fluid" lay-filter="login" lay-submit>登 入</button>
        </div>
    </form>
        <div class="layui-trans layui-form-item qn-user-login-other">
          <label>社交账号登入</label>
          <?php echo hook('synclogin');?>
          <a href="<?php echo U('home/member/register');?>" class="qn-user-jump-change qn-link">注册帐号</a>
        </div>
      </div>
    </div>
    <div class="layui-trans qn-user-login-footer">
    	<p>&copy; 2018 <a href="" target="_blank"><?php echo get_domain();?></a></p>
    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    var quickLogin = "<?php echo ($login_type); ?>";
    $(function () {
		var verifyimg = $(".verifyimg").attr("src");
		$(".reloadverify").click(function () {
			if (verifyimg.indexOf('?') > 0) {
				$(".verifyimg").attr("src", verifyimg + '&random=' + Math.random());
			} else {
				$(".verifyimg").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
			}
		});
	});
</script>