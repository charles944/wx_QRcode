<?php

namespace home\controller;
use think\Controller;

/**
 * 扩展控制器
 * 用于调度各个扩展的URL访问需求
 */
class AddonsController extends Controller{

    public function _initialize(){
        /* 读取数据库中的配置 */
        /* $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); */ //添加配置
    }

    protected $addons = null;

    public function execute($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons = ucfirst(parse_name($_addons, 1));
            $_controller = parse_name($_controller,1);
        }
        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . "/addons/{$_addons}";
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
        if(!empty($_addons) && !empty($_controller) && !empty($_action)){
            $Addons = A("Addons://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->error('没有指定插件名称，控制器或操作！');
        }
    }
	
	public function plugin($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons = ucfirst(parse_name($_addons, 1));
            $_controller = parse_name($_controller,1);
        }
        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . "/plugins/{$_addons}";
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);

        if(!empty($_addons) && !empty($_controller) && !empty($_action)){
            $Addons = A("Plugins://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->error('没有指定插件名称，控制器或操作！');
        }
	}
	
	/**
	 * 重写模板显示 调用内置的模板引擎显示方法，
	 *
	 * @access protected
	 * @param string $templateFile
	 *        	指定要调用的模板文件
	 *        	默认为空 由系统自动定位模板文件
	 *        	支持格式: 空, index, UserCenter/index 和 完整的地址
	 * @param string $charset
	 *        	输出编码
	 * @param string $contentType
	 *        	输出类型
	 * @param string $content
	 *        	输出内容
	 * @param string $prefix
	 *        	模板缓存前缀
	 * @return void
	 */
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$templateFile = $this->getAddonTemplate ( $templateFile );
		$this->view->display ( $templateFile, $charset, $contentType, $content, $prefix );
	}
	
	function getAddonTemplate($templateFile = '') {
		if (file_exists ( $templateFile )) {
			return $templateFile;
		}
		$type = is_dir ( QN_PLUGIN_PATH . _ADDONS ) ? 'plugins' : 'addons';
		$oldFile = $templateFile;
		if (empty ( $templateFile )) {
			$templateFile = T ( $type . '://' . _ADDONS . '@' . _CONTROLLER . '/' . _ACTION );
		} elseif (stripos ( $templateFile, '/addons/' ) === false && stripos ( $templateFile, THINK_PATH ) === false) {
			if (stripos ( $templateFile, '/' ) === false) { // 如index
				$templateFile = T ( $type . '://' . _ADDONS . '@' . _CONTROLLER . '/' . $templateFile );
			} elseif (stripos ( $templateFile, '@' ) === false) { // // 如 UserCenter/index
				$templateFile = T ( $type . '://' . _ADDONS . '@' . $templateFile );
			}
		}
		
		if (stripos ( $templateFile, '/addons/' ) !== false && ! file_exists ( $templateFile )) {
			$templateFile = ! empty ( $oldFile ) && stripos ( $oldFile, '/' ) === false ? $oldFile : _ACTION;
		}
		return $templateFile;
	}

}
