-- -----------------------------
-- 表结构 `qn_ucenter_admin`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qn_ucenter_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员用户ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员表';


-- -----------------------------
-- 表结构 `qn_ucenter_extend_log`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qn_ucenter_extend_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `uc_uid` int(11) NOT NULL,
  `uc_username` varchar(200) NOT NULL,
  `uc_email` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qn_ucenter_member`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qn_ucenter_member` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(200) DEFAULT NULL COMMENT '用户名',
  `password` char(32) DEFAULT NULL COMMENT '密码',
  `email` varchar(200) DEFAULT NULL COMMENT '用户邮箱',
  `email_ver` tinyint(3) NOT NULL DEFAULT '0' COMMENT '邮箱验证',
  `mobile` char(15) DEFAULT '' COMMENT '用户手机',
  `mobile_ver` tinyint(3) NOT NULL DEFAULT '0' COMMENT '手机验证',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` int(3) NOT NULL DEFAULT '1' COMMENT '用户状态',
  `type` tinyint(3) DEFAULT '1' COMMENT '1为用户名注册，2为邮箱注册，3为手机注册',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='用户表';


-- -----------------------------
-- 表结构 `qn_ucenter_setting`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qn_ucenter_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型（1-用户配置）',
  `value` text NOT NULL COMMENT '配置数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置表';


-- -----------------------------
-- 表结构 `qn_ucenter_user_link`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qn_ucenter_user_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `uc_uid` int(11) NOT NULL,
  `uc_username` varchar(50) NOT NULL,
  `uc_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表内记录 `qn_ucenter_member`
-- -----------------------------
INSERT INTO `qn_ucenter_member` VALUES ('1', 'admin', '3885baf07540a0d9a43f55a987a6b0aa', '411924848@qq.com', '1', '', '0', '1478586535', '1007428059', '1498469915', '0', '1478586535', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('2', 'test', 'c131a834cf964190a0c952343f1f3604', '', '0', '', '0', '1481639529', '0', '1497843932', '0', '1481639529', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('3', 'test1', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '', '0', '1481639688', '0', '1498133076', '0', '1481639688', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('4', 'f59furOpaB', '3885baf07540a0d9a43f55a987a6b0aa', 'bbc@bbc.com', '0', '', '0', '1483624060', '0', '1488265524', '0', '1483624060', '1', '2');
INSERT INTO `qn_ucenter_member` VALUES ('5', '5YEt7DCnYo', '3885baf07540a0d9a43f55a987a6b0aa', 'bbc1@bbc.com', '0', '', '0', '1483624245', '0', '1483624246', '0', '1483624245', '1', '2');
INSERT INTO `qn_ucenter_member` VALUES ('6', 'bmuK77AoGL', 'c131a834cf964190a0c952343f1f3604', 'bbc2@bbc.com', '0', '', '0', '1483625115', '0', '1483625116', '0', '1483625115', '1', '2');
INSERT INTO `qn_ucenter_member` VALUES ('7', 'aqqCutGuMI', 'c131a834cf964190a0c952343f1f3604', 'bbc3@jj.com', '0', '', '0', '1483628515', '0', '1483628516', '0', '1483628515', '0', '2');
INSERT INTO `qn_ucenter_member` VALUES ('8', '3VHFH6F0d3', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '13112323232', '0', '1495091541', '0', '1495091541', '0', '1495091541', '1', '3');
INSERT INTO `qn_ucenter_member` VALUES ('9', 'jDsa5XhoLw', '3885baf07540a0d9a43f55a987a6b0aa', '无', '0', '13116753592', '0', '1495091765', '0', '1495098748', '0', '1497769356', '-1', '3');
INSERT INTO `qn_ucenter_member` VALUES ('10', '123123sddf', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '', '0', '1498410345', '0', '1498410401', '0', '1498410345', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('11', '90908', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '', '0', '1498457259', '0', '1498457261', '0', '1498457259', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('12', 'vbxvc', '387673cc0365a0c662b2c56ea6c3acfd', '', '0', '', '0', '1498457355', '0', '1498457357', '0', '1498457355', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('13', 't234523', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '', '0', '1498457697', '0', '1498457699', '0', '1498457697', '1', '1');
INSERT INTO `qn_ucenter_member` VALUES ('14', 'dTJfzJfnzH', '3885baf07540a0d9a43f55a987a6b0aa', '123@123.com', '0', '', '0', '1498461502', '0', '1498464757', '0', '1498461502', '1', '2');
INSERT INTO `qn_ucenter_member` VALUES ('15', '0m3Afh2G0z', '3885baf07540a0d9a43f55a987a6b0aa', '', '0', '13112323222', '0', '1498464836', '0', '1498464838', '0', '1498464836', '1', '3');
