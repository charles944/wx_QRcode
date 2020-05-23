<?php

//decode by http://www.yunlu99.com/
namespace think;

error_reporting(E_ALL ^ E_NOTICE);
class View
{
	protected $tVar = array();
	protected $theme = '';
	public function assign($_var_0, $_var_1 = '')
	{
		if (is_array($_var_0)) {
			$this->tVar = array_merge($this->tVar, $_var_0);
		} else {
			$this->tVar[$_var_0] = $_var_1;
		}
	}
	public function get($_var_2 = '')
	{
		if ('' === $_var_2) {
			return $this->tVar;
		}
		return isset($this->tVar[$_var_2]) ? $this->tVar[$_var_2] : false;
	}
	public function display($_var_3 = '', $_var_4 = '', $_var_5 = '', $_var_6 = '', $_var_7 = '')
	{
		G('viewStartTime');
		Hook::listen('view_begin', $_var_3);
		$_var_6 = $this->fetch($_var_3, $_var_6, $_var_7);
		$this->render($_var_6, $_var_4, $_var_5);
		Hook::listen('view_end');
	}
	private function render($_var_8, $_var_9 = '', $_var_10 = '')
	{
		if (empty($_var_9)) {
			$_var_9 = C('DEFAULT_CHARSET');
		}
		if (empty($_var_10)) {
			$_var_10 = C('TMPL_CONTENT_TYPE');
		}
		header('Content-Type:' . $_var_10 . '; charset=' . $_var_9);
		header('Cache-control: ' . C('HTTP_CACHE_CONTROL'));
		header('X-Powered-By:QingNianPHP');
		echo $_var_8;
	}
	public function fetch($_var_11 = '', $_var_12 = '', $_var_13 = '')
	{
		if (empty($_var_12)) {
			$_var_11 = $this->parseTemplate($_var_11);
			if (!is_file($_var_11)) {
				E(L('_TEMPLATE_NOT_EXIST_') . ':' . $_var_11);
			}
		}
		ob_start();
		ob_implicit_flush(0);
		if ('php' == strtolower(C('TMPL_ENGINE_TYPE'))) {
			$_var_14 = $_var_12;
			extract($this->tVar, EXTR_OVERWRITE);
			empty($_var_14) ? include $_var_11 : eval('?>' . $_var_14);
		} else {
			$_var_15 = array('var' => $this->tVar, 'file' => $_var_11, 'content' => $_var_12, 'prefix' => $_var_13);
			Hook::listen('view_parse', $_var_15);
		}
		$_var_12 = ob_get_clean();
		Hook::listen('view_filter', $_var_12);
		return $_var_12;
	}
	public function parseTemplate($_var_16 = '')
	{
		if (is_file($_var_16)) {
			return $_var_16;
		}
		$_var_17 = C('TMPL_FILE_DEPR');
		$_var_16 = str_replace(':', $_var_17, $_var_16);
		$_var_18 = MODULE_NAME;
		if (strpos($_var_16, '@')) {
			list($_var_18, $_var_16) = explode('@', $_var_16);
		}
		if ('' == $_var_16) {
			if (isMobile()) {
				$_var_16 = strtolower(CONTROLLER_NAME) . $_var_17 . 'wap' . ACTION_NAME;
			} else {
				$_var_16 = strtolower(CONTROLLER_NAME) . $_var_17 . ACTION_NAME;
			}
		} elseif (false === strpos($_var_16, $_var_17)) {
			if (isMobile()) {
				$_var_16 = strtolower(CONTROLLER_NAME) . $_var_17 . 'wap' . $_var_16;
			} else {
				$_var_16 = strtolower(CONTROLLER_NAME) . $_var_17 . $_var_16;
			}
		}
		$_var_19 = cookie('TO_LOOK_THEME', '', array('prefix' => 'WZBV2'));
		if ($_var_19) {
			if ($_var_19 != 'default') {
				if (!defined('NOW_THEME_PATH')) {
					$_var_20 = QN_THEME_PATH . $_var_19 . '/' . $_var_18 . '/' . C('DEFAULT_V_LAYER') . '/';
					define('NOW_THEME_PATH', $_var_20);
				}
				$_var_21 = NOW_THEME_PATH . $_var_16 . C('TMPL_TEMPLATE_SUFFIX');
			}
		} else {
			$_var_22 = modC('NOW_THEME', 'default', 'Theme');
			if ($_var_22 != 'default') {
				if (!defined('NOW_THEME_PATH')) {
					$_var_20 = QN_THEME_PATH . $_var_22 . '/' . $_var_18 . '/' . C('DEFAULT_V_LAYER') . '/';
					define('NOW_THEME_PATH', $_var_20);
				}
				$_var_21 = NOW_THEME_PATH . $_var_16 . C('TMPL_TEMPLATE_SUFFIX');
			}
		}
		if (isset($_var_21) && is_file($_var_21)) {
			return $_var_21;
		}
		if (!defined('THEME_PATH')) {
			if (C('VIEW_PATH')) {
				$_var_20 = C('VIEW_PATH');
			} else {
				$_var_20 = defined('TMPL_PATH') ? TMPL_PATH . $_var_18 . '/' : APP_PATH . $_var_18 . '/' . C('DEFAULT_V_LAYER') . '/';
			}
			$_var_23 = $this->getTemplateTheme();
			define('THEME_PATH', $_var_20 . $_var_23);
		}
		$_var_21 = THEME_PATH . $_var_16 . C('TMPL_TEMPLATE_SUFFIX');
		if (!is_file($_var_21)) {
			$_var_19 = cookie('TO_LOOK_THEME', '', array('prefix' => 'WZBV2'));
			if ($_var_19) {
				if ($_var_19 != 'default') {
					$_var_24 = QN_THEME_PATH . $_var_19 . '/common/' . C('DEFAULT_V_LAYER') . '/' . $_var_21 . C('TMPL_TEMPLATE_SUFFIX');
				}
			} else {
				$_var_22 = modC('NOW_THEME', 'default', 'Theme');
				if ($_var_22 != 'default') {
					$_var_24 = QN_THEME_PATH . $_var_22 . '/common/' . C('DEFAULT_V_LAYER') . '/' . $_var_21 . C('TMPL_TEMPLATE_SUFFIX');
				}
			}
			if (isset($_var_24) && is_file($_var_24)) {
				return $_var_24;
			}
		}
		if (C('TMPL_LOAD_DEFAULTTHEME') && THEME_NAME != C('DEFAULT_THEME') && !is_file($_var_21)) {
			$_var_21 = dirname(THEME_PATH) . '/' . C('DEFAULT_THEME') . '/' . $_var_16 . C('TMPL_TEMPLATE_SUFFIX');
		}
		if (isMobile() && !is_file($_var_21)) {
			$_var_21 = str_replace('wap', '', $_var_21);
		}
		return $_var_21;
	}
	public function theme($_var_25)
	{
		$this->theme = $_var_25;
		return $this;
	}
	private function getTemplateTheme()
	{
		if ($this->theme) {
			$_var_26 = $this->theme;
		} else {
			$_var_26 = C('DEFAULT_THEME');
			if (C('TMPL_DETECT_THEME')) {
				$_var_27 = C('VAR_TEMPLATE');
				if (isset($_GET[$_var_27])) {
					$_var_26 = $_GET[$_var_27];
				} elseif (cookie('think_template')) {
					$_var_26 = cookie('think_template');
				}
				if (!in_array($_var_26, explode(',', C('THEME_LIST')))) {
					$_var_26 = C('DEFAULT_THEME');
				}
				cookie('think_template', $_var_26, 864000);
			}
		}
		defined('THEME_NAME') || define('THEME_NAME', $_var_26);
		return $_var_26 ? $_var_26 . '/' : '';
	}
}