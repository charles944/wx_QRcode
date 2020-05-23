<?php
/**
 * 安装程序配置文件
 */
define('INSTALL_APP_PATH', realpath('./') . '/');

return array(

    'ORIGINAL_TABLE_PREFIX' => 'qn_', //默认表前缀

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__IMG__' 			=> __ROOT__ . '/public/images',
        '__CSS__' 			=> __ROOT__ . '/public/css',
        '__JS__'			=> __ROOT__ . '/public/js',
    	'__Layer__'			=> __ROOT__ . '/public/layui',
		'__PUBLIC__' 		=> __ROOT__ . '/public',
		'__COMMON_STATIC__' => __ROOT__ . '/app/common/static',
		'__COMMON_STATIC__' => __ROOT__ . '/app/common/static',
        '__WEBSITE__'		=>'http://www.qkephp.com/',
        '__COMPANY_WEBSITE__'=>'http://www.qkephp.com/'
    ),
    /* URL配置 */
    'URL_MODEL' => 3, //URL模式
    
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称
);