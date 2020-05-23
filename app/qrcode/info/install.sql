DROP TABLE IF EXISTS `qn_qrcode`;
CREATE TABLE IF NOT EXISTS `qn_qrcode` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL COMMENT '二维码标题',
  `max_scan` bigint(11) NOT NULL DEFAULT '10000' COMMENT '最大扫描次数',
  `view_mode` int(11) NOT NULL DEFAULT '1' COMMENT '显示模式',
  `sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
  `status` tinyint(2) NOT NULL COMMENT '状态',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `scan_count` bigint(11) NOT NULL DEFAULT '0' COMMENT '总扫描量',
  `create_time` bigint(11) NOT NULL,
  `update_time` bigint(11) NOT NULL,
  `type_mode` int(11) NOT NULL DEFAULT '1' COMMENT '活码类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='二维码列表' AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `qn_qrcode_child`;
CREATE TABLE IF NOT EXISTS `qn_qrcode_child` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `qr_pid` int(11) NOT NULL,
  `qr_img` bigint(11) NOT NULL DEFAULT '0' COMMENT '图片',
  `qr_link` varchar(255) DEFAULT NULL COMMENT '链接',
  `qr_text` text COMMENT '文本',
  `qr_card` text COMMENT '名片',
  `qr_type` int(11) NOT NULL DEFAULT '1',
  `scan_count` bigint(11) NOT NULL DEFAULT '0' COMMENT '总扫描量',
  `sort` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` bigint(11) NOT NULL,
  `update_time` bigint(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `qr_province` varchar(200) DEFAULT NULL,
  `qr_city` varchar(200) DEFAULT NULL,
  `qr_district` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='二维码子分类' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `qn_qrcode_domain`;
CREATE TABLE IF NOT EXISTS `qn_qrcode_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `in_domain` varchar(255) NOT NULL,
  `out_domain` text NOT NULL,
  `creat_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `qn_qrcode_log`;
CREATE TABLE IF NOT EXISTS `qn_qrcode_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `create_time` bigint(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP',
  `ip_addr` varchar(255) DEFAULT NULL COMMENT 'IP归属地',
  `scan_device` text COMMENT '扫描设备',
  `qr_id` bigint(11) NOT NULL COMMENT '扫描二维码ID',
  `qr_cid` bigint(11) NOT NULL COMMENT '扫描子二维码ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='扫描记录' AUTO_INCREMENT=1 ;