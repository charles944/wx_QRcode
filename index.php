<?php
//error_reporting(E_ALL || ~ E_NOTICE);

ini_set ( 'display_errors', true );		//调试、找错时请去掉///前空格
error_reporting ( E_ALL );				//调试、找错时请去掉///前空格
set_time_limit ( 0 );

function reset_session_path()
{
    $root = str_replace("\\", '/', dirname(__FILE__));
    $savePath = $root . "/tmp/";
    if (!file_exists($savePath))
       @mkdir($savePath, 0777);
    session_save_path($savePath);
}

//reset_session_path();  //如果您的服务器无法安装或者无法登陆，又或者后台验证码无限错误，请尝试取消本行起始两条左斜杠，让本行代码生效，以修改session存储的路径

date_default_timezone_set ( 'PRC' );
if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('当前PHP版本'. PHP_VERSION .'，最低要求PHP版本5.3.0 <br/><br/>最低要求PHP版本5.3.0 <br/><br/>很遗憾，未能达到最低要求。本系统必须运行在PHP5.3 及以上版本。如果您是虚拟主机，请联系空间商升级PHP版本，如果您是VPS用户，请自行升级php版本或者联系VPS提供商寻求技术支持。');

// 系统调试设置，项目正式部署后请设置为false，使用Zend解密组件必须设置为 true
define ( 'APP_DEBUG', true );

/*移除magic_quotes_gpc参数影响*/
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
/*移除magic_quotes_gpc参数影响end*/

// 应用目录
define ( 'APP_PATH', './app/' );
// 缓存目录设置，此目录必须可写
define ( 'RUNTIME_PATH', './~runtime/' );

// 网站主题目录
define ( 'QN_THEME_PATH', './themes/');
define ( 'QN_ADDON_PATH', './addons/');
define ( 'QN_PLUGIN_PATH', './plugins/');
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

// 官方远程同步服务器地址，应用于后台应用商店、在线升级，配置教程等功能
define ( 'REMOTE_BASE_URL', 'http://www.wpidea.org' );

// 网站根路径设置
define ( 'SITE_PATH', dirname ( __FILE__ ) );
//简写目录分隔符
define( 'DS' , DIRECTORY_SEPARATOR );

// 网站默认token
define ( 'DEFAULT_TOKEN', '-1' ); 

if (!is_file( SITE_PATH.'/conf/user.php')) {
	header('Location: ./install.php');
	exit;
}

// 引入核心
require APP_PATH . 'framework/qiankuncore.php';