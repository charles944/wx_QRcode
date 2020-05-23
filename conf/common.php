<?php
/**
* +----------------------------------------------------------------------------+
* | 靑年 PHP  &  QingNianPHP										           |
* +----------------------------------------------------------------------------+
* | Copyright (c) 2014~2017 http://www.bajiuniandai.com All rights reserved.   |
* +----------------------------------------------------------------------------+
* | Author: 重庆捌玖年代网络科技有限公司版权所有（朝兮夕兮，那你自己想想）     |
* +----------------------------------------------------------------------------+
**/
/**
* 系统配文件
* 所有系统级别的配置
*/
return array(
/* 模块相关配置 */
'AUTOLOAD_NAMESPACE' => array('addons' => QN_ADDON_PATH,'plugins' => QN_PLUGIN_PATH), //扩展模块列表
'DEFAULT_MODULE'     => 'home',
'MODULE_DENY_LIST'   => array('common', 'user'),
//'MODULE_ALLOW_LIST'  => array('Home','Admin'),
'URL_MODULE_MAP'=>array(
    'manage'      =>  'admin',
),

/* 系统数据加密设置 */
'DATA_AUTH_KEY' => '&5v;W~>F]Jc2ZU3!bAf?Bq[n_k/VY*G$jlTiE96H', //默认数据加密KEY

/* 错误页面模板 */
// 默认错误跳转对应的模板文件
//'TMPL_ACTION_ERROR'     =>  './public/error.html', 
// 默认成功跳转对应的模板文件
//'TMPL_ACTION_SUCCESS'   =>  './public/success.html', 
// 异常页面的模板文件
//'TMPL_EXCEPTION_FILE'   =>  './public/exception.html',

/* 模板相关配置 */
//此处只做模板使用，具体替换在COMMON模块中的set_theme函数,该函数替换MODULE_NAME,DEFAULT_THEME两个值为设置值
'DEFAULT_THEME' => 'default', // 默认模板主题名称

/* 模板相关配置 */
'TMPL_PARSE_STRING' => array(
'__STATIC__' => __ROOT__ . '/public/static',
'__Layer__' => __ROOT__ . '/public/layui',
'__PUBLIC_CSS__' => __ROOT__ . '/public/css',
),

'URL_HTML_SUFFIX'       =>  '.html',  // URL伪静态后缀设置


//子域名配置 
//格式如: '子域名'=>array('分组名/[模块名]','var1=a&var2=b'); 

'APP_SUB_DOMAIN_DEPLOY'=>1, 	// 开启二级子域名配置
'APP_SUB_DOMAIN_RULES'=>array(   
	'api'=>array('api/'),  // api域名指向Api分组
),

/* 调试配置 */
'SHOW_PAGE_TRACE' => 1,

/* 用户相关设置 */
'USER_MAX_CACHE'     => 5000, //最大缓存用户数
'USER_ADMINISTRATOR' => 1, //管理员用户ID

/* URL配置 */
'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
'URL_MODEL'            => 3, //URL模式  默认关闭伪静态
'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

/* 全局过滤配置 */
'DEFAULT_FILTER' => 'safe', //全局过滤函数

/* 数据缓存设置 */
'DATA_CACHE_TYPE' => 'File', // 数据缓存类型
'DATA_CACHE_PREFIX' => SITE_DIR_NAME . '_', // 缓存前缀
'MEMCACHE_HOST' => '127.0.0.1',
'MEMCACHE_PORT' => 11211,
'DATA_CACHE_TIMEOUT' => 86400,

/* 阿里云OCS数据缓存设置 */
//'DATA_CACHE_TYPE'     =>  'Memcached',  //如果启用阿里云OCS DATA_CACHE_TYPE用此句
//'SESSION_TYPE'		=>	'Memcached', //阿里云OCS SESSSION缓存
//'MEMCACHED_SERVER' 	=>	'xxxxxxxx.ocs.aliyuncs.com',//阿里云 OCS Server地址
//'MEMCACHED_PORT' 		=>	11211,//端口
//'MEMCACHED_USERNAME'	=>	'xxxxxxxx',//阿里云 OCS 账户
//'MEMCACHED_PASSWORD'	=>	'xxxxxxxx',//阿里云 OCS 密码

/* Couchbase缓存分布式配置 */
//'SESSION_TYPE'	=>  'Couchbase'
//'DATA_CACHE_TYPE'	=>  'Couchbase'
//'COUCH_PORT'		=>	'8091',
//'COUCH_HOST'		=>	'localhost',
//'COUCH_USER'		=>	'',
//'COUCH_PASS'		=>	'',
//'COUCH_PREFIX'	=>	'',
//'COUCH_EXPIRE'	=>	7200,
//'COUCH_BUCKETS'	=>	'' //数据桶

//redis 分布式配置
'REDIS_SERVER' 		=> '127.0.0.1',
'REDIS_PORT' 			=> 6379,
'REDIS_PCONNECT' 		=> 0,
'REDIS_REQUIREPASS' 	=> '',
'REDIS_TIMEOUT' 		=> 1,

/* 数据库配置 */
'DB_TYPE'   => 'mysql', // 数据库类型
'DB_HOST'   => '127.0.0.1', // 服务器地址
'DB_NAME'   => 'huoma', // 数据库名
'DB_USER'   => 'sucaishui', // 用户名
'DB_PWD'    => '123456',  // 密码
'DB_PORT'   => '3306', // 端口
'DB_PREFIX' => 'qn_', // 数据库表前缀
'DB_DEPLOY_TYPE'		=>  0,			// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
'DB_RW_SEPARATE'        =>  false,      // 数据库读写是否分离 true主从式有效，想要实现主从式，设置此项为true
'DB_MASTER_NUM'         =>  1, 			// 读写分离后 主服务器数量
'DB_SLAVE_NO'           =>  '', 		// 指定从服务器序号
'DB_SQL_BUILD_LENGTH'   =>  1000, 		// SQL缓存的队列长度20条记录
'DB_SQL_BUILD_QUEUE'    =>  'file',   	// SQL缓存队列的缓存方式 支持 file xcache和apc
'DB_SQL_LOG'            =>  false, 		// SQL执行日志记录

/* 文档模型配置 (文档模型核心配置，请勿更改) */
//'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
'LOAD_EXT_CONFIG' => 'router',

/* 文件上传相关配置 */
'DOWNLOAD_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './uploads/download/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => false, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
), //下载模型上传配置（文件上传类配置）


/* 图片上传相关配置 */
'PICTURE_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './uploads/picture/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => true, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
), //图片上传相关配置（文件上传类配置）

//本地上传文件驱动配置
'PICTURE_UPLOAD_DRIVER'=>'local',
'DOWNLOAD_UPLOAD_DRIVER'=>'local',

'UPLOAD_LOCAL_CONFIG'=>array(),

/* 编辑器图片上传相关配置 */
'EDITOR_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './uploads/editor/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => false, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
),


);
