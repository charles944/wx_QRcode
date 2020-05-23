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
	
<div class="container" id="step1">
	<div id="logo"  style="min-height:50px">&nbsp;</div>
    <table class="layui-table w50 m0c">
        <caption><h3>运行环境检查</h3></caption>
        <thead>
            <tr>
                <th style="width:30%">项目</th>
                <th style="width:30%">所需配置</th>
                <th style="width:40%">当前配置</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($env)): $i = 0; $__LIST__ = $env;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($item[0]); ?></td>
                    <td><?php echo ($item[1]); ?></td>
                    <td><i class="layui-icon <?php echo ($item[5]); ?>"><?php echo ($item[4]); ?></i><?php echo ($item[3]); ?></td>       
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
	<?php if(isset($dirfile)): ?><table class="layui-table w50 m0c">
        <caption><h3>目录、文件权限检查</h3></caption>
        <thead>
            <tr>
                <th style="width:30%">目录/文件</th>
                <th style="width:30%">所需状态</th>
                <th style="width:40%">当前状态</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($dirfile)): $i = 0; $__LIST__ = $dirfile;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($item[3]); ?></td>
                    <td>可写</td>
                    <td><i class="layui-icon <?php echo ($item[4]); ?>"><?php echo ($item[2]); ?></i><?php echo ($item[1]); ?></td>   
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table><?php endif; ?>
    <table class="layui-table w50 m0c">
        <caption><h3>函数依赖性检查</h3></caption>
        <thead>
            <tr>
                <th style="width:60%">函数名称</th>
                <th style="width:40%">检查结果</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($func)): $i = 0; $__LIST__ = $func;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($item[0]); ?>()</td>
                    <td><i class="layui-icon <?php echo ($item[3]); ?>"><?php echo ($item[2]); ?></i><?php echo ($item[1]); ?></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>

	<footer class="footer">
		<div style="text-align:center">
			
    <a class="layui-btn layui-btn-radius layui-btn-primary" href="<?php echo U('Index/index');?>">返回上一步</a>
    <a class="layui-btn layui-btn-radius layui-btn-normal" href="<?php echo U('Install/step2');?>">下一步</a>

		</div>
	</footer>
	<div style="clear:both"></div>
    </body>
</html>