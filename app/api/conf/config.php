<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD'     =>    'ot\\taglib\\Article,ot\\taglib\\Think',
        
    /* 主题设置 */
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'qn_home', //session前缀
    'COOKIE_PREFIX'  => 'qn_home', // Cookie前缀 避免冲突

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/public/static',
        '__ADDONS__' => __ROOT__ . '/public/' . MODULE_NAME . '/addons',
        '__IMG__'    => __ROOT__ . '/public/' . MODULE_NAME . '/images',
        '__CSS__'    => __ROOT__ . '/public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/public/' . MODULE_NAME . '/js',
    ),
);