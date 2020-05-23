<?php

//decode by http://www.yunlu99.com/
error_reporting(E_ALL ^ E_NOTICE);
function qnhm_encrypt($_var_0, $_var_1)
{
	$_var_2 = $_var_3 = '';
	$_var_1 = md5($_var_1);
	$_var_4 = 0;
	$_var_5 = strlen($_var_0);
	$_var_6 = strlen($_var_1);
	for ($_var_7 = 0; $_var_7 < $_var_5; $_var_7++) {
		if ($_var_4 == $_var_6) {
			$_var_4 = 0;
		}
		$_var_2 .= $_var_1[$_var_4];
		$_var_4++;
	}
	for ($_var_7 = 0; $_var_7 < $_var_5; $_var_7++) {
		$_var_3 .= chr(ord($_var_0[$_var_7]) + ord($_var_2[$_var_7]) % 256);
	}
	return base64_encode($_var_3);
}
function qnhm_decrypt($_var_8, $_var_9)
{
	$_var_10 = $_var_11 = '';
	$_var_9 = md5($_var_9);
	$_var_12 = 0;
	$_var_8 = base64_decode($_var_8);
	$_var_13 = strlen($_var_8);
	$_var_14 = strlen($_var_9);
	for ($_var_15 = 0; $_var_15 < $_var_13; $_var_15++) {
		if ($_var_12 == $_var_14) {
			$_var_12 = 0;
		}
		$_var_10 .= substr($_var_9, $_var_12, 1);
		$_var_12++;
	}
	for ($_var_15 = 0; $_var_15 < $_var_13; $_var_15++) {
		if (ord(substr($_var_8, $_var_15, 1)) < ord(substr($_var_10, $_var_15, 1))) {
			$_var_11 .= chr(ord(substr($_var_8, $_var_15, 1)) + 256 - ord(substr($_var_10, $_var_15, 1)));
		} else {
			$_var_11 .= chr(ord(substr($_var_8, $_var_15, 1)) - ord(substr($_var_10, $_var_15, 1)));
		}
	}
	return $_var_11;
}
function check_file_md5($_var_16, $_var_17)
{
	$_var_18['file_md5_1'] = md5_file($_var_16);
	$_var_18['file_md5_2'] = md5_file($_var_17);
	if ($_var_18['file_md5_1'] == $_var_18['file_md5_2']) {
		return true;
	}
	return false;
}
function is_mobile()
{
	$_var_19 = $_SERVER['HTTP_USER_AGENT'];
	$_var_20 = array('240x320', 'acer', 'acoon', 'acs-', 'abacho', 'ahong', 'airness', 'alcatel', 'amoi', 'android', 'anywhereyougo.com', 'applewebkit/525', 'applewebkit/532', 'asus', 'audio', 'au-mic', 'avantogo', 'becker', 'benq', 'bilbo', 'bird', 'blackberry', 'blazer', 'bleu', 'cdm-', 'compal', 'coolpad', 'danger', 'dbtel', 'dopod', 'elaine', 'eric', 'etouch', 'fly ', 'fly_', 'fly-', 'go.web', 'goodaccess', 'gradiente', 'grundig', 'haier', 'hedy', 'hitachi', 'htc', 'huawei', 'hutchison', 'inno', 'ipad', 'ipaq', 'ipod', 'jbrowser', 'kddi', 'kgt', 'kwc', 'lenovo', 'lg ', 'lg2', 'lg3', 'lg4', 'lg5', 'lg7', 'lg8', 'lg9', 'lg-', 'lge-', 'lge9', 'longcos', 'maemo', 'mercator', 'meridian', 'micromax', 'midp', 'mini', 'mitsu', 'mmm', 'mmp', 'mobi', 'mot-', 'moto', 'nec-', 'netfront', 'newgen', 'nexian', 'nf-browser', 'nintendo', 'nitro', 'nokia', 'nook', 'novarra', 'obigo', 'palm', 'panasonic', 'pantech', 'philips', 'phone', 'pg-', 'playstation', 'pocket', 'pt-', 'qc-', 'qtek', 'rover', 'sagem', 'sama', 'samu', 'sanyo', 'samsung', 'sch-', 'scooter', 'sec-', 'sendo', 'sgh-', 'sharp', 'siemens', 'sie-', 'softbank', 'sony', 'spice', 'sprint', 'spv', 'symbian', 'tablet', 'talkabout', 'tcl-', 'teleca', 'telit', 'tianyu', 'tim-', 'toshiba', 'tsm', 'up.browser', 'utec', 'utstar', 'verykool', 'virgin', 'vk-', 'voda', 'voxtel', 'vx', 'wap', 'wellco', 'wig browser', 'wii', 'windows ce', 'wireless', 'xda', 'xde', 'zte');
	$_var_21 = false;
	foreach ($_var_20 as $_var_22) {
		if (stristr($_var_19, $_var_22)) {
			$_var_21 = true;
			break;
		}
	}
	return $_var_21;
}
function isMobile()
{
	$_var_23 = array();
	static $_var_24 = 'Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
	if (preg_match("/{$_var_24}/i", $_SERVER['HTTP_USER_AGENT'], $_var_23)) {
		return true;
	} else {
		if (preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
			return false;
		} else {
			if ($_GET['mobile'] === 'yes') {
				return true;
			} else {
				return false;
			}
		}
	}
}
function isiPhone()
{
	return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false;
}
function isiPad()
{
	return strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false;
}
function isiOS()
{
	return isiPhone() || isiPad();
}
function isAndroid()
{
	return strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false;
}
function isWeixinBrowser($_var_25 = 0)
{
	if (!$_var_25 && defined('IN_WEIXIN') && IN_WEIXIN || isset($_GET['is_stree'])) {
		return true;
	}
	$_var_26 = $_SERVER['HTTP_USER_AGENT'];
	if (!strpos($_var_26, 'icroMessenger')) {
		return false;
	}
	return true;
}
function C($_var_27 = null, $_var_28 = null, $_var_29 = null)
{
	static $_var_30 = array();
	if (empty($_var_27)) {
		return $_var_30;
	}
	if (is_string($_var_27)) {
		if (!strpos($_var_27, '.')) {
			$_var_27 = strtoupper($_var_27);
			if (is_null($_var_28)) {
				return isset($_var_30[$_var_27]) ? $_var_30[$_var_27] : $_var_29;
			}
			$_var_30[$_var_27] = $_var_28;
			return;
		}
		$_var_27 = explode('.', $_var_27);
		$_var_27[0] = strtoupper($_var_27[0]);
		if (is_null($_var_28)) {
			return isset($_var_30[$_var_27[0]][$_var_27[1]]) ? $_var_30[$_var_27[0]][$_var_27[1]] : $_var_29;
		}
		$_var_30[$_var_27[0]][$_var_27[1]] = $_var_28;
		return;
	}
	if (is_array($_var_27)) {
		$_var_30 = array_merge($_var_30, array_change_key_case($_var_27, CASE_UPPER));
		return;
	}
	return null;
}
function load_config($_var_31, $_var_32 = CONF_PARSE)
{
	$_var_33 = pathinfo($_var_31, PATHINFO_EXTENSION);
	switch ($_var_33) {
		case 'php':
			return include $_var_31;
		case 'ini':
			return parse_ini_file($_var_31);
		case 'yaml':
			return yaml_parse_file($_var_31);
		case 'xml':
			return (array) simplexml_load_file($_var_31);
		case 'json':
			return json_decode(file_get_contents($_var_31), true);
		default:
			if (function_exists($_var_32)) {
				return $_var_32($_var_31);
			} else {
				E(L('_NOT_SUPPERT_') . ':' . $_var_33);
			}
	}
}
if (!function_exists('yaml_parse_file')) {
	function yaml_parse_file($_var_34)
	{
		vendor('spyc.Spyc');
		return Spyc::YAMLLoad($_var_34);
	}
}
function E($_var_35, $_var_36 = 0)
{
	throw new think\Exception($_var_35, $_var_36);
}
function G($_var_37, $_var_38 = '', $_var_39 = 4)
{
	static $_var_40 = array();
	static $_var_41 = array();
	if (is_float($_var_38)) {
		$_var_40[$_var_37] = $_var_38;
	} elseif (!empty($_var_38)) {
		if (!isset($_var_40[$_var_38])) {
			$_var_40[$_var_38] = microtime(true);
		}
		if (MEMORY_LIMIT_ON && $_var_39 == 'm') {
			if (!isset($_var_41[$_var_38])) {
				$_var_41[$_var_38] = memory_get_usage();
			}
			return number_format(($_var_41[$_var_38] - $_var_41[$_var_37]) / 1024);
		} else {
			return number_format($_var_40[$_var_38] - $_var_40[$_var_37], $_var_39);
		}
	} else {
		$_var_40[$_var_37] = microtime(true);
		if (MEMORY_LIMIT_ON) {
			$_var_41[$_var_37] = memory_get_usage();
		}
	}
}
function L($_var_42 = null, $_var_43 = null)
{
	static $_var_44 = array();
	if (empty($_var_42)) {
		return $_var_44;
	}
	if (is_string($_var_42)) {
		$_var_42 = strtoupper($_var_42);
		if (is_null($_var_43)) {
			return isset($_var_44[$_var_42]) ? $_var_44[$_var_42] : $_var_42;
		} elseif (is_array($_var_43)) {
			$_var_45 = array_keys($_var_43);
			foreach ($_var_45 as &$_var_46) {
				$_var_46 = '{$' . $_var_46 . '}';
			}
			return str_replace($_var_45, $_var_43, isset($_var_44[$_var_42]) ? $_var_44[$_var_42] : $_var_42);
		}
		$_var_44[$_var_42] = $_var_43;
		return;
	}
	if (is_array($_var_42)) {
		$_var_44 = array_merge($_var_44, array_change_key_case($_var_42, CASE_UPPER));
	}
	return;
}
function trace($_var_47 = '[think]', $_var_48 = '', $_var_49 = 'DEBUG', $_var_50 = false)
{
	return think\Think::trace($_var_47, $_var_48, $_var_49, $_var_50);
}
function compile($_var_51)
{
	$_var_52 = php_strip_whitespace($_var_51);
	$_var_52 = trim(substr($_var_52, 5));
	$_var_52 = preg_replace('/\\/\\/\\[RUNTIME\\](.*?)\\/\\/\\[\\/RUNTIME\\]/s', '', $_var_52);
	if (0 === strpos($_var_52, 'namespace')) {
		$_var_52 = preg_replace('/namespace\\s(.*?);/', 'namespace \\1{', $_var_52, 1);
	} else {
		$_var_52 = 'namespace {' . $_var_52;
	}
	if ('?>' == substr($_var_52, -2)) {
		$_var_52 = substr($_var_52, 0, -2);
	}
	return $_var_52 . '}';
}
function T($_var_53 = '', $_var_54 = '')
{
	$_var_55 = C('TMPL_TEMPLATE_SUFFIX');
	if (defined('_ADDONS') && false === strpos($_var_53, '://')) {
		if (empty($_var_53)) {
			$_var_56 = __ROOT__ . '/addons/' . _ADDONS . '/view/default/' . _CONTROLLER . '/' . _ACTION . $_var_55;
		} elseif (false === strpos($_var_53, '/')) {
			$_var_56 = __ROOT__ . '/addons/' . _ADDONS . '/view/default/' . _CONTROLLER . '/' . $_var_53 . $_var_55;
		} else {
			$_var_56 = __ROOT__ . '/addons/' . _ADDONS . '/view/default/' . $_var_53 . $_var_55;
		}
		if (file_exists($_var_56)) {
			return $_var_56;
		} elseif (defined('CUSTOM_TEMPLATE_PATH') && file_exists(CUSTOM_TEMPLATE_PATH . $_var_53 . $_var_55)) {
			return CUSTOM_TEMPLATE_PATH . $_var_53 . $_var_55;
		}
	}
	if (false === strpos($_var_53, '://')) {
		$_var_53 = 'http://' . str_replace(':', '/', $_var_53);
	}
	$_var_57 = parse_url($_var_53);
	$_var_58 = $_var_57['host'] . (isset($_var_57['path']) ? $_var_57['path'] : '');
	$_var_59 = isset($_var_57['user']) ? strtolower($_var_57['user']) . '/' : strtolower(MODULE_NAME) . '/';
	$_var_60 = strtolower($_var_57['scheme']);
	$_var_54 = $_var_54 ? $_var_54 : C('DEFAULT_V_LAYER');
	if (strtolower(MODULE_NAME) != 'admin') {
		$_var_61 = cookie('TO_LOOK_THEME', '', array('prefix' => 'WZBV2'));
		if ($_var_61) {
			if ($_var_61 != 'default') {
				$_var_56 = QN_THEME_PATH . $_var_61 . '/' . $_var_59 . $_var_54 . '/' . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
			}
		} else {
			$_var_62 = modC('NOW_THEME', 'default', 'Theme');
			if ($_var_62 != 'default') {
				$_var_56 = QN_THEME_PATH . $_var_62 . '/' . $_var_59 . $_var_54 . '/' . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
			}
		}
		if (isset($_var_56) && is_file($_var_56)) {
			return $_var_56;
		}
	}
	$_var_63 = C('AUTOLOAD_NAMESPACE');
	if ($_var_63 && isset($_var_63[$_var_60])) {
		$_var_64 = $_var_63[$_var_60] . $_var_59 . $_var_54 . '/';
	} elseif (C('VIEW_PATH')) {
		$_var_64 = C('VIEW_PATH');
	} elseif (defined('TMPL_PATH')) {
		$_var_64 = TMPL_PATH . $_var_59;
	} else {
		$_var_64 = APP_PATH . $_var_59 . $_var_54 . '/';
	}
	$_var_65 = substr_count($_var_58, '/') < 2 ? C('DEFAULT_THEME') : '';
	$_var_66 = C('TMPL_FILE_DEPR');
	if ('' == $_var_58) {
		$_var_58 = CONTROLLER_NAME . $_var_66 . ACTION_NAME;
	} elseif (false === strpos($_var_58, '/')) {
		$_var_58 = CONTROLLER_NAME . $_var_66 . $_var_58;
	} elseif ('/' != $_var_66) {
		$_var_58 = substr_count($_var_58, '/') > 1 ? substr_replace($_var_58, $_var_66, strrpos($_var_58, '/'), 1) : str_replace('/', $_var_66, $_var_58);
	}
	$_var_67 = $_var_64 . ($_var_65 ? $_var_65 . '/' : '') . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
	if (is_file($_var_67)) {
		return $_var_67;
	}
	if (strtolower(MODULE_NAME) != 'admin') {
		$_var_61 = cookie('TO_LOOK_THEME', '', array('prefix' => 'WZBV2'));
		if ($_var_61) {
			if ($_var_61 != 'default') {
				$_var_68 = QN_THEME_PATH . $_var_61 . '/common/' . $_var_54 . '/' . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
			}
		} else {
			$_var_62 = modC('NOW_THEME', 'default', 'Theme');
			if ($_var_62 != 'default') {
				$_var_68 = QN_THEME_PATH . $_var_62 . '/common/' . $_var_54 . '/' . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
			}
		}
		if (isset($_var_68) && is_file($_var_68)) {
			return $_var_68;
		}
	}
	$_var_64 = APP_PATH . 'common/view/' . ($_var_65 ? $_var_65 . '/' : '');
	$_var_67 = $_var_64 . $_var_58 . C('TMPL_TEMPLATE_SUFFIX');
	return $_var_67;
}
function I($_var_69, $_var_70 = '', $_var_71 = null, $_var_72 = null)
{
	if (strpos($_var_69, '.')) {
		list($_var_73, $_var_69) = explode('.', $_var_69, 2);
	} else {
		$_var_73 = 'param';
	}
	switch (strtolower($_var_73)) {
		case 'get':
			$_var_74 =& $_GET;
			break;
		case 'post':
			$_var_74 =& $_POST;
			break;
		case 'put':
			parse_str(file_get_contents('php://input'), $_var_74);
			break;
		case 'param':
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$_var_74 = $_POST;
					break;
				case 'PUT':
					parse_str(file_get_contents('php://input'), $_var_74);
					break;
				default:
					$_var_74 = $_GET;
			}
			break;
		case 'path':
			$_var_74 = array();
			if (!empty($_SERVER['PATH_INFO'])) {
				$_var_75 = C('URL_PATHINFO_DEPR');
				$_var_74 = explode($_var_75, trim($_SERVER['PATH_INFO'], $_var_75));
			}
			break;
		case 'request':
			$_var_74 =& $_REQUEST;
			break;
		case 'session':
			$_var_74 =& $_SESSION;
			break;
		case 'cookie':
			$_var_74 =& $_COOKIE;
			break;
		case 'server':
			$_var_74 =& $_SERVER;
			break;
		case 'globals':
			$_var_74 =& $GLOBALS;
			break;
		case 'data':
			$_var_74 =& $_var_72;
			break;
		default:
			return NULL;
	}
	if ('' == $_var_69) {
		$_var_76 = $_var_74;
		array_walk_recursive($_var_76, 'filter_exp');
		$_var_77 = isset($_var_71) ? $_var_71 : C('DEFAULT_FILTER');
		if ($_var_77) {
			if (is_string($_var_77)) {
				$_var_77 = explode(',', $_var_77);
			}
			foreach ($_var_77 as $_var_71) {
				$_var_76 = array_map_recursive($_var_71, $_var_76);
			}
		}
	} elseif (isset($_var_74[$_var_69])) {
		$_var_76 = $_var_74[$_var_69];
		is_array($_var_76) && array_walk_recursive($_var_76, 'filter_exp');
		$_var_77 = isset($_var_71) ? $_var_71 : C('DEFAULT_FILTER');
		if ($_var_77) {
			if (is_string($_var_77)) {
				$_var_77 = explode(',', $_var_77);
			} elseif (is_int($_var_77)) {
				$_var_77 = array($_var_77);
			}
			foreach ($_var_77 as $_var_71) {
				if (function_exists($_var_71)) {
					$_var_76 = is_array($_var_76) ? array_map_recursive($_var_71, $_var_76) : $_var_71($_var_76);
				} else {
					$_var_76 = filter_var($_var_76, is_int($_var_71) ? $_var_71 : filter_id($_var_71));
					if (false === $_var_76) {
						return isset($_var_70) ? $_var_70 : NULL;
					}
				}
			}
		}
	} else {
		$_var_76 = isset($_var_70) ? $_var_70 : NULL;
	}
	return $_var_76;
}
function array_map_recursive($_var_78, $_var_79)
{
	$_var_80 = array();
	foreach ($_var_79 as $_var_81 => $_var_82) {
		$_var_80[$_var_81] = is_array($_var_82) ? array_map_recursive($_var_78, $_var_82) : call_user_func($_var_78, $_var_82);
	}
	return $_var_80;
}
function N($_var_83, $_var_84 = 0, $_var_85 = false)
{
	static $_var_86 = array();
	if (!isset($_var_86[$_var_83])) {
		$_var_86[$_var_83] = false !== $_var_85 ? S('N_' . $_var_83) : 0;
	}
	if (empty($_var_84)) {
		return $_var_86[$_var_83];
	} else {
		$_var_86[$_var_83] = $_var_86[$_var_83] + (int) $_var_84;
	}
	if (false !== $_var_85) {
		S('N_' . $_var_83, $_var_86[$_var_83], $_var_85);
	}
}
function parse_name($_var_87, $_var_88 = 0)
{
	if ($_var_88) {
		return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($_var_89) {
			return strtoupper($_var_89[1]);
		}, $_var_87));
	} else {
		return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $_var_87), '_'));
	}
}
function require_cache($_var_90)
{
	static $_var_91 = array();
	if (!isset($_var_91[$_var_90])) {
		if (file_exists_case($_var_90)) {
			require $_var_90;
			$_var_91[$_var_90] = true;
		} else {
			$_var_91[$_var_90] = false;
		}
	}
	return $_var_91[$_var_90];
}
function file_exists_case($_var_92)
{
	if (is_file($_var_92)) {
		if (IS_WIN && APP_DEBUG) {
			if (basename(realpath($_var_92)) != basename($_var_92)) {
				return false;
			}
		}
		return true;
	}
	return false;
}
function import($_var_93, $_var_94 = '', $_var_95 = EXT)
{
	static $_var_96 = array();
	$_var_93 = str_replace(array('.', '#'), array('/', '.'), $_var_93);
	if (isset($_var_96[$_var_93 . $_var_94])) {
		return true;
	} else {
		$_var_96[$_var_93 . $_var_94] = true;
	}
	$_var_97 = explode('/', $_var_93);
	if (empty($_var_94)) {
		if ('@' == $_var_97[0] || MODULE_NAME == $_var_97[0]) {
			$_var_94 = MODULE_PATH;
			$_var_93 = substr_replace($_var_93, '', 0, strlen($_var_97[0]) + 1);
		} elseif (in_array($_var_97[0], array('Think', 'Org', 'Behavior', 'Com', 'Vendor')) || is_dir(LIB_PATH . $_var_97[0])) {
			$_var_94 = LIB_PATH;
		} else {
			$_var_94 = APP_PATH;
		}
	}
	if (substr($_var_94, -1) != '/') {
		$_var_94 .= '/';
	}
	$_var_98 = $_var_94 . $_var_93 . $_var_95;
	if (!class_exists(basename($_var_93), false)) {
		return require_cache($_var_98);
	}
}
function load($_var_99, $_var_100 = '', $_var_101 = '.php')
{
	$_var_99 = str_replace(array('.', '#'), array('/', '.'), $_var_99);
	if (empty($_var_100)) {
		if (0 === strpos($_var_99, '@/')) {
			$_var_100 = MODULE_PATH . 'common/';
			$_var_99 = substr($_var_99, 2);
		} else {
			$_var_102 = explode('/', $_var_99);
			$_var_100 = APP_PATH . array_shift($_var_102) . '/common/';
			$_var_99 = implode('/', $_var_102);
		}
	}
	if (substr($_var_100, -1) != '/') {
		$_var_100 .= '/';
	}
	require_cache($_var_100 . $_var_99 . $_var_101);
}
function vendor($_var_103, $_var_104 = '', $_var_105 = '.php')
{
	if (empty($_var_104)) {
		$_var_104 = VENDOR_PATH;
	}
	return import($_var_103, $_var_104, $_var_105);
}
function D($_var_106 = '', $_var_107 = '')
{
	if (empty($_var_106)) {
		return new Think\Model();
	}
	static $_var_108 = array();
	$_var_107 = $_var_107 ?: C('DEFAULT_M_LAYER');
	if (strpos($_var_106, '://')) {
		list($_var_109, $_var_106) = explode('://', $_var_106);
		$_var_106 = strtolower($_var_109) . '://' . $_var_106;
	} else {
		$_var_110 = explode('/', $_var_106);
		if (count($_var_110) > 1) {
			$_var_111 = $_var_110[0];
			$_var_112 = $_var_110[1];
			$_var_106 = strtolower($_var_111) . '/' . $_var_112;
		}
	}
	if (isset($_var_108[$_var_106 . $_var_107])) {
		return $_var_108[$_var_106 . $_var_107];
	}
	$_var_113 = parse_res_name($_var_106, $_var_107);
	$_var_114 = '';
	if (defined('_ADDONS')) {
		$_var_114 = parse_res_name('addons://' . _ADDONS . '/' . $_var_106, $_var_107);
		if (!class_exists($_var_114)) {
			$_var_114 = parse_res_name('plugins://' . _ADDONS . '/' . $_var_106, $_var_107);
		}
	}
	if ($_var_114 && class_exists($_var_114)) {
		$_var_115 = new $_var_114(basename($_var_106));
	} elseif (class_exists($_var_113)) {
		$_var_115 = new $_var_113(basename($_var_106));
	} elseif (false === strpos($_var_106, '/')) {
		if (!C('APP_USE_NAMESPACE')) {
			import('common/' . $_var_107 . '/' . $_var_113);
		} else {
			$_var_113 = '\\common\\' . $_var_107 . '\\' . $_var_106 . $_var_107;
		}
		$_var_115 = class_exists($_var_113) ? new $_var_113($_var_106) : new think\Model($_var_106);
	} else {
		think\Log::record('D方法实例化没找到模型类' . $_var_113, think\Log::NOTICE);
		$_var_115 = new think\Model(basename($_var_106));
	}
	$_var_108[$_var_106 . $_var_107] = $_var_115;
	return $_var_115;
}
function M($_var_116 = '', $_var_117 = '', $_var_118 = '')
{
	static $_var_119 = array();
	if (strpos($_var_116, ':')) {
		list($_var_120, $_var_116) = explode(':', $_var_116);
	} else {
		$_var_120 = 'think\\Model';
	}
	$_var_121 = (is_array($_var_118) ? implode('', $_var_118) : $_var_118) . $_var_117 . $_var_116 . '_' . $_var_120;
	if (!isset($_var_119[$_var_121])) {
		$_var_119[$_var_121] = new $_var_120($_var_116, $_var_117, $_var_118);
	}
	return $_var_119[$_var_121];
}
function parse_res_name($_var_122, $_var_123, $_var_124 = 1)
{
	if (strpos($_var_122, '://')) {
		list($_var_125, $_var_122) = explode('://', $_var_122);
	} else {
		$_var_125 = '';
	}
	if (strpos($_var_122, '/') && substr_count($_var_122, '/') >= $_var_124) {
		list($_var_126, $_var_122) = explode('/', $_var_122, 2);
		$_var_126 = strtolower($_var_126);
	} else {
		$_var_126 = defined('MODULE_NAME') ? MODULE_NAME : '';
	}
	$_var_127 = explode('/', $_var_122);
	if (!C('APP_USE_NAMESPACE')) {
		$_var_128 = parse_name($_var_122, 1);
		import($_var_126 . '/' . strtolower($_var_123) . '/' . $_var_128 . $_var_123);
	} else {
		$_var_128 = $_var_126 . '\\' . strtolower($_var_123);
		foreach ($_var_127 as $_var_122) {
			$_var_128 .= '\\' . parse_name($_var_122, 1);
		}
		if ($_var_125) {
			$_var_128 = $_var_125 . '\\' . $_var_128;
		}
	}
	return $_var_128 . ucfirst($_var_123);
}
function controller($_var_129, $_var_130 = '')
{
	$_var_131 = C('DEFAULT_C_LAYER');
	if (!C('APP_USE_NAMESPACE')) {
		$_var_132 = parse_name($_var_129, 1) . $_var_131;
		import(MODULE_NAME . '/' . $_var_131 . '/' . $_var_132);
	} else {
		$_var_132 = ($_var_130 ? basename(ADDON_PATH) . '\\' . $_var_130 : MODULE_NAME) . '\\' . $_var_131;
		$_var_133 = explode('/', $_var_129);
		foreach ($_var_133 as $_var_129) {
			$_var_132 .= '\\' . parse_name($_var_129, 1);
		}
		$_var_132 .= $_var_131;
	}
	if (class_exists($_var_132)) {
		return new $_var_132();
	} else {
		return false;
	}
}
function A($_var_134, $_var_135 = '', $_var_136 = 0)
{
	static $_var_137 = array();
	$_var_135 = $_var_135 ? ucfirst(strtolower($_var_135)) : C('DEFAULT_C_LAYER');
	$_var_136 = $_var_136 ? $_var_136 : ($_var_135 == C('DEFAULT_C_LAYER') ? C('CONTROLLER_LEVEL') : 1);
	if (isset($_var_137[$_var_134 . $_var_135])) {
		return $_var_137[$_var_134 . $_var_135];
	}
	$_var_138 = parse_res_name($_var_134, $_var_135, $_var_136);
	if (class_exists($_var_138)) {
		$_var_139 = new $_var_138();
		$_var_137[$_var_134 . ucfirst($_var_135)] = $_var_139;
		return $_var_139;
	} else {
		return false;
	}
}
function R($_var_140, $_var_141 = array(), $_var_142 = '')
{
	$_var_143 = pathinfo($_var_140);
	$_var_144 = $_var_143['basename'];
	$_var_145 = $_var_143['dirname'];
	$_var_146 = A($_var_145, $_var_142);
	if ($_var_146) {
		if (is_string($_var_141)) {
			parse_str($_var_141, $_var_141);
		}
		return call_user_func_array(array(&$_var_146, $_var_144 . C('ACTION_SUFFIX')), $_var_141);
	} else {
		return false;
	}
}
function tag($_var_147, &$_var_148 = NULL)
{
	\think\Hook::listen($_var_147, $_var_148);
}
function B($_var_149, $_var_150 = '', &$_var_151 = NULL)
{
	if ('' == $_var_150) {
		$_var_149 .= 'Behavior';
	}
	\think\Hook::exec($_var_149, $_var_150, $_var_151);
}
function strip_whitespace($_var_152)
{
	$_var_153 = '';
	$_var_154 = token_get_all($_var_152);
	$_var_155 = false;
	for ($_var_156 = 0, $_var_157 = count($_var_154); $_var_156 < $_var_157; $_var_156++) {
		if (is_string($_var_154[$_var_156])) {
			$_var_155 = false;
			$_var_153 .= $_var_154[$_var_156];
		} else {
			switch ($_var_154[$_var_156][0]) {
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
				case T_WHITESPACE:
					if (!$_var_155) {
						$_var_153 .= ' ';
						$_var_155 = true;
					}
					break;
				case T_START_HEREDOC:
					$_var_153 .= '<<<THINK
';
					break;
				case T_END_HEREDOC:
					$_var_153 .= 'THINK;
';
					for ($_var_158 = $_var_156 + 1; $_var_158 < $_var_157; $_var_158++) {
						if (is_string($_var_154[$_var_158]) && $_var_154[$_var_158] == ';') {
							$_var_156 = $_var_158;
							break;
						} else {
							if ($_var_154[$_var_158][0] == T_CLOSE_TAG) {
								break;
							}
						}
					}
					break;
				default:
					$_var_155 = false;
					$_var_153 .= $_var_154[$_var_156][1];
			}
		}
	}
	return $_var_153;
}
function throw_exception($_var_159, $_var_160 = 'think\\Exception', $_var_161 = 0)
{
	\think\Log::record('建议使用E方法替代throw_exception', think\Log::NOTICE);
	if (class_exists($_var_160, false)) {
		throw new $_var_160($_var_159, $_var_161);
	} else {
		think\Think::halt($_var_159);
	}
}
function dump($_var_162, $_var_163 = true, $_var_164 = null, $_var_165 = true)
{
	$_var_164 = $_var_164 === null ? '' : rtrim($_var_164) . ' ';
	if (!$_var_165) {
		if (ini_get('html_errors')) {
			$_var_166 = print_r($_var_162, true);
			$_var_166 = '<pre>' . $_var_164 . htmlspecialchars($_var_166, ENT_QUOTES) . '</pre>';
		} else {
			$_var_166 = $_var_164 . print_r($_var_162, true);
		}
	} else {
		ob_start();
		var_dump($_var_162);
		$_var_166 = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$_var_166 = preg_replace('/\\]\\=\\>\\n(\\s+)/m', '] => ', $_var_166);
			$_var_166 = '<pre>' . $_var_164 . htmlspecialchars($_var_166, ENT_QUOTES) . '</pre>';
		}
	}
	if ($_var_163) {
		echo $_var_166;
		return null;
	} else {
		return $_var_166;
	}
}
function layout($_var_167)
{
	if (false !== $_var_167) {
		C('LAYOUT_ON', true);
		if (is_string($_var_167)) {
			C('LAYOUT_NAME', $_var_167);
		}
	} else {
		C('LAYOUT_ON', false);
	}
}
function U($_var_168 = '', $_var_169 = '', $_var_170 = true, $_var_171 = false)
{
	$_var_172 = parse_url($_var_168);
	$_var_168 = !empty($_var_172['path']) ? $_var_172['path'] : ACTION_NAME;
	if (isset($_var_172['fragment'])) {
		$_var_173 = $_var_172['fragment'];
		if (false !== strpos($_var_173, '?')) {
			list($_var_173, $_var_172['query']) = explode('?', $_var_173, 2);
		}
		if (false !== strpos($_var_173, '@')) {
			list($_var_173, $_var_174) = explode('@', $_var_173, 2);
		}
	} elseif (false !== strpos($_var_168, '@')) {
		list($_var_168, $_var_174) = explode('@', $_var_172['path'], 2);
	}
	if (isset($_var_174)) {
		$_var_171 = $_var_174 . (strpos($_var_174, '.') ? '' : strstr($_SERVER['HTTP_HOST'], '.'));
	} elseif ($_var_171 === true) {
		$_var_171 = $_SERVER['HTTP_HOST'];
		if (C('APP_SUB_DOMAIN_DEPLOY')) {
			$_var_171 = $_var_171 == 'localhost' ? 'localhost' : 'www' . strstr($_SERVER['HTTP_HOST'], '.');
			foreach (C('APP_SUB_DOMAIN_RULES') as $_var_175 => $_var_176) {
				$_var_176 = is_array($_var_176) ? $_var_176[0] : $_var_176;
				if (false === strpos($_var_175, '*') && 0 === strpos($_var_168, $_var_176)) {
					$_var_171 = $_var_175 . strstr($_var_171, '.');
					$_var_168 = substr_replace($_var_168, '', 0, strlen($_var_176));
					break;
				}
			}
		}
	}
	if (is_string($_var_169)) {
		parse_str($_var_169, $_var_169);
	} elseif (!is_array($_var_169)) {
		$_var_169 = array();
	}
	if (isset($_var_172['query'])) {
		parse_str($_var_172['query'], $_var_177);
		$_var_169 = array_merge($_var_177, $_var_169);
	}
	$_var_178 = C('URL_PATHINFO_DEPR');
	$_var_179 = C('URL_CASE_INSENSITIVE');
	if ($_var_168) {
		if (0 === strpos($_var_168, '/')) {
			$_var_180 = true;
			$_var_168 = substr($_var_168, 1);
			if ('/' != $_var_178) {
				$_var_168 = str_replace('/', $_var_178, $_var_168);
			}
		} else {
			if ('/' != $_var_178) {
				$_var_168 = str_replace('/', $_var_178, $_var_168);
			}
			$_var_168 = trim($_var_168, $_var_178);
			$_var_181 = explode($_var_178, $_var_168);
			$_var_182 = array();
			$_var_183 = C('VAR_MODULE');
			$_var_184 = C('VAR_CONTROLLER');
			$_var_185 = C('VAR_ACTION');
			$_var_182[$_var_185] = !empty($_var_181) ? array_pop($_var_181) : ACTION_NAME;
			$_var_182[$_var_184] = !empty($_var_181) ? array_pop($_var_181) : CONTROLLER_NAME;
			if ($_var_186 = C('URL_ACTION_MAP')) {
				if (isset($_var_186[strtolower($_var_182[$_var_184])])) {
					$_var_186 = $_var_186[strtolower($_var_182[$_var_184])];
					if ($_var_187 = array_search(strtolower($_var_182[$_var_185]), $_var_186)) {
						$_var_182[$_var_185] = $_var_187;
					}
				}
			}
			if ($_var_186 = C('URL_CONTROLLER_MAP')) {
				if ($_var_188 = array_search(strtolower($_var_182[$_var_184]), $_var_186)) {
					$_var_182[$_var_184] = $_var_188;
				}
			}
			if ($_var_179) {
				$_var_182[$_var_184] = parse_name($_var_182[$_var_184]);
			}
			$_var_189 = '';
			if (!empty($_var_181)) {
				$_var_182[$_var_183] = implode($_var_178, $_var_181);
			} else {
				if (C('MULTI_MODULE')) {
					if (MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')) {
						$_var_182[$_var_183] = MODULE_NAME;
					}
				}
			}
			if ($_var_186 = C('URL_MODULE_MAP')) {
				if ($_var_190 = array_search(strtolower($_var_182[$_var_183]), $_var_186)) {
					$_var_182[$_var_183] = $_var_190;
				}
			}
			if (isset($_var_182[$_var_183])) {
				$_var_189 = $_var_182[$_var_183];
				unset($_var_182[$_var_183]);
			}
		}
	}
	$_var_191 = MODULE_NAME == 'admin' ? 3 : C('URL_MODEL');
	if ($_var_191 == 0) {
		$_var_168 = __APP__ . '?' . C('VAR_MODULE') . "={$_var_189}&" . http_build_query(array_reverse($_var_182));
		if ($_var_179) {
			$_var_168 = strtolower($_var_168);
		}
		if (!empty($_var_169)) {
			$_var_169 = http_build_query($_var_169);
			$_var_168 .= '&' . $_var_169;
		}
	} elseif ($_var_191 == 2) {
		$_var_192 = C('router');
		$_var_193 = $_var_189 . '/' . $_var_182[$_var_184] . '/' . $_var_182[$_var_185];
		$_var_193 = strtolower($_var_193);
		if (isset($_var_192[$_var_193])) {
			if (false == strpos($_var_192[$_var_193], '://')) {
				$_var_168 = __APP__ . '/' . $_var_192[$_var_193];
			} else {
				$_var_168 = $_var_192[$_var_193];
			}
			if ($_var_179) {
				$_var_168 = strtolower($_var_168);
			}
			if (!empty($_var_169)) {
				$_var_194 = $_var_168;
				$_var_195 = '';
				foreach ($_var_169 as $_var_182 => $_var_196) {
					if (strpos($_var_168, '[' . $_var_182 . ']')) {
						if ($_var_196 !== '' && $_var_196 !== null) {
							$_var_168 = str_replace('[' . $_var_182 . ']', $_var_196, $_var_168);
						}
					} else {
						if ('' !== trim($_var_196)) {
							$_var_195 .= $_var_178 . $_var_182 . $_var_178 . urlencode($_var_196);
						}
					}
				}
				$_var_168 = preg_replace('/(.*)?\\_$/i', '$1', $_var_168);
				if ($_var_195 != '') {
					if ($_var_194 == $_var_168) {
						$_var_168 .= '_' . $_var_195;
					} else {
						$_var_168 .= $_var_195;
					}
				}
				if ($_var_170) {
					$_var_170 = $_var_170 === true ? C('URL_HTML_SUFFIX') : $_var_170;
					if ($_var_197 = strpos($_var_170, '|')) {
						$_var_170 = substr($_var_170, 0, $_var_197);
					}
					if ($_var_170 && '/' != substr($_var_168, -1)) {
						$_var_168 .= '.' . ltrim($_var_170, '.');
					}
				}
			} else {
				$_var_168 = preg_replace('/(.*)?\\_$/i', '$1', $_var_168);
			}
			$_var_168 = preg_replace('/\\[PAGE\\]/', '{[PAGE]}', $_var_168);
			$_var_168 = preg_replace('/\\/[a-z]\\_\\[.*?\\]/i', '', $_var_168);
			$_var_168 = preg_replace('/\\_[a-z]\\_\\[.*?\\](\\/)?/i', '', $_var_168);
			$_var_168 = preg_replace('/\\_\\[.*?\\](\\/)?/i', '', $_var_168);
			$_var_168 = preg_replace('/\\/\\[.*?\\]/i', '', $_var_168);
			$_var_168 = preg_replace('/\\{\\[PAGE\\]\\}/', urlencode('[PAGE]'), $_var_168);
		} else {
			if (isset($_var_180)) {
				$_var_168 = __APP__ . '/' . rtrim($_var_168, $_var_178);
			} else {
				$_var_189 = defined('BIND_MODULE') ? '' : $_var_189;
				$_var_168 = __APP__ . '/' . ($_var_189 ? $_var_189 . MODULE_PATHINFO_DEPR : '') . implode($_var_178, array_reverse($_var_182));
			}
			if ($_var_179) {
				$_var_168 = strtolower($_var_168);
			}
			if (!empty($_var_169)) {
				foreach ($_var_169 as $_var_182 => $_var_196) {
					if ('' !== trim($_var_196)) {
						$_var_168 .= $_var_178 . $_var_182 . $_var_178 . urlencode($_var_196);
					}
				}
			}
			if ($_var_170) {
				$_var_170 = $_var_170 === true ? C('URL_HTML_SUFFIX') : $_var_170;
				if ($_var_197 = strpos($_var_170, '|')) {
					$_var_170 = substr($_var_170, 0, $_var_197);
				}
				if ($_var_170 && '/' != substr($_var_168, -1)) {
					$_var_168 .= '.' . ltrim($_var_170, '.');
				}
			}
		}
	} else {
		if (isset($_var_180)) {
			$_var_168 = __APP__ . '/' . rtrim($_var_168, $_var_178);
		} else {
			$_var_189 = defined('BIND_MODULE') && BIND_MODULE == $_var_189 ? '' : $_var_189;
			$_var_168 = __APP__ . '/' . ($_var_189 ? $_var_189 . MODULE_PATHINFO_DEPR : '') . implode($_var_178, array_reverse($_var_182));
		}
		if ($_var_179) {
			$_var_168 = strtolower($_var_168);
		}
		if (!empty($_var_169)) {
			foreach ($_var_169 as $_var_182 => $_var_196) {
				if ('' !== trim($_var_196)) {
					$_var_168 .= $_var_178 . $_var_182 . $_var_178 . urlencode($_var_196);
				}
			}
		}
		if ($_var_170) {
			$_var_170 = $_var_170 === true ? C('URL_HTML_SUFFIX') : $_var_170;
			if ($_var_197 = strpos($_var_170, '|')) {
				$_var_170 = substr($_var_170, 0, $_var_197);
			}
			if ($_var_170 && '/' != substr($_var_168, -1)) {
				$_var_168 .= '.' . ltrim($_var_170, '.');
			}
		}
	}
	if (isset($_var_173)) {
		$_var_168 .= '#' . $_var_173;
	}
	if ($_var_171) {
		$_var_168 = (is_ssl() ? 'https://' : 'http://') . $_var_171 . $_var_168;
	}
	$_var_198 = C('URL_ROUTE_RULES');
	if (isset($_var_198['addons/execute/:_addons/:_controller/:_action'])) {
		$_var_168 = str_ireplace(array('home/addons/execute', '_addons/', '_controller/', '_action/'), array('addon', ''), $_var_168);
	}
	if (isset($_var_198['addons/plugin/:_addons/:_controller/:_action'])) {
		$_var_168 = str_ireplace(array('home/addons/plugin', '_addons/', '_controller/', '_action/'), array('plugin', ''), $_var_168);
	}
	return $_var_168;
}
function W($_var_199, $_var_200 = array())
{
	R($_var_199, $_var_200, 'widget');
}
function is_ssl()
{
	if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
		return true;
	} elseif (isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT']) {
		return true;
	}
	return false;
}
function redirect($_var_201, $_var_202 = 0, $_var_203 = '')
{
	$_var_201 = str_replace(array('
', ''), '', $_var_201);
	if (empty($_var_203)) {
		$_var_203 = "系统将在{$_var_202}秒之后自动跳转到{$_var_201}！";
	}
	if (!headers_sent()) {
		if (0 === $_var_202) {
			header('Location: ' . $_var_201);
		} else {
			header("refresh:{$_var_202};url={$_var_201}");
			echo $_var_203;
		}
		exit;
	} else {
		$_var_204 = "<meta http-equiv='Refresh' content='{$_var_202};URL={$_var_201}'>";
		if ($_var_202 != 0) {
			$_var_204 .= $_var_203;
		}
		exit($_var_204);
	}
}
function S($_var_205, $_var_206 = '', $_var_207 = null)
{
	static $_var_208 = '';
	if (is_array($_var_207) && empty($_var_208)) {
		$_var_209 = isset($_var_207['type']) ? $_var_207['type'] : '';
		$_var_208 = think\Cache::getInstance($_var_209, $_var_207);
	} elseif (is_array($_var_205)) {
		$_var_209 = isset($_var_205['type']) ? $_var_205['type'] : '';
		$_var_208 = think\Cache::getInstance($_var_209, $_var_205);
		return $_var_208;
	} elseif (empty($_var_208)) {
		$_var_208 = think\Cache::getInstance();
	}
	if ('' === $_var_206) {
		return $_var_208->get($_var_205);
	} elseif (is_null($_var_206)) {
		return $_var_208->rm($_var_205);
	} else {
		if (is_array($_var_207)) {
			$_var_210 = isset($_var_207['expire']) ? $_var_207['expire'] : NULL;
		} else {
			$_var_210 = is_numeric($_var_207) ? $_var_207 : NULL;
		}
		return $_var_208->set($_var_205, $_var_206, $_var_210);
	}
}
function F($_var_211, $_var_212 = '', $_var_213 = DATA_PATH)
{
	static $_var_214 = array();
	$_var_215 = $_var_213 . $_var_211 . '.php';
	if ('' !== $_var_212) {
		if (is_null($_var_212)) {
			if (false !== strpos($_var_211, '*')) {
				return false;
			} else {
				unset($_var_214[$_var_211]);
				return think\Storage::unlink($_var_215, 'F');
			}
		} else {
			think\Storage::put($_var_215, serialize($_var_212), 'F');
			$_var_214[$_var_211] = $_var_212;
			return;
		}
	}
	if (isset($_var_214[$_var_211])) {
		return $_var_214[$_var_211];
	}
	if (think\Storage::has($_var_215, 'F')) {
		$_var_212 = unserialize(think\Storage::read($_var_215, 'F'));
		$_var_214[$_var_211] = $_var_212;
	} else {
		$_var_212 = false;
	}
	return $_var_212;
}
function to_guid_string($_var_216)
{
	if (is_object($_var_216)) {
		return spl_object_hash($_var_216);
	} elseif (is_resource($_var_216)) {
		$_var_216 = get_resource_type($_var_216) . strval($_var_216);
	} else {
		$_var_216 = serialize($_var_216);
	}
	return md5($_var_216);
}
function xml_encode($_var_217, $_var_218 = 'think', $_var_219 = 'item', $_var_220 = '', $_var_221 = 'id', $_var_222 = 'utf-8')
{
	if (is_array($_var_220)) {
		$_var_223 = array();
		foreach ($_var_220 as $_var_224 => $_var_225) {
			$_var_223[] = "{$_var_224}=\"{$_var_225}\"";
		}
		$_var_220 = implode(' ', $_var_223);
	}
	$_var_220 = trim($_var_220);
	$_var_220 = empty($_var_220) ? '' : " {$_var_220}";
	$_var_226 = "<?xml version=\"1.0\" encoding=\"{$_var_222}\"?>";
	$_var_226 .= "<{$_var_218}{$_var_220}>";
	$_var_226 .= data_to_xml($_var_217, $_var_219, $_var_221);
	$_var_226 .= "</{$_var_218}>";
	return $_var_226;
}
function data_to_xml($_var_227, $_var_228 = 'item', $_var_229 = 'id')
{
	$_var_230 = $_var_231 = '';
	foreach ($_var_227 as $_var_232 => $_var_233) {
		if (is_numeric($_var_232)) {
			$_var_229 && ($_var_231 = " {$_var_229}=\"{$_var_232}\"");
			$_var_232 = $_var_228;
		}
		$_var_230 .= "<{$_var_232}{$_var_231}>";
		$_var_230 .= is_array($_var_233) || is_object($_var_233) ? data_to_xml($_var_233, $_var_228, $_var_229) : $_var_233;
		$_var_230 .= "</{$_var_232}>";
	}
	return $_var_230;
}
function session($_var_234 = '', $_var_235 = '')
{
	$_var_236 = C('SESSION_PREFIX');
	if (is_array($_var_234)) {
		if (isset($_var_234['prefix'])) {
			C('SESSION_PREFIX', $_var_234['prefix']);
		}
		if (C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])) {
			session_id($_REQUEST[C('VAR_SESSION_ID')]);
		} elseif (isset($_var_234['id'])) {
			session_id($_var_234['id']);
		}
		if ('common' != APP_MODE) {
			ini_set('session.auto_start', 0);
		}
		if (isset($_var_234['name'])) {
			session_name($_var_234['name']);
		}
		if (isset($_var_234['path'])) {
			session_save_path($_var_234['path']);
		}
		if (isset($_var_234['domain'])) {
			ini_set('session.cookie_domain', $_var_234['domain']);
		}
		if (isset($_var_234['expire'])) {
			ini_set('session.gc_maxlifetime', $_var_234['expire']);
		}
		if (isset($_var_234['use_trans_sid'])) {
			ini_set('session.use_trans_sid', $_var_234['use_trans_sid'] ? 1 : 0);
		}
		if (isset($_var_234['use_cookies'])) {
			ini_set('session.use_cookies', $_var_234['use_cookies'] ? 1 : 0);
		}
		if (isset($_var_234['cache_limiter'])) {
			session_cache_limiter($_var_234['cache_limiter']);
		}
		if (isset($_var_234['cache_expire'])) {
			session_cache_expire($_var_234['cache_expire']);
		}
		if (isset($_var_234['type'])) {
			C('SESSION_TYPE', $_var_234['type']);
		}
		if (C('SESSION_TYPE')) {
			$_var_237 = C('SESSION_TYPE');
			$_var_238 = strpos($_var_237, '\\') ? $_var_237 : 'think\\session\\driver\\' . ucwords(strtolower($_var_237));
			$_var_239 = new $_var_238();
			session_set_save_handler(array(&$_var_239, 'open'), array(&$_var_239, 'close'), array(&$_var_239, 'read'), array(&$_var_239, 'write'), array(&$_var_239, 'destroy'), array(&$_var_239, 'gc'));
		}
		if (C('SESSION_AUTO_START')) {
			session_start();
		}
	} elseif ('' === $_var_235) {
		if ('' === $_var_234) {
			return $_var_236 ? $_SESSION[$_var_236] : $_SESSION;
		} elseif (0 === strpos($_var_234, '[')) {
			if ('[pause]' == $_var_234) {
				session_write_close();
			} elseif ('[start]' == $_var_234) {
				session_start();
			} elseif ('[destroy]' == $_var_234) {
				$_SESSION = array();
				session_unset();
				session_destroy();
			} elseif ('[regenerate]' == $_var_234) {
				session_regenerate_id();
			}
		} elseif (0 === strpos($_var_234, '?')) {
			$_var_234 = substr($_var_234, 1);
			if (strpos($_var_234, '.')) {
				list($_var_240, $_var_241) = explode('.', $_var_234);
				return $_var_236 ? isset($_SESSION[$_var_236][$_var_240][$_var_241]) : isset($_SESSION[$_var_240][$_var_241]);
			} else {
				return $_var_236 ? isset($_SESSION[$_var_236][$_var_234]) : isset($_SESSION[$_var_234]);
			}
		} elseif (is_null($_var_234)) {
			if ($_var_236) {
				unset($_SESSION[$_var_236]);
			} else {
				$_SESSION = array();
			}
		} elseif ($_var_236) {
			if (strpos($_var_234, '.')) {
				list($_var_240, $_var_241) = explode('.', $_var_234);
				return isset($_SESSION[$_var_236][$_var_240][$_var_241]) ? $_SESSION[$_var_236][$_var_240][$_var_241] : null;
			} else {
				return isset($_SESSION[$_var_236][$_var_234]) ? $_SESSION[$_var_236][$_var_234] : null;
			}
		} else {
			if (strpos($_var_234, '.')) {
				list($_var_240, $_var_241) = explode('.', $_var_234);
				return isset($_SESSION[$_var_240][$_var_241]) ? $_SESSION[$_var_240][$_var_241] : null;
			} else {
				return isset($_SESSION[$_var_234]) ? $_SESSION[$_var_234] : null;
			}
		}
	} elseif (is_null($_var_235)) {
		if ($_var_236) {
			unset($_SESSION[$_var_236][$_var_234]);
		} else {
			unset($_SESSION[$_var_234]);
		}
	} else {
		if ($_var_236) {
			if (!isset($_SESSION[$_var_236])) {
				$_SESSION[$_var_236] = array();
			}
			$_SESSION[$_var_236][$_var_234] = $_var_235;
		} else {
			$_SESSION[$_var_234] = $_var_235;
		}
	}
	return null;
}
function cookie($_var_242 = '', $_var_243 = '', $_var_244 = null)
{
	$_var_245 = array('prefix' => C('COOKIE_PREFIX'), 'expire' => C('COOKIE_EXPIRE'), 'path' => C('COOKIE_PATH'), 'domain' => C('COOKIE_DOMAIN'), 'httponly' => C('COOKIE_HTTPONLY'));
	if (!is_null($_var_244)) {
		if (is_numeric($_var_244)) {
			$_var_244 = array('expire' => $_var_244);
		} elseif (is_string($_var_244)) {
			parse_str($_var_244, $_var_244);
		}
		$_var_245 = array_merge($_var_245, array_change_key_case($_var_244));
	}
	if (!empty($_var_245['httponly'])) {
		ini_set('session.cookie_httponly', 1);
	}
	if (is_null($_var_242)) {
		if (empty($_COOKIE)) {
			return null;
		}
		$_var_246 = empty($_var_243) ? $_var_245['prefix'] : $_var_243;
		if (!empty($_var_246)) {
			foreach ($_COOKIE as $_var_247 => $_var_248) {
				if (0 === stripos($_var_247, $_var_246)) {
					setcookie($_var_247, '', time() - 3600, $_var_245['path'], $_var_245['domain']);
					unset($_COOKIE[$_var_247]);
				}
			}
		}
		return null;
	} elseif ('' === $_var_242) {
		return $_COOKIE;
	}
	$_var_242 = $_var_245['prefix'] . str_replace('.', '_', $_var_242);
	if ('' === $_var_243) {
		if (isset($_COOKIE[$_var_242])) {
			$_var_243 = $_COOKIE[$_var_242];
			if (0 === strpos($_var_243, 'think:')) {
				$_var_243 = substr($_var_243, 6);
				return array_map('urldecode', json_decode(MAGIC_QUOTES_GPC ? stripslashes($_var_243) : $_var_243, true));
			} else {
				return $_var_243;
			}
		} else {
			return null;
		}
	} else {
		if (is_null($_var_243)) {
			setcookie($_var_242, '', time() - 3600, $_var_245['path'], $_var_245['domain']);
			unset($_COOKIE[$_var_242]);
		} else {
			if (is_array($_var_243)) {
				$_var_243 = 'think:' . json_encode(array_map('urlencode', $_var_243));
			}
			$_var_249 = !empty($_var_245['expire']) ? time() + intval($_var_245['expire']) : 0;
			setcookie($_var_242, $_var_243, $_var_249, $_var_245['path'], $_var_245['domain']);
			$_COOKIE[$_var_242] = $_var_243;
		}
	}
	return null;
}
function load_ext_file($_var_250)
{
	if ($_var_251 = C('LOAD_EXT_FILE')) {
		$_var_251 = explode(',', $_var_251);
		foreach ($_var_251 as $_var_252) {
			$_var_252 = $_var_250 . 'common/' . $_var_252 . '.php';
			if (is_file($_var_252)) {
				include $_var_252;
			}
		}
	}
	if ($_var_253 = C('LOAD_EXT_CONFIG')) {
		if (is_string($_var_253)) {
			$_var_253 = explode(',', $_var_253);
		}
		foreach ($_var_253 as $_var_254 => $_var_255) {
			$_var_252 = $_var_250 . 'conf/' . $_var_255 . CONF_EXT;
			if (is_file($_var_252)) {
				is_numeric($_var_254) ? C(load_config($_var_252)) : C($_var_254, load_config($_var_252));
			}
		}
	}
}
function get_client_ip($_var_256 = 0, $_var_257 = false)
{
	$_var_256 = $_var_256 ? 1 : 0;
	if ($_var_257) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$_var_258 = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$_var_259 = array_search('unknown', $_var_258);
			if (false !== $_var_259) {
				unset($_var_258[$_var_259]);
			}
			$_var_260 = trim($_var_258[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$_var_260 = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$_var_260 = $_SERVER['REMOTE_ADDR'];
		}
	} elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$_var_260 = $_SERVER['REMOTE_ADDR'];
		if ($_var_260 == '127.0.0.1') {
			$_var_260 = get_client_ip(0, true);
		}
	}
	$_var_261 = sprintf('%u', ip2long($_var_260));
	$_var_260 = $_var_261 ? array($_var_260, $_var_261) : array('0.0.0.0', 0);
	return $_var_260[$_var_256];
}
function send_http_status($_var_262)
{
	static $_var_263 = array(100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Moved Temporarily ', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 509 => 'Bandwidth Limit Exceeded');
	if (isset($_var_263[$_var_262])) {
		header('HTTP/1.1 ' . $_var_262 . ' ' . $_var_263[$_var_262]);
		header('Status:' . $_var_262 . ' ' . $_var_263[$_var_262]);
	}
}
function filter_exp(&$_var_264)
{
	if (in_array(strtolower($_var_264), array('exp', 'or'))) {
		$_var_264 .= ' ';
	}
}
function in_array_case($_var_265, $_var_266)
{
	return in_array(strtolower($_var_265), array_map('strtolower', $_var_266));
}