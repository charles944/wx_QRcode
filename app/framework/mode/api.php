<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
      
/**
 * ThinkPHP API模式定义
 */
return array(
    // 配置文件
    'config'    =>  array(
        THINK_PATH.'conf/convention.php',   // 系统惯例配置
        CONF_PATH.'config.php',      // 应用公共配置
    ),

    // 别名定义
    'alias'     =>  array(
        'think\Exception'         => CORE_PATH . 'Exception'.EXT,
        'think\Model'             => CORE_PATH . 'Model'.EXT,
        'think\Db'                => CORE_PATH . 'Db'.EXT,
        'think\Cache'             => CORE_PATH . 'Cache'.EXT,
        'think\cache\driver\File' => CORE_PATH . 'cache/driver/File'.EXT,
        'think\Storage'           => CORE_PATH . 'Storage'.EXT,
    ),

    // 函数和类文件
    'core'      =>  array(
        MODE_PATH.'api/functions.php',
        COMMON_PATH.'common/function.php',
        MODE_PATH . 'api/App'.EXT,
        MODE_PATH . 'api/Dispatcher'.EXT,
        MODE_PATH . 'api/Controller'.EXT,
        CORE_PATH . 'Behavior'.EXT,
    ),
    // 行为扩展定义
    'tags'  =>  array(
    ),
);
