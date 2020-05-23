<?php
/**
 * ThinkPHP SAE应用模式定义文件
 */
return array(
    // 配置文件
    'config'    =>  array(
        THINK_PATH.'conf/convention.php',   // 系统惯例配置
        CONF_PATH.'config.php',      // 应用公共配置
        THINK_PATH.'conf/convention_sae.php',//[sae] sae的惯例配置
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
        CORE_PATH . 'Behavior'.EXT,		
        BEHAVIOR_PATH . 'ReadHtmlCacheBehavior'.EXT,
        BEHAVIOR_PATH . 'ShowPageTraceBehavior'.EXT,
        BEHAVIOR_PATH . 'ParseTemplateBehavior'.EXT,
        BEHAVIOR_PATH . 'ContentReplaceBehavior'.EXT,
        BEHAVIOR_PATH . 'WriteHtmlCacheBehavior'.EXT,
    ),
    // 行为扩展定义
    'tags'  =>  array(
        'app_init'      =>  array(
        ),
        'app_begin'     =>  array(
            'behavior\ReadHtmlCache', // 读取静态缓存
        ),
        'app_end'       =>  array(
            'behavior\ShowPageTrace', // 页面Trace显示
        ),
        'path_info'     =>  array(),
        'action_begin'  =>  array(),
        'action_end'    =>  array(),
        'view_begin'    =>  array(),
        'view_parse'    =>  array(
            'behavior\ParseTemplate', // 模板解析 支持PHP、内置模板引擎和第三方模板引擎
        ),
        'template_filter'=> array(
            'behavior\ContentReplace', // 模板输出替换
        ),
        'view_filter'   =>  array(
            'behavior\WriteHtmlCache', // 写入静态缓存
        ),
        'view_end'      =>  array(),
    ),
);
