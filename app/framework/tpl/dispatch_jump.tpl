<extend name="base/common"/>

<block name="body">
<?php
$img_id = modC('JUMP_BACKGROUND','','config');
if($img_id){
  $background =get_cover($img_id,'path');
}else{
$background = '__PUBLIC__/images/jump_background.jpg';
}
?>
<script type="text/javascript">
    $(function () {
        $(window).resize(function () {
            $(".wrapper").css("min-height", $(window).height() - 143);
        }).resize();
    })
</script>
<div class="wrapper" style="padding:300px 100px 0 100px;min-height: 650px; background: url(<?php echo($background); ?>)">

<div class="text-center " style="margin: 0 auto;">
<?php if(isset($success_message)) {?>
<div class="alert alert-success">
    <div class="">
        <p class="font"><?php echo($success_message); ?></p>
    </div>
</div>
<?php }else{?>
<div class="alert alert-success">
    <div class="">
        <p class="font"> <?php echo($error_message); ?></p>
    </div>
</div>
<?php }?>
<p class="jump" style="margin-top:20px">
    页面自动 <a id="href" style="color: green" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait">5</b> 或 <a href="http://{$_SERVER['HTTP_HOST']}__ROOT__" style="color: green">返回首页</a>
</p>
</div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</block>