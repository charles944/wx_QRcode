<?php
/**
* +----------------------------------------------------------------------------+
* | 年代 PHP  &  NianDaiPHP													    |
* +----------------------------------------------------------------------------+
* | Copyright (c) 2014~2016 http://www.bajiuniandai.com All rights reserved.	|
* +----------------------------------------------------------------------------+
* | Author: 重庆捌玖年代网络科技有限公司版权所有（朝兮夕兮，那你自己想想）		|
* +----------------------------------------------------------------------------+
**/

// 检测环境是否支持可写
define('IS_WRITE',APP_MODE !== 'sae');

/**
 * 系统环境检测
 * @return array 系统环境数据
 * @author 朝兮夕兮，那你自己想想
 */
function check_env(){
	$items = array(
		'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, '&#xe605;', 'green'),
		'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, '&#xe605;', 'green'),
		//'mysql'   => array('MYSQL版本', '5.0', '5.0+', '未知', '&#xe605;', 'green'), //PHP5.5不支持mysql版本检测
		'upload'  => array('附件上传', '不限制', '2M+', '未知', '&#xe605;', 'green'),
		'gd'      => array('GD库', '2.0', '2.0+', '未知', '&#xe605;', 'green'),
		'disk'    => array('磁盘空间', '5M', '不限制', '未知', '&#xe605;', 'green'),
	);

	//PHP环境检测
	if($items['php'][3] < $items['php'][1]){
		$items['php'][4] = '&#x1007;';
		$items['php'][5] = 'red';
		cookie('error', true);
	}

	//数据库检测
	// if(function_exists('mysql_get_server_info')){
	// 	$items['mysql'][3] = mysql_get_server_info();
	// 	if($items['mysql'][3] < $items['mysql'][1]){
	// 		$items['mysql'][4] = '&#x1007;';
	// 		cookie('error', true);
	// 	}
	// }

	//附件上传检测
	if(@ini_get('file_uploads'))
		$items['upload'][3] = ini_get('upload_max_filesize');

	//GD库检测
	$tmp = function_exists('gd_info') ? gd_info() : array();
	if(empty($tmp['GD Version'])){
		$items['gd'][3] = '未安装';
		$items['gd'][4] = '&#x1007;';
		$items['gd'][5] = 'red';
		cookie('error', true);
	} else {
		$items['gd'][3] = $tmp['GD Version'];
	}
	unset($tmp);

	//磁盘空间检测
	if(function_exists('disk_free_space')) {
		$items['disk'][3] = floor(disk_free_space(INSTALL_APP_PATH) / (1024*1024)).'M';
	}

	return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 * @author 朝兮夕兮，那你自己想想
 */
function check_dirfile(){
	$items = array(
		array('dir', '可写', '&#xe605;', './data/backup', 'green'),
        array('dir', '可写', '&#xe605;', './data/temp', 'green'),
        array('dir', '可写', '&#xe605;', './data/cloud', 'green'),
        array('dir', '可写', '&#xe605;', './data/update', 'green'),
		array('dir',  '可写', '&#xe605;', './uploads/download', 'green'),
		array('dir',  '可写', '&#xe605;', './uploads/picture', 'green'),
		array('dir',  '可写', '&#xe605;', './uploads/editor', 'green'),
		array('dir',  '可写', '&#xe605;', './~runtime', 'green'),
		array('file', '可写', '&#xe605;', './conf/user.php', 'green'),
		array('file', '可写', '&#xe605;', './conf/common.php', 'green'),
	);

	foreach ($items as &$val) {
		if('dir' == $val[0]){
			if(!is_writable(INSTALL_APP_PATH . $val[3])) {
				if(is_dir($items[1])) {
					$val[1] = '可读';
					$val[2] = '&#x1007;';
					$val[4] = 'red';
					cookie('error', true);
				} else {
					$val[1] = '不存在';
					$val[2] = '&#x1007;';
					$val[4] = 'red';
					cookie('error', true);
				}
			}
		} else {
			if(file_exists(INSTALL_APP_PATH . $val[3])) {
				if(!is_writable(INSTALL_APP_PATH . $val[3])) {
					$val[1] = '不可写';
					$val[2] = '&#x1007;';
					$val[4] = 'red';
					cookie('error', true);
				}
			} else {
				if(!is_writable(dirname(INSTALL_APP_PATH . $val[3]))) {
					$val[1] = '不存在';
					$val[2] = '&#x1007;';
					$val[4] = 'red';
					cookie('error', true);
				}
			}
		}
	}

	return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){
	$items = array(
		array('mysql_connect',     '支持', '&#xe605;', 'green'),
		array('file_get_contents', '支持', '&#xe605;', 'green'),
		array('mb_strlen',		   '支持', '&#xe605;', 'green'),
	);

	foreach ($items as &$val) {
		if(!function_exists($val[0])){
			$val[1] = '不支持';
			$val[2] = '&#x1007;';
			$val[3] = '开启';
			cookie('error', true);
		}
	}
	return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth){
	if(is_array($config)){
		//读取配置内容
		$conf = file_get_contents(MODULE_PATH . 'data/conf.tpl');
		$user = file_get_contents(MODULE_PATH . 'data/user.tpl');
		//替换配置项
		foreach ($config as $name => $value) {
			$conf = str_replace("[{$name}]", $value, $conf);
			$user = str_replace("[{$name}]", $value, $user);
		}

		$conf = str_replace('[AUTH_KEY]', $auth, $conf);
		$user = str_replace('[AUTH_KEY]', $auth, $user);

		//写入应用配置文件
		if(!IS_WRITE){
			return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>'.realpath('').'./conf/common.php</p>
			<textarea name="" style="width:650px;height:185px">'.$conf.'</textarea>
			<p>'.realpath('').'./conf/user.php</p>
			<textarea name="" style="width:650px;height:125px">'.$user.'</textarea>';
		}else{
			if(file_put_contents('./conf/common.php', $conf) &&
			   file_put_contents('./conf/user.php', $user)){
				chmod('./conf/common.php',0777);
				chmod('./conf/user.php',0777);
				show_msg('配置文件写入成功');
			} else {
				show_msg('配置文件写入失败！', 'error');
				cookie('error', true);
			}
			return '';
		}

	}
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = ''){
	//读取SQL文件
	$sql = file_get_contents(MODULE_PATH . 'data/install.sql');
	$sql = str_replace("\r", "\n", $sql);
	$sql = explode(";\n", $sql);

	//替换表前缀
	$orginal = C('ORIGINAL_TABLE_PREFIX');
	$sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

	//开始安装
	show_msg('开始安装数据库...');
	foreach ($sql as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if(substr($value, 0, 12) == 'CREATE TABLE') {
			$name = preg_replace("/^CREATE TABLE IF NOT EXISTS `(\w+)` .*/s", "\\1", $value);
			$msg  = "创建数据表{$name}";
			if(false !== $db->execute($value)){
				show_msg($msg . '...成功');
			} else {
				show_msg($msg . '...失败！', 'error');
				cookie('error', true);
			}
		} else {
			$db->execute($value);
		}
	}
}

function register_administrator($db, $prefix, $admin, $auth){
	show_msg('开始注册创始人帐号...');
	$uid=1;
	
	$activation_code = S('qn_activation_code');
	if(empty($activation_code)){
		$key = "";
	}else{
		$key = $activation_code;
	}

	$sql = "UPDATE `[PREFIX]config` SET `value` = '[KEY]' where `name` = 'QN_ACTIVATION_CODE'";
	$sql = str_replace(array('[PREFIX]', '[KEY]'),array($prefix, $key),$sql);
	$res = $db->execute($sql);

	$sql=<<<sql
REPLACE INTO `[PREFIX]ucenter_member` (`id`, `username`, `password`, `email`, `email_ver`, `mobile`, `mobile_ver`, `reg_time`, `reg_ip`, `last_login_time`, `last_login_ip`, `update_time`, `status`, `type`) VALUES
([UID], '[NAME]', '[PASS]', '[EMAIL]', 1, '', 0, [TIME], [IP], [TIME], [IP], [TIME], 1, 1);
sql;
	$password = user_md5($admin['password'], $auth);
	$sql = str_replace(
		array('[PREFIX]', '[NAME]', '[PASS]', '[EMAIL]', '[TIME]', '[IP]','[UID]'),
		array($prefix, $admin['username'], $password, $admin['email'], NOW_TIME, get_client_ip(1),$uid),
		$sql);
	//执行sql
	$db->execute($sql);

	$sql =<<<sql
REPLACE INTO `[PREFIX]member` (`uid`, `nickname`, `sex`, `birthday`, `safecode`, `memberno`, `score`, `signature`, `gold_coin`, `pos_province`, `pos_city`, `pos_district`, `pos_community`, `money`, `name`, `purse`, `purse_type`, `lower_gold_coin`, `pay_money`, `qq`, `con_check`, `total_check`, `idcard`, `tj_province`, `tj_broadband`) VALUES
([UID], '[NAME]', 0, '0000-00-00', '4297f44b13955235245b2497399d7a93', '', 0, '', 0.00, 0, 0, 0, 0, 0, '', NULL, 0, 0, 0, '0', 0, 0, NULL, '', NULL);
sql;

	$sql = str_replace(
		array('[PREFIX]', '[NAME]', '[TIME]','[UID]'),
		array($prefix, $admin['username'], NOW_TIME,$uid),
		$sql);
	$db->execute($sql);

	show_msg('创始人帐号注册完成！');
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = ''){
	echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
	ob_flush();
	flush();
}

function show_overmsg($msg, $urls = ''){
	echo "<script type=\"text/javascript\">showovermsg(\"{$msg}\", \"{$urls}\")</script>";
	ob_flush();
	flush();
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function user_md5($str, $key = ''){
	return '' === $str ? '' : md5(sha1($str) . $key);
}