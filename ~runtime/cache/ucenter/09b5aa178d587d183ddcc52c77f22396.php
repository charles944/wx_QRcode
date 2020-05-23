<?php if (!defined('THINK_PATH')) exit();?><div class="layui-input-inline"><select class="" name="province" id="J_province" lay-filter="province"><option value=''>-省份-</option></select></div>
<div class="layui-input-inline"><select class=""  name="city" id="J_city" lay-filter="city"><option value=''>-城市-</option></select></div>
<div class="layui-input-inline"><select class=""  name="district" id="J_district" lay-filter="district"><option value=''>-州县-</option></select></div>

<script type="text/javascript">
var $form;
var form;
var $;
var layer;
var pid=<?php if($param["province"] != ''): echo ($param["province"]); else: ?>0<?php endif; ?>;  //默认省份id
var cid=<?php if($param["city"] != ''): echo ($param["city"]); else: ?>0<?php endif; ?>;  //默认城市id
var did=<?php if($param["district"] != ''): echo ($param["district"]); else: ?>0<?php endif; ?>;  //默认区县市id

layui.use(['jquery','layer','form'], function(){
	$ = layui.jquery;
	layer = layui.layer;
	form = layui.form;
	$form = $('#form1');
	loadProvince(pid);
	loadcity(pid,cid);
    loaddistrict(cid,did);
    form.on('select(province)', function(data) {
		console.log(data);
		var pid_g= data.value;
		loadcity(pid_g);
        loaddistrict(0);
	});
	form.on('select(city)', function(data) {
		var cid_g = data.value;
		loaddistrict(cid_g);
	});
	form.on('select(district)', function(data) {
		var did_g= data.value;
	 });
});

//加载省数据
function loadProvince(pid) {
	$.post("<?php echo addons_url('Chinacity://Chinacity/getProvince');?>", {pid: pid}, function(result){
		$form.find('select[name=province]').html(result);
		form.render('select');
	});
	form.render();
}

function loadcity(p_pid,p_cid){
	$.post('<?php echo addons_url("Chinacity://Chinacity/getCity");?>', {pid: p_pid, cid: p_cid}, function(result){
		$form.find('select[name=city]').html(result);
		form.render('select');
	});
	form.render();
}

function loaddistrict(p_cid,p_did){
	$.post('<?php echo addons_url("Chinacity://Chinacity/getDistrict");?>', {cid: p_cid, did: p_did}, function(result){
		$form.find('select[name=district]').html(result);
		form.render('select');
	});
	form.render();
}
</script>