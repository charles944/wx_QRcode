<?php

//decode by http://www.sucaihuo.com/
error_reporting(E_ALL ^ E_NOTICE);
$GLOBALS['_beginTime'] = microtime(true);
define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
if (MEMORY_LIMIT_ON) {
	$GLOBALS['_startUseMems'] = memory_get_usage();
}
const THINK_VERSION = '3.2.2';
const URL_COMMON = 0;
const URL_PATHINFO = 1;
const URL_REWRITE = 2;
const URL_COMPAT = 3;
const EXT = '.class.php';
defined('THINK_PATH') || define('THINK_PATH', __DIR__ . '/');
defined('APP_PATH') || define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
defined('QN_PLUGIN_PATH') || define('QN_PLUGIN_PATH', dirname(__FILE__) . '/plugins/');
defined('APP_STATUS') || define('APP_STATUS', 'config');
defined('APP_DEBUG') || define('APP_DEBUG', true);
if (function_exists('saeAutoLoader')) {
	defined('APP_MODE') || define('APP_MODE', 'sae');
	defined('STORAGE_TYPE') || define('STORAGE_TYPE', 'Sae');
} else {
	defined('APP_MODE') || define('APP_MODE', 'common');
	defined('STORAGE_TYPE') || define('STORAGE_TYPE', 'File');
}
defined('RUNTIME_PATH') || define('RUNTIME_PATH', APP_PATH . '~runtime/');
defined('LIB_PATH') || define('LIB_PATH', realpath(THINK_PATH . 'library') . '/');
defined('CORE_PATH') || define('CORE_PATH', LIB_PATH . 'think/');
defined('BEHAVIOR_PATH') || define('BEHAVIOR_PATH', LIB_PATH . 'behavior/');
defined('MODE_PATH') || define('MODE_PATH', THINK_PATH . 'mode/');
defined('VENDOR_PATH') || define('VENDOR_PATH', LIB_PATH . 'vendor/');
defined('COMMON_PATH') || define('COMMON_PATH', APP_PATH . 'common/');
defined('CONF_PATH') || define('CONF_PATH', COMMON_PATH . 'conf/');
defined('LANG_PATH') || define('LANG_PATH', COMMON_PATH . 'lang/');
defined('HTML_PATH') || define('HTML_PATH', APP_PATH . 'html/');
defined('LOG_PATH') || define('LOG_PATH', RUNTIME_PATH . 'logs/');
defined('TEMP_PATH') || define('TEMP_PATH', RUNTIME_PATH . 'temp/');
defined('DATA_PATH') || define('DATA_PATH', RUNTIME_PATH . 'data/');
defined('CACHE_PATH') || define('CACHE_PATH', RUNTIME_PATH . 'cache/');
defined('CONF_EXT') || define('CONF_EXT', '.php');
defined('CONF_PARSE') || define('CONF_PARSE', '');
defined('ADDON_PATH') || define('ADDON_PATH', APP_PATH . 'addon');
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	ini_set('magic_quotes_runtime', 0);
	define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
} else {
	define('MAGIC_QUOTES_GPC', false);
}
define('IS_CGI', 0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi') ? 1 : 0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
if (!IS_CLI) {
	if (!defined('_PHP_FILE_')) {
		if (IS_CGI) {
			$_temp = explode('.php', $_SERVER['PHP_SELF']);
			define('_PHP_FILE_', rtrim(str_replace($_SERVER['HTTP_HOST'], '', $_temp[0] . '.php'), '/'));
		} else {
			define('_PHP_FILE_', rtrim($_SERVER['SCRIPT_NAME'], '/'));
		}
	}
	if (!defined('__ROOT__')) {
		$_root = rtrim(dirname(_PHP_FILE_), '/');
		define('__ROOT__', $_root == '/' || $_root == '\\' ? '' : $_root);
	}
}
define('SITE_DOMAIN', strip_tags($_SERVER['HTTP_HOST']));
define('SITE_URL', 'http://' . SITE_DOMAIN . __ROOT__);
define('SITE_DIR_NAME', str_replace('.', '_', pathinfo(SITE_PATH, PATHINFO_BASENAME)));
require CORE_PATH . 'Think' . EXT;
think\Think::start();