<?php
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'    => SITE_DIR_NAME . '_', // 缓存前缀
    'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型
    //'MODULE_ALLOW_LIST'  => array('manage'),
    'URL_MODEL'            => 3, //URL模式
    'URL_HTML_SUFFIX'      => '',
    

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './uploads/download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
		'mimes'    => '', //允许上传的文件MiMe类型
		'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
		'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
		'autoSub'  => true, //自动子目录保存文件
		'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'rootPath' => './uploads/picture/', //保存根路径
		'savePath' => '', //保存路径
		'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveExt'  => '', //文件保存后缀，空则使用原后缀
		'replace'  => false, //存在同名是否覆盖
		'hash'     => true, //是否生成hash编码
		'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    // 设置默认的模板主题
    'DEFAULT_THEME'  => 'default',//当模块中没有设置主题，则模块主题会设置为此处设置的主题,主题名和模块名不能重复，如不能采用“Home”

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
    		'__STATIC__' 	=> __ROOT__ . '/public/static',
    		'__ADDONS__' 	=> __ROOT__ . '/public/' . MODULE_NAME . '/addons',
    		'__ASSETS__' 	=> __ROOT__ . '/public',
    		'__IMG__' 		=> __ROOT__ . '/app/' . MODULE_NAME . '/static/images',
    		'__CSS__' 		=> __ROOT__ . '/app/' . MODULE_NAME . '/static/css',
    		'__JS__' 		=> __ROOT__ . '/app/' . MODULE_NAME . '/static/js',
    		'__ADMIN__' 	=> __ROOT__ . '/app/' . MODULE_NAME . '/static',
    		'__LAYER__' 	=> __ROOT__ . '/app/' . MODULE_NAME . '/static/layui',
			'__Layer__' 	=> __ROOT__ . '/app/' . MODULE_NAME . '/static/layui',
            '__CLOUD__' 	=> 'http://www.qkephp.com',
    ),
    '__CLOUD__'=>'http://www.qkephp.com',
    'UPDATE_PATH'=>'./data/update/',
    'CLOUD_PATH'=>'./data/cloud/',
    
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'qn_admin', //session前缀
    'COOKIE_PREFIX'  => 'qn_admin_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug
    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR' => MODULE_PATH . 'view/default/public/error.html', // 默认错误跳转对应的模板文件
    //'TMPL_ACTION_SUCCESS' => MODULE_PATH . 'view/default/public/success.html', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => MODULE_PATH . 'view/default/public/exception.html',// 异常页面的模板文件
);
