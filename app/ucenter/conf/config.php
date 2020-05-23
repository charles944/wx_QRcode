<?php
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'ot\\taglib\\Article,ot\\taglib\\Think',

    /* 主题设置 */
    'DEFAULT_USER_THEME' => 'default', // 默认模板主题名称
    'DEFAULT_THEME'      => 'default',
	
	'URL_MODEL'            => 2, //URL模式
    
    /* 模板相关配置 */
    //此处只做模板使用，具体替换在COMMON模块中的set_theme函数,该函数替换MODULE_NAME,DEFAULT_THEME两个值为设置值

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
		'__STATIC__' 			=> __ROOT__ . '/public/static',
		'__Layer__' 			=> __ROOT__ . '/public/layui',
		'__ADDONS__' 			=> __ROOT__ . '/public/' . MODULE_NAME . '/addons',
		'__IMG__' 				=> __ROOT__ . '/app/' . MODULE_NAME . '/static/images',
		'__CSS__' 				=> __ROOT__ . '/app/' . MODULE_NAME . '/static/css',
		'__JS__' 				=> __ROOT__ . '/app/' . MODULE_NAME . '/static/js',
		'__APPLICATION__'		=> __ROOT__ . '/app/',
		'__PUBLIC__' 			=> __ROOT__ . '/public',
		'__PUBLIC_JS__' 		=> __ROOT__ . '/public/js',
		'__COMMON_STATIC__' 	=> __ROOT__ . '/app/common/static',
    ),
);