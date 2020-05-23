<?php
return array(
    'name' => 'ucenter',//模块名
    'alias' => '用户中心', //别名
    'version' => '2.0.0',//版本号
    'is_com' => 0, //是否商业模块,1是，0，否
    'show_nav' => 1, //是否显示在导航栏内？  1是，0否
    'summary' => '用户中心模块，系统核心模块',
    'developer' => '靑年PHP开发组',
    'website' => '',
    'entry' => 'ucenter/index/index', //前台入口，可用U函数
    'admin_entry' => 'admin/user/index',
    'icon'=>'user',
    'can_uninstall' => 0
);