<?php
header("Content-type: text/html; charset=utf-8");
set_time_limit ( 0 );
if (isset ( $_GET ['dir'] )) { // 设置文件目录
	$basedir = $_GET ['dir'];
} else {
	$basedir = '.';
}
$auto = 1;
checkdir ( $basedir );
function checkdir($basedir) {
	if ($dh = opendir ( $basedir )) {
		while ( ($file = readdir ( $dh )) !== false ) {
			if ($file != '.' && $file != '..' && $file != '.svn') {
				if (! is_dir ( $basedir . "/" . $file )) {
					$res = checkBOM ( "$basedir/$file" );
					if ($res != '没有检测到 BOM 头.') {
						echo "文件名: $basedir/$file " . $res . " <br>";
					}
				} else {
					$dirname = $basedir . "/" . $file;
					checkdir ( $dirname );
				}
			}
		}
		closedir ( $dh );
	}
}
function checkBOM($filename) {
	global $auto;
	$contents = file_get_contents ( $filename );
	$charset [1] = substr ( $contents, 0, 1 );
	$charset [2] = substr ( $contents, 1, 1 );
	$charset [3] = substr ( $contents, 2, 1 );
	if (ord ( $charset [1] ) == 239 && ord ( $charset [2] ) == 187 && ord ( $charset [3] ) == 191) {
		if ($auto == 1) {
			$rest = substr ( $contents, 3 );
			rewrite ( $filename, $rest );
			return ("<font color=red>检测到 BOM 头, 自动删除.</font>");
		} else {
			return ("<font color=red>检测到 BOM 头.</font>");
		}
	} else
		return ("没有检测到 BOM 头.");
}
function rewrite($filename, $data) {
	$filenum = fopen ( $filename, "w" );
	flock ( $filenum, LOCK_EX );
	fwrite ( $filenum, $data );
	fclose ( $filenum );
}
?>
