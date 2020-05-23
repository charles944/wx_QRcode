<?php
return array(
    //模块名
    'name' => 'qrcode',
    //别名
    'alias' => '活码系统',
    //版本号
    'version' => '1.3.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '活码系统',
    //开发者
    'developer' => '朝兮夕兮',
    //开发者网站
    'website' => '',
    //前台入口，可用U函数
    'entry' => 'qrcode/index/index',

    'admin_entry' => 'admin/qrcode/index',

    'icon' => 'book-open',

    'can_uninstall' => 1,
	
	'has_tage'=>0

);