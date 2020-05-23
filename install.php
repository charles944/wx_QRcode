<?php
header("Content-Type:text/html;charset=utf-8");
function reset_session_path()
{
    $root = str_replace("\\", '/', dirname(__FILE__));
    $savePath = $root . "/tmp/";
    if (!file_exists($savePath))
        @mkdir($savePath, 0777);
    session_save_path($savePath);
}

//reset_session_path();  //如果您的服务器无法安装或者无法登陆，又或者后台验证码无限错误，请尝试取消本行起始两条左斜杠，让本行代码生效，以修改session存储的路径

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('当前PHP版本'. PHP_VERSION .'，最低要求PHP版本5.3.0 <br/><br/>最低要求PHP版本5.3.0 <br/><br/>很遗憾，未能达到最低要求。本系统必须运行在PHP5.3 及以上版本。如果您是虚拟主机，请联系空间商升级PHP版本，如果您是VPS用户，请自行升级php版本或者联系VPS提供商寻求技术支持。');

//此处APP_DEBUG一定要设置为true，否则安装后会生成错误的缓存文件
define ( 'APP_DEBUG', true );
define( 'BIND_MODULE' , 'install' );

// 网站根路径设置
define ( 'SITE_PATH', dirname ( __FILE__ ) );

// 应用目录设置
define ('APP_PATH', './app/');

// 主题目录模板地址
define ('QN_THEME_PATH', './themes/');

// 缓存目录设置 此目录必须可写
define ('RUNTIME_PATH', './~runtime/');

// 引入核心
require APP_PATH . 'framework/qiankuncore.php';