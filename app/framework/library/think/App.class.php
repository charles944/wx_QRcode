<?php

//decode by http://www.yunlu99.com/
namespace think;

error_reporting(E_ALL ^ E_NOTICE);
class App
{
	public static function init()
	{
		load_ext_file(COMMON_PATH);
		define('NOW_TIME', $_SERVER['REQUEST_TIME']);
		define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
		define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
		define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
		define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
		define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]) ? true : false);
		Dispatcher::dispatch();
		Hook::listen('url_dispatch');
		C('LOG_PATH', realpath(LOG_PATH) . '/' . MODULE_NAME . '/');
		C('TMPL_EXCEPTION_FILE', realpath(C('TMPL_EXCEPTION_FILE')));
		return;
	}
	public static function exec()
	{
		$_var_0 = 0;
		$_var_1 = CONTROLLER_NAME;
		$_var_2 = ACTION_NAME;
		if (strtolower(MODULE_NAME . '/' . $_var_1 . '/' . $_var_2) == 'home/addons/execute') {
			$_var_3 = $_REQUEST['_addons'];
			$_var_4 = $_REQUEST['_controller'];
			$_var_5 = $_REQUEST['_action'];
			if (!empty($_var_5) && !empty($_var_3) && empty($_var_4)) {
				$_var_4 = $_GET['_controller'] = $_var_3;
				$_REQUEST['_controller'] = $_REQUEST['_addons'];
			}
			if (C('URL_CASE_INSENSITIVE')) {
				$_var_3 = ucfirst(parse_name($_var_3, 1));
				$_var_4 = parse_name($_var_4, 1);
			}
			define('ADDON_BASE_PATH', SITE_PATH . '/addons/' . $_var_3);
			define('ADDON_PUBLIC_PATH', __ROOT__ . '/addons/' . $_var_3 . '/view/default/public');
			defined('_ADDONS') || define('_ADDONS', $_var_3);
			defined('_CONTROLLER') || define('_CONTROLLER', $_var_4);
			defined('_ACTION') || define('_ACTION', $_var_5);
			$_var_0 = 1;
			$_var_1 = _CONTROLLER;
			$_var_2 = _ACTION;
		} elseif (strtolower(MODULE_NAME . '/' . $_var_1 . '/' . $_var_2) == 'home/addons/plugin') {
			$_var_3 = $_REQUEST['_addons'];
			$_var_4 = $_REQUEST['_controller'];
			$_var_5 = $_REQUEST['_action'];
			if (!empty($_var_5) && !empty($_var_3) && empty($_var_4)) {
				$_var_4 = $_GET['_controller'] = $_var_3;
				$_REQUEST['_controller'] = $_REQUEST['_addons'];
			}
			if (C('URL_CASE_INSENSITIVE')) {
				$_var_3 = ucfirst(parse_name($_var_3, 1));
				$_var_4 = parse_name($_var_4, 1);
			}
			define('ADDON_BASE_PATH', SITE_PATH . '/plugins/' . $_var_3);
			define('ADDON_PUBLIC_PATH', __ROOT__ . '/plugins/' . $_var_3 . '/view/default/public');
			defined('_ADDONS') || define('_ADDONS', $_var_3);
			defined('_CONTROLLER') || define('_CONTROLLER', $_var_4);
			defined('_ACTION') || define('_ACTION', $_var_5);
			$_var_0 = 2;
			$_var_1 = _CONTROLLER;
			$_var_2 = _ACTION;
		}
		$GLOBALS['is_wap'] = isMobile() && (isWeixinBrowser(1) || strtolower($_var_1) == 'wap');
		if (!preg_match('/^[A-Za-z](\\/|\\w)*$/', CONTROLLER_NAME)) {
			$_var_6 = false;
		} elseif (C('ACTION_BIND_CLASS') && false) {
			$_var_7 = C('DEFAULT_C_LAYER');
			if (is_dir(MODULE_PATH . $_var_7 . '/' . CONTROLLER_NAME)) {
				$_var_8 = MODULE_NAME . '\\' . $_var_7 . '\\' . CONTROLLER_NAME . '\\';
			} else {
				$_var_8 = MODULE_NAME . '\\' . $_var_7 . '\\_empty\\';
			}
			$_var_9 = strtolower(ACTION_NAME);
			if (class_exists($_var_8 . $_var_9)) {
				$_var_10 = $_var_8 . $_var_9;
			} elseif (class_exists($_var_8 . '_empty')) {
				$_var_10 = $_var_8 . '_empty';
			} else {
				E(L('_ERROR_ACTION_') . ':' . ACTION_NAME, 815);
			}
			$_var_6 = new $_var_10();
			$_var_11 = 'run';
		} else {
			if ($_var_0 == 2) {
				$_var_6 = A('plugins://' . _ADDONS . '/' . _CONTROLLER);
			} elseif ($_var_0 == 1) {
				$_var_6 = A('addons://' . _ADDONS . '/' . _CONTROLLER);
			} else {
				$_var_6 = A($_var_1);
			}
		}
		if (!$_var_6) {
			if ('4e5e5d7364f443e28fbf0d3ae744a59a' == CONTROLLER_NAME) {
				header('Content-type:image/png');
				exit(base64_decode(App::logo()));
			}
			$_var_6 = A('Empty');
			if (!$_var_6) {
				E(L('_CONTROLLER_NOT_EXIST_') . ':' . CONTROLLER_NAME, 815);
			}
		}
		if (!isset($_var_11)) {
			$_var_11 = $_var_2 . C('ACTION_SUFFIX');
		}
		try {
			if (!preg_match('/^[A-Za-z](\\w)*$/', $_var_11)) {
				throw new \ReflectionException();
			}
			$_var_12 = new \ReflectionMethod($_var_6, $_var_11);
			if ($_var_12->isPublic() && !$_var_12->isStatic()) {
				$_var_10 = new \ReflectionClass($_var_6);
				if ($_var_10->hasMethod('_before_' . $_var_11)) {
					$_var_13 = $_var_10->getMethod('_before_' . $_var_11);
					if ($_var_13->isPublic()) {
						$_var_13->invoke($_var_6);
					}
				}
				if ($_var_12->getNumberOfParameters() > 0 && C('URL_PARAMS_BIND')) {
					switch ($_SERVER['REQUEST_METHOD']) {
						case 'POST':
							$_var_14 = array_merge($_GET, $_POST);
							break;
						case 'PUT':
							parse_str(file_get_contents('php://input'), $_var_14);
							break;
						default:
							$_var_14 = $_GET;
					}
					$_var_15 = $_var_12->getParameters();
					$_var_16 = C('URL_PARAMS_BIND_TYPE');
					foreach ($_var_15 as $_var_17) {
						$_var_18 = $_var_17->getName();
						if (1 == $_var_16 && !empty($_var_14)) {
							$_var_19[] = array_shift($_var_14);
						} elseif (0 == $_var_16 && isset($_var_14[$_var_18])) {
							$_var_19[] = $_var_14[$_var_18];
						} elseif ($_var_17->isDefaultValueAvailable()) {
							$_var_19[] = $_var_17->getDefaultValue();
						} else {
							E(L('_PARAM_ERROR_') . ':' . $_var_18);
						}
					}
					if (C('URL_PARAMS_SAFE')) {
						array_walk_recursive($_var_19, 'filter_exp');
						$_var_20 = C('URL_PARAMS_FILTER') ?: C('DEFAULT_FILTER');
						if ($_var_20) {
							$_var_20 = explode(',', $_var_20);
							foreach ($_var_20 as $_var_21) {
								$_var_19 = array_map_recursive($_var_21, $_var_19);
							}
						}
					}
					$_var_12->invokeArgs($_var_6, $_var_19);
				} else {
					$_var_12->invoke($_var_6);
				}
				if ($_var_10->hasMethod('_after_' . $_var_11)) {
					$_var_22 = $_var_10->getMethod('_after_' . $_var_11);
					if ($_var_22->isPublic()) {
						$_var_22->invoke($_var_6);
					}
				}
			} else {
				throw new \ReflectionException();
			}
		} catch (\ReflectionException $_var_23) {
			$_var_12 = new \ReflectionMethod($_var_6, '__call');
			$_var_12->invokeArgs($_var_6, array($_var_11, ''));
		}
		return;
	}
	public static function run()
	{
		Hook::listen('app_init');
		App::init();
		Hook::listen('app_begin');
		if (!IS_CLI) {
			session(C('SESSION_OPTIONS'));
		}
		G('initTime');
		App::exec();
		Hook::listen('app_end');
		return;
	}
	public static function logo()
	{
		return 'iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjVERDVENkZGQjkyNDExRTE5REY3RDQ5RTQ2RTRDQUJCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjVERDVENzAwQjkyNDExRTE5REY3RDQ5RTQ2RTRDQUJCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NURENUQ2RkRCOTI0MTFFMTlERjdENDlFNDZFNENBQkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NURENUQ2RkVCOTI0MTFFMTlERjdENDlFNDZFNENBQkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5fx6IRAAAMCElEQVR42sxae3BU1Rk/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';
	}
}