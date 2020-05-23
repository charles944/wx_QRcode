<?php
$dirname = './~runtime/';

//清文件缓存
$dirs	=	array($dirname);

if(function_exists('memcache_init')){
    $mem = memcache_init();
    $mem->flush();
}
header('Content-Type:text/html;charset=utf-8');
//清理缓存
foreach($dirs as $value) {
	rmdirr($value);
	
	echo "<div style='border:2px solid #33ab07; border-radius:10px; background:#e8fce1; padding:20px;margin:100px auto;width:300px;font-weight:bold;color:#33ab07;text-align:center;'><img src='public/images/qnlogo.png'/><br><br>缓存清理完毕!</div>";
}

@mkdir($dirname,0777,true);

function rmdirr($dirname) {
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}
	$dir = dir($dirname);
	if($dir){
		while (false !== $entry = $dir->read()) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
	}
	$dir->close();
	return rmdir($dirname);
}
function U(){
	return false;
}
?>