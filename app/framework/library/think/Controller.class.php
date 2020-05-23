<?php

//decode by http://www.yunlu99.com/
namespace think;

error_reporting(E_ALL ^ E_NOTICE);
abstract class Controller
{
	protected $view = null;
	protected $config = array();
	public $_seo = array();
	public function setTitle($_var_0)
	{
		$this->_seo['title'] = $_var_0;
		$this->assign('seo', $this->_seo);
	}
	public function setKeywords($_var_1)
	{
		$this->_seo['keywords'] = $_var_1;
		$this->assign('seo', $this->_seo);
	}
	public function setDescription($_var_2)
	{
		$this->_seo['description'] = $_var_2;
		$this->assign('seo', $this->_seo);
	}
	public function qn_curl($_var_3, $_var_4 = false, $_var_5 = 0)
	{
		$_var_6 = array();
		$_var_7 = curl_init();
		curl_setopt($_var_7, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($_var_7, CURLOPT_USERAGENT, 'JuheData');
		curl_setopt($_var_7, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($_var_7, CURLOPT_TIMEOUT, 60);
		curl_setopt($_var_7, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($_var_7, CURLOPT_FOLLOWLOCATION, true);
		if ($_var_5) {
			curl_setopt($_var_7, CURLOPT_POST, true);
			curl_setopt($_var_7, CURLOPT_POSTFIELDS, $_var_4);
			curl_setopt($_var_7, CURLOPT_URL, $_var_3);
		} else {
			if ($_var_4) {
				curl_setopt($_var_7, CURLOPT_URL, $_var_3 . '?' . $_var_4);
			} else {
				curl_setopt($_var_7, CURLOPT_URL, $_var_3);
			}
		}
		$_var_8 = curl_exec($_var_7);
		if ($_var_8 === false) {
			return false;
		}
		$_var_9 = curl_getinfo($_var_7, CURLINFO_HTTP_CODE);
		$_var_6 = array_merge($_var_6, curl_getinfo($_var_7));
		curl_close($_var_7);
		return $_var_8;
	}
	public function i_dog($_var_10)
	{
//		header('Content-type:text/html;charset=utf8');
//		$_var_11 = $_SERVER['SERVER_NAME'];
//		$_var_12 = 'http://www.qkephp.com/auth/index/monitor';
//		$_var_13 = gethostbyname($_SERVER['SERVER_NAME']);
//		$_var_14 = $this->qn_curl(base64_decode($_var_12) . '?qa=' . base64_encode($_var_13) . '&qb=' . base64_encode($_var_11) . '&qc=' . base64_encode(QN_VERSION) . '&qd=' . base64_encode($_var_10) . '&qe=hm');
//		return true;
	}
	public function c_dog()
	{
//		$_var_15 = S('QN_HAS_CHECK');
//		if (empty($_var_15)) {
//			header('Content-type:text/html;charset=utf8');
//			$_var_16 = $_SERVER['SERVER_NAME'];
//			$_var_17 = C('QN_ACTIVATION_CODE');
//			$_var_18 = S('QN_DOMAIN_IP');
//			if (empty($_var_18)) {
//				$_var_18 = gethostbyname($_SERVER['SERVER_NAME']);
//				if (filter_var($_var_18, FILTER_VALIDATE_IP)) {
//					S('QN_DOMAIN_IP', $_var_18, 31536000);
//				} else {
//					$_var_18 = D('Admin/Curl')->curl('http://ident.me/');
//					if (filter_var($_var_18, FILTER_VALIDATE_IP)) {
//						S('QN_DOMAIN_IP', $_var_18, 31536000);
//					}
//				}
//			}
//			if (empty($_var_17)) {
//				exit('<script>
//                    var cc = \'<div style="-ms-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);min-width:80%;padding:30px;text-align:center;position:absolute;left:50%;top:50%;border-top:none;"><h1 style="letter-spacing: 0.05em;font-size: 24px;text-transform: uppercase;margin-bottom:20px;">\\u5f53\\u524d\\u7a0b\\u5e8f\\u672a\\u6fc0\\u6d3b\\uff0c\\u65e0\\u6cd5\\u4f7f\\u7528</h1><p>\\u8bf7\\u6e05\\u7a7a\\u7f51\\u7ad9\\u6839\\u76ee\\u5f55\\u0043\\u006f\\u006e\\u0066\\u6587\\u4ef6\\u5939\\u91cc\\u9762\\u7684\\u6587\\u4ef6\\u540e\\uff0c\\u91cd\\u65b0\\u5b89\\u88c5\\u672c\\u7a0b\\u5e8f\\u6fc0\\u6d3b</p></div>\';
//                    document.write(cc);
//                    </script>');
//			} else {
//				$_var_19 = strtoupper(md5($_var_18));
//				$_var_20 = qnhm_encrypt($_var_19, '123');
//				$_var_21 = strtoupper(md5($_var_20));
//				$_var_22 = explode('-', $_var_17);
//				$_var_23 = $_var_22[0] . $_var_22[1] . $_var_22[2] . $_var_22[3] . $_var_22[4];
//				$_var_24 = substr($_var_21, 0, 30);
//				if ($_var_24 != $_var_23) {
//					exit('<script>
//                    var cc = \'<div style="-ms-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);min-width:80%;padding:30px;text-align:center;position:absolute;left:50%;top:50%;border-top:none;"><h1 style="letter-spacing: 0.05em;font-size: 24px;text-transform: uppercase;margin-bottom:20px;">\\u5f53\\u524d\\u7a0b\\u5e8f\\u672a\\u6fc0\\u6d3b\\uff0c\\u65e0\\u6cd5\\u4f7f\\u7528</h1><p>\\u8bf7\\u6e05\\u7a7a\\u7f51\\u7ad9\\u6839\\u76ee\\u5f55\\u0043\\u006f\\u006e\\u0066\\u6587\\u4ef6\\u5939\\u91cc\\u9762\\u7684\\u6587\\u4ef6\\u540e\\uff0c\\u91cd\\u65b0\\u5b89\\u88c5\\u672c\\u7a0b\\u5e8f\\u6fc0\\u6d3b</p></div>\';
//                    document.write(cc);
//                    </script>');
//				} else {
//					S('QN_HAS_CHECK', 1, 3600);
//				}
//			}
//		}
	}
	public function d_dog($_var_25)
	{
		if (empty($_var_25)) {
			$this->error('参数错误');
		}
//		$_var_26 = gethostbyname($_SERVER['SERVER_NAME']);
//		if (filter_var($_var_26, FILTER_VALIDATE_IP)) {
//			S('QN_DOMAIN_IP', $_var_26, 31536000);
//		} else {
//			$_var_26 = $this->qn_curl('http://ident.me/');
//			if (filter_var($_var_26, FILTER_VALIDATE_IP)) {
//				S('QN_DOMAIN_IP', $_var_26, 31536000);
//			}
//		}
		$_var_27 = strtoupper(md5($_var_26));
		$_var_28 = qnhm_encrypt($_var_27, '123');
		$_var_29 = strtoupper(md5($_var_28));
		$_var_30 = explode('-', $_var_25);
		$_var_31 = $_var_30[0] . $_var_30[1] . $_var_30[2] . $_var_30[3] . $_var_30[4];
		$_var_32 = substr($_var_29, 0, 30);
//		if ($_var_32 != $_var_31) {
//			S('qn_activation_code', NULL);
//			$this->error('阿联酋，请重试');
//		} else {
			S('qn_activation_code', $_var_25, 7200);
			return true;
//		}
	}
	public function cc_dog()
	{
//		$_var_33 = S('QN_HAS_CHECK_CC');
//		if (empty($_var_33)) {
//			header('Content-type:text/html;charset=utf8');
//			$_var_34 = @file_get_contents('./data/compare_version.php');
//			if (!empty($_var_34)) {
//				$_var_35 = qnhm_decrypt($_var_34, '2018qnphpdesignbyzhaoxi');
//				$_var_36 = explode('||', $_var_35);
//				if (count($_var_36) != 5) {
//					exit('<script>var cc = \'<div style="-ms-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);min-width:80%;padding:30px;text-align:center;position:absolute;left:50%;top:50%;border-top:none;"><h1 style="letter-spacing: 0.05em;font-size: 24px;text-transform: uppercase;margin-bottom:20px;">\\u6838\\u5fc3\\u6587\\u4ef6\\u4e0d\\u5b8c\\u6574\\uff0c\\u7a0b\\u5e8f\\u8fd0\\u884c\\u5931\\u8d25</h1><p>\\u8bf7\\u68c0\\u67e5\\u7a0b\\u5e8f\\u6587\\u4ef6\\u5b8c\\u6574\\u6027</p></div>\';document.write(cc);</script>');
//				}
//				$_var_37 = dirname(__FILE__);
//				$_var_38 = dirname(dirname($_var_37)) . '/qiankuncore.php';
//				$_var_39 = md5_file($_var_38);
//				$_var_40 = dirname(dirname($_var_37)) . '/common/functions.php';
//				$_var_41 = md5_file($_var_40);
//				$_var_42 = $_var_37 . '/App.class.php';
//				$_var_43 = md5_file($_var_42);
//				$_var_44 = $_var_37 . '/Controller.class.php';
//				$_var_45 = md5_file($_var_44);
//				$_var_46 = $_var_37 . '/View.class.php';
//				$_var_47 = md5_file($_var_46);
//				$_var_48 = $_var_39 . '||' . $_var_41 . '||' . $_var_43 . '||' . $_var_45 . '||' . $_var_47;
//				if ($_var_48 != $_var_35) {
//					exit('<script>var cc = \'<div style="-ms-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);min-width:80%;padding:30px;text-align:center;position:absolute;left:50%;top:50%;border-top:none;"><h1 style="letter-spacing: 0.05em;font-size: 24px;text-transform: uppercase;margin-bottom:20px;">\\u6838\\u5fc3\\u6587\\u4ef6\\u4e0d\\u5b8c\\u6574\\uff0c\\u7a0b\\u5e8f\\u8fd0\\u884c\\u5931\\u8d25</h1><p>\\u8bf7\\u68c0\\u67e5\\u7a0b\\u5e8f\\u6587\\u4ef6\\u5b8c\\u6574\\u6027</p></div>\';document.write(cc);</script>');
//				} else {
//					S('QN_HAS_CHECK_CC', 1, 3500);
//				}
//			}
//		}
	}
	public function __construct()
	{
		$this->cc_dog();
		if (is_file('./conf/user.php')) {
			$_var_49 = D('Common/Module');
			$_var_50 = api('Config/lists');
			if (!$_var_50) {
				$_var_50 = api('Config/lists');
				S('DB_CONFIG_DATA', $_var_50);
			}
			C($_var_50);
			$_var_51 = $_var_49->getModule(MODULE_NAME);
			if (strtolower(MODULE_NAME) != 'install' && strtolower(MODULE_NAME) != 'admin') {
				if (!C('WEB_SITE_CLOSE')) {
					header('Content-Type: text/html; charset=utf-8');
					$_var_52 = '<div style="margin:0 auto; text-align:center; font-size:24px;"><div style="margin-top:20%"><p>' . C('WEB_SITE_CLOSE_INFO') . '</p></div></div>';
					exit($_var_52);
				}
				if (strtolower(MODULE_NAME) != 'install' && strtolower(MODULE_NAME) != 'admin') {
					$_var_49->checkCanVisit(strtolower(MODULE_NAME));
				}
			}
			if (strtolower(MODULE_NAME) != 'install') {
				$this->c_dog();
			}
		}
		Hook::listen('action_begin', $this->config);
		$this->view = Think::instance('think\\View');
		if (!empty($_var_51)) {
			$this->view->assign('MODULE_INFO', $_var_51);
			$this->view->assign('MODULE_ALIAS', $_var_51['alias']);
		}
		if (method_exists($this, '_initialize')) {
			$this->_initialize();
		}
	}
	protected function display($_var_53 = '', $_var_54 = '', $_var_55 = '', $_var_56 = '', $_var_57 = '')
	{
		$this->view->display($_var_53, $_var_54, $_var_55, $_var_56, $_var_57);
	}
	protected function show($_var_58, $_var_59 = '', $_var_60 = '', $_var_61 = '')
	{
		$this->view->display('', $_var_59, $_var_60, $_var_58, $_var_61);
	}
	protected function fetch($_var_62 = '', $_var_63 = '', $_var_64 = '')
	{
		return $this->view->fetch($_var_62, $_var_63, $_var_64);
	}
	protected function buildHtml($_var_65 = '', $_var_66 = '', $_var_67 = '')
	{
		$_var_68 = $this->fetch($_var_67);
		$_var_66 = !empty($_var_66) ? $_var_66 : HTML_PATH;
		$_var_65 = $_var_66 . $_var_65 . C('HTML_FILE_SUFFIX');
		Storage::put($_var_65, $_var_68, 'html');
		return $_var_68;
	}
	protected function theme($_var_69)
	{
		$this->view->theme($_var_69);
		return $this;
	}
	protected function assign($_var_70, $_var_71 = '')
	{
		$this->view->assign($_var_70, $_var_71);
		return $this;
	}
	public function __set($_var_72, $_var_73)
	{
		$this->assign($_var_72, $_var_73);
	}
	public function get($_var_74 = '')
	{
		return $this->view->get($_var_74);
	}
	public function __get($_var_75)
	{
		return $this->get($_var_75);
	}
	public function __isset($_var_76)
	{
		return $this->get($_var_76);
	}
	public function __call($_var_77, $_var_78)
	{
		if (0 === strcasecmp($_var_77, ACTION_NAME . C('ACTION_SUFFIX'))) {
			if (method_exists($this, '_empty')) {
				$this->_empty($_var_77, $_var_78);
			} elseif (file_exists_case($this->view->parseTemplate())) {
				$this->display();
			} else {
				E(L('_ERROR_ACTION_') . ':' . ACTION_NAME, 815);
			}
		} else {
			E(__CLASS__ . ':' . $_var_77 . L('_METHOD_NOT_EXIST_'));
			return;
		}
	}
	protected function error($_var_79 = '', $_var_80 = '', $_var_81 = false)
	{
		$this->dispatchJump($_var_79, 0, $_var_80, $_var_81);
	}
	protected function success($_var_82 = '', $_var_83 = '', $_var_84 = false)
	{
		$this->dispatchJump($_var_82, 1, $_var_83, $_var_84);
	}
	protected function ajaxReturn($_var_85, $_var_86 = '')
	{
		if (empty($_var_86)) {
			$_var_86 = C('DEFAULT_AJAX_RETURN');
		}
		switch (strtoupper($_var_86)) {
			case 'JSON':
				header('Content-Type:application/json; charset=utf-8');
				exit(json_encode($_var_85));
			case 'XML':
				header('Content-Type:text/xml; charset=utf-8');
				exit(xml_encode($_var_85));
			case 'JSONP':
				header('Content-Type:application/json; charset=utf-8');
				$_var_87 = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
				exit($_var_87 . '(' . json_encode($_var_85) . ');');
			case 'EVAL':
				header('Content-Type:text/html; charset=utf-8');
				exit($_var_85);
			default:
				Hook::listen('ajax_return', $_var_85);
		}
	}
	protected function redirect($_var_88, $_var_89 = array(), $_var_90 = 0, $_var_91 = '')
	{
		$_var_88 = U($_var_88, $_var_89);
		redirect($_var_88, $_var_90, $_var_91);
	}
	private function dispatchJump($_var_92, $_var_93 = 1, $_var_94 = '', $_var_95 = false)
	{
		if (true === $_var_95 || IS_AJAX) {
			$_var_96 = is_array($_var_95) ? $_var_95 : array();
			$_var_96['info'] = $_var_92;
			$_var_96['status'] = $_var_93;
			$_var_96['url'] = $_var_94;
			$this->ajaxReturn($_var_96);
		}
		if (is_int($_var_95)) {
			$this->assign('waitSecond', $_var_95);
		}
		if (!empty($_var_94)) {
			$this->assign('jumpUrl', $_var_94);
		}
		$this->assign('msgTitle', $_var_93 ? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
		if ($this->get('closeWin')) {
			$this->assign('jumpUrl', 'javascript:window.close();');
		}
		$this->assign('status', $_var_93);
		C('HTML_CACHE_ON', false);
		if ($_var_93) {
			$this->assign('success_message', $_var_92);
			if (!isset($this->waitSecond)) {
				$this->assign('waitSecond', modC('SUCCESS_WAIT_TIME', '2', 'config'));
			}
			if (!isset($this->jumpUrl)) {
				$this->assign('jumpUrl', $_SERVER['HTTP_REFERER']);
			}
			$this->display(C('TMPL_ACTION_SUCCESS'));
			exit;
		} else {
			$this->assign('error_message', $_var_92);
			if (!isset($this->waitSecond)) {
				$this->assign('waitSecond', modC('ERROR_WAIT_TIME', '10', 'config'));
			}
			if (!isset($this->jumpUrl)) {
				$this->assign('jumpUrl', 'javascript:history.back(-1);');
			}
			$this->display(C('TMPL_ACTION_ERROR'));
			exit;
		}
	}
	public function __destruct()
	{
		Hook::listen('action_end');
	}
	public function checkAuth($_var_97 = '', $_var_98 = -1, $_var_99 = '')
	{
		if (!check_auth($_var_97, $_var_98)) {
			$this->error(empty($_var_99) ? '您无操作权限。' : $_var_99);
		}
	}
	public function checkActionLimit($_var_100 = null, $_var_101 = null, $_var_102 = null, $_var_103 = null, $_var_104 = false, $_var_105 = false)
	{
		$_var_106 = check_action_limit($_var_100, $_var_101, $_var_102, $_var_103, $_var_104);
		if ($_var_106 && !$_var_106['state']) {
			if ($_var_105 === true) {
				$_var_105 = $_var_106['url'];
			} elseif ($_var_105 === false) {
				$_var_105 = '';
			}
			$this->error($_var_106['info'], $_var_105);
		}
	}
}
class_alias('think\\Controller', 'think\\Action');