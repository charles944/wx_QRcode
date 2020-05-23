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
		<h3>安装</h3>
	    <div id="show-list" class="install-database" style="height:500px;overflow: auto;border:0px;">
	    </div>
	    <script type="text/javascript">
			var list   = document.getElementById('show-list');
			function showmsg(msg, classname){
				var li = document.createElement('p'); 
				li.innerHTML = msg;
				classname && li.setAttribute('class', classname);
				list.appendChild(li);
				var w = document.getElementById("progress-inner").getAttribute("title");
				var n = Math.min( Math.random() * 2, 100 );
				var a = parseFloat(w)+parseFloat(n);
				document.getElementById("install-tip").setAttribute('class', 'layui-btn layui-btn-radius layui-btn-disabled state-loading');
				
				if( parseInt(w) > 100 ) {
					document.getElementById("install-tip").setAttribute('class', 'layui-btn layui-btn-radius layui-btn-disabled finished');
					document.getElementById("progress-inner").style.width = '100%';
				}else{
					document.getElementById("progress-inner").setAttribute('title', a);
					document.getElementById("progress-inner").style.width = a+'%';
				}
			}
			function showovermsg(msg,url){
				document.getElementById("progress-inner").style.width = '100%';
				document.getElementById("install-tip").setAttribute('class', 'layui-btn layui-btn-radius finished');
				document.getElementById("msg").innerHTML = msg;
				document.getElementById("install-tip").setAttribute('onclick', 'location.href="'+url+'"');
			}
	    </script>
	    <div class="footer">
			<button class="layui-btn layui-btn-radius layui-btn-disabled" id="install-tip">
			<span class="content" id="msg">正在安装，稍候片刻...</span>
			<span class="progress"><span class="progress-inner notransition" id="progress-inner" style="width: 0%; opacity: 1;" title="0"></span></span>
			</button>
			<i class="layui-icon" id="view-list" title="查看安装进度" style="font-size: 30px; color: #1E9FFF;margin-left:20px;line-height: 38px;display: inline-block;vertical-align: bottom;cursor:pointer;">&#xe609;</i>  
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