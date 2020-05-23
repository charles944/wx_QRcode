<?php
/**
 * ThinkPHP 普通模式定义
 */
return array(
    // 配置文件
    'config'    =>  array(
        THINK_PATH.'conf/convention.php',   // 系统惯例配置
        CONF_PATH.'config'.CONF_EXT,      // 应用公共配置
    ),

    // 别名定义
    'alias'     =>  array(
        'think\Log'               => CORE_PATH . 'Log'.EXT,
        'think\log\driver\File'   => CORE_PATH . 'log/driver/File'.EXT,
        'think\Exception'         => CORE_PATH . 'Exception'.EXT,
        'think\Model'             => CORE_PATH . 'Model'.EXT,
        'think\Db'                => CORE_PATH . 'Db'.EXT,
        'think\Template'          => CORE_PATH . 'Template'.EXT,
        'think\Cache'             => CORE_PATH . 'Cache'.EXT,
        'think\cache\driver\File' => CORE_PATH . 'cache/driver/File'.EXT,
        'think\Storage'           => CORE_PATH . 'Storage'.EXT,
    ),

    // 函数和类文件
    'core'      =>  array(
        THINK_PATH.'common/functions.php',
        COMMON_PATH.'common/function.php',
        CORE_PATH . 'Hook'.EXT,
        CORE_PATH . 'App'.EXT,
        CORE_PATH . 'Dispatcher'.EXT,
        //CORE_PATH . 'Log'.EXT,
        CORE_PATH . 'Route'.EXT,
        CORE_PATH . 'Controller'.EXT,
        CORE_PATH . 'View'.EXT,
        BEHAVIOR_PATH . 'BuildLiteBehavior'.EXT,
        BEHAVIOR_PATH . 'ParseTemplateBehavior'.EXT,
        BEHAVIOR_PATH . 'ContentReplaceBehavior'.EXT,
    ),
    // 行为扩展定义
    'tags'  =>  array(
        'app_init'     =>  array(
            'behavior\BuildLiteBehavior', // 生成运行Lite文件
        ),        
        'app_begin'     =>  array(
            'behavior\ReadHtmlCacheBehavior', // 读取静态缓存
        ),
        'app_end'       =>  array(
            'behavior\ShowPageTraceBehavior', // 页面Trace显示
        ),
        'view_parse'    =>  array(
            'behavior\ParseTemplateBehavior', // 模板解析 支持PHP、内置模板引擎和第三方模板引擎
        ),
        'template_filter'=> array(
            'behavior\ContentReplaceBehavior', // 模板输出替换
        ),
        'view_filter'   =>  array(
            'behavior\WriteHtmlCacheBehavior', // 写入静态缓存
        ),
    ),
);
