<?php if (!defined('THINK_PATH')) exit();?><div class="layui-col-md6">
<div class="layui-card">
	<div class="layui-card-header">服务器信息</div>
	<div class="layui-card-body">
		<table class="layui-table">
            <colgroup>
              <col width="180">
              <col>
            </colgroup>
            <tbody>
              <tr>
                <td>内核版本</td>
                <td>
                  靑年PHP Ver<?php echo (QN_VERSION); ?>
                </td>
              </tr>
              <tr>
                <td>网站域名/IP</td>
                <td>
                  <?php echo get_domain().'&nbsp;/&nbsp;'.gethostbyname($_SERVER['SERVER_NAME']).'&nbsp;/&nbsp;'.long2ip(get_client_ip(1)); ?>
               </td>
              </tr>
              <tr>
                <td>服务器对外IP</td>
                <td>
                  <font><?php echo gethostbyname($_SERVER['SERVER_NAME']);?></font>
                </td>
              </tr>
              <tr>
                <td>服务器所在地</td>
                <td>
                  <font><?php echo ipfrom(gethostbyname($_SERVER['SERVER_NAME']));?></font>
                </td>
              </tr>
              <tr>
        				<td>ThinkPHP版本 / PHP版本</td>
        				<td>THINKPHP <?php echo (THINK_VERSION); ?> / PHP <?php echo (PHP_VERSION); ?></td>
              </tr>
              <tr>
                <td>MYSQL 信息</td>
                <td>
                	<?php $system_info_mysql = M()->query("select version() as v;"); ?>
							 版本：<?php echo ($system_info_mysql["0"]["v"]); ?>，已用：<?php echo ($info["mysqlsize"]); ?>
                </td>
              </tr>
              <tr>
        				<td>运行环境</td>
        				<td><?php echo ($_SERVER['SERVER_SOFTWARE']); ?></td>
              </tr>
              <tr>
        				<td>上传限制</td>
        				<td><?php echo ini_get('upload_max_filesize');?></td>
              </tr>
              <tr>
                <td>产品研发团队</td>
                <td style="padding-bottom: 0;">
                  朝兮夕兮，那你自己想想；版权所有：<?php echo QN_VERSION_COPYRIGHT;?>...
                </td>
              </tr>
            </tbody>
        </table>
	</div>
  <script type="text/javascript">
    // $(document).ready(function(){
    //   $.getScript('http://pv.sohu.com/cityjson?ie=utf-8',function(){
    //     $("#ip").html(returnCitySN.cip);
    //     $("#address").html(returnCitySN.cname);
    //   })
    // })
  </script>
</div>
</div>