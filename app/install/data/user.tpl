<?php
/**
* +----------------------------------------------------------------------------+
* | 靑年 PHP  &  QingNianPHP													    |
* +----------------------------------------------------------------------------+
* | Copyright (c) 2014~2016 http://www.bajiuniandai.com All rights reserved.	|
* +----------------------------------------------------------------------------+
* | Author: 重庆捌玖年代网络科技有限公司版权所有（朝兮夕兮，那你自己想想）		|
* +----------------------------------------------------------------------------+
**/
/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', '[AUTH_KEY]'); //加密KEY
define('UC_DB_DSN', '[DB_TYPE]://[DB_USER]:[DB_PWD]@[DB_HOST]:[DB_PORT]/[DB_NAME]'); // 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', '[DB_PREFIX]'); // 数据表前缀，使用Model方式调用API必须配置此项
