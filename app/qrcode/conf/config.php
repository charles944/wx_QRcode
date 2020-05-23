<?php
return array(
    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'ot\\taglib\\Article,ot\\taglib\\Think',

    /* 主题设置 */
    'DEFAULT_THEME' => 'default', // 默认模板主题名称
	
	'URL_MODEL'            => 2, //URL模式

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' 		=> __ROOT__ . '/public/static',
		'__Layer__' 		=> __ROOT__ . '/public/layui',
        '__ADDONS__'		=> __ROOT__ . '/public/' . MODULE_NAME . '/addons',
        '__IMG__' 			=> __ROOT__ . '/app/'.MODULE_NAME   . '/static/images',
        '__CSS__' 			=> __ROOT__ . '/app/'.MODULE_NAME .'/static/css',
        '__JS__' 			=> __ROOT__ . '/app/'.MODULE_NAME. '/static/js',
        '__MSTATIC__'       => __ROOT__ . '/app/'.MODULE_NAME. '/static',
		'__PUBLIC__' 		=> __ROOT__ . '/public',
    	'__PUBLIC_JS__' 	=> __ROOT__ . '/public/js',
		'__PUBLIC_CSS__' 	=> __ROOT__ . '/public/css',
		'__COMMON_STATIC__' => __ROOT__ . '/app/common/static',
    ),

    'NEED_VERIFY'=>0,//此处控制默认是否需要审核，该配置项为了便于部署起见，暂时通过在此修改来设定。
);