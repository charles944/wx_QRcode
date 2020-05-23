<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
$(document).delegate(".selectImg", "click", function() {
	$name = $(this).data("name");
	$id   = $(this).data("id");
	$type = $(this).data("type");
	layer.open({
	  type: 2,
	  title: '浏览服务器图片',
	  shadeClose: true,
	  shade: 0.8,
	  area: ['40%', '70%'],
	  scrollbar: false,
	  content: "<?php echo addons_url('Imagemanager://Imagemanager/Imagemanager');?>&name="+$name+"&t="+$type, //iframe的url
	});
});
</script>