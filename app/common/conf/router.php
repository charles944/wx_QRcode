<?php
return array(

    /**
     * 路由的key必须写全称,且必须全小写. 比如: 使用'wap/index/index', 而非'wap'.
     */
    'router' => array(

		/*系统首页*/
		'home/index/index'          =>  'home',
      
        /*活码伪静态*/
        'qrcode/index/query'		=>	'news-[id]',

		/*用户中心*/
        'ucenter/index/index'                => 'ucenter/[uid]',
        'ucenter/index/applist'              => 'ucenter/applist_[type]/[uid]',
        'ucenter/index/rank'                 => 'ucenter/rank_[uid]',
        'ucenter/index/rankverifywait'       => 'ucenter/rankwait_[uid]',
        'ucenter/index/rankverifyfailure'    => 'ucenter/rankfailure_[uid]',
        'ucenter/index/rankverify'           => 'ucenter/rankverify_[uid]',
        'ucenter/config/index'               => 'ucenter/conf',
        'ucenter/config/tag'                 => 'ucenter/tag',
        'ucenter/config/avatar'              => 'ucenter/avatar',
        'ucenter/config/password'            => 'ucenter/password',
        'ucenter/config/score'               => 'ucenter/score',
        'ucenter/config/role'                => 'ucenter/role',
        'ucenter/config/other'               => 'ucenter/other',
		'ucenter/config/mobile'              => 'ucenter/mobile',
		'ucenter/config/email'               => 'ucenter/email',
		'ucenter/config/safepass'            => 'ucenter/safepass',
        'ucenter/message/message'            => 'ucenter/msg_[tab]',
        'ucenter/invite/invite'              => 'ucenter/invite',
        'ucenter/invite/index'               => 'ucenter/invite_create',
		'ucenter/cash/index'				 => 'ucenter/cash',
		'ucenter/system/index'				 => 'ucenter/system',
		'ucenter/my/index'					 => 'ucenter/my',

		/*注册登录*/
        'home/member/login'                  => 'login',
        'home/member/register'               => 'register/[type]/c_[code]',
    ),

);