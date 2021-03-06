<?php
/**
 * 自动缓存
 * @param $key
 * @param $interval
 * @param $func
 * @return mixed
 */
function op_cache($key, $func, $interval)
{
    $result = S($key);
    if (!$result) {
        $result = $func();
        S($key, $result, $interval);
    }
    return $result;
}

/**清理全部缓存
 */
function clean_all_cache()
{
    $dirname = './~runtime/';

	//清文件缓存
    $dirs = array($dirname);
	//清理缓存
    foreach ($dirs as $value) {
        rmdirr($value);
    }
    @mkdir($dirname, 0777, true);
}


function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
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

/**
 * 清理指定位置缓存
 * @param $dirname
 */
function clean_cache($dirname='./~runtime/')
{
//清文件缓存
    $dirs = array($dirname);
//清理缓存
    foreach ($dirs as $value) {
        rmdirr($value);
    }
    @mkdir($dirname, 0777, true);
}