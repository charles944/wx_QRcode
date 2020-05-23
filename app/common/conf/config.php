<?php
/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */
if (is_file('./conf/common.php'))
    return array_merge(require_once('./conf/common.php'),array(
        'LANG_SWITCH_ON' => true,
        'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
		'DEFAULT_LANG' => 'zh-cn', // 默认语言
        'LANG_LIST'        => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
        'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
    ));