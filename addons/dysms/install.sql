DROP TABLE IF EXISTS `qn_dysms_tpl`;
CREATE TABLE `qn_dysms_tpl` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '类别（1：验证码，2：通知通知）',
  `action` char(80) NOT NULL DEFAULT '' COMMENT '行为名称，如:reg,changemobile',
  `sign` char(20) NOT NULL DEFAULT '' COMMENT '短信签名',
  `template_code` char(20) NOT NULL DEFAULT '' COMMENT '模板ID',
  `template_content` varchar(140) NOT NULL DEFAULT '' COMMENT '模板内容',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `action` (`action`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='模板配置表';



INSERT INTO `qn_dysms_tpl` (`id`, `type`, `action`, `sign`, `template_code`, `template_content`, `status`, `create_time`) VALUES
(1, 1, 'register', '靑年PHP', 'SMS_133276017', '感谢您的注册，您的验证码为：${code}，验证码5分钟内有效，请勿泄漏给他人。', 1, 1525411158),
(2, 1, 'bindmobile', '靑年PHP', 'SMS_133266164', '您的验证码为：${code}，验证码5分钟内有效，请勿泄漏给他人', 1, 1532259837),
(3, 1, 'unbindmobile', '靑年PHP', 'SMS_133266164', '您的验证码为：${code}，验证码5分钟内有效，请勿泄漏给他人', 1, 1532259865),
(4, 1, 'cash', '靑年PHP', 'SMS_133266164', '您的验证码为：${code}，验证码5分钟内有效，请勿泄漏给他人。', 1, 1532259893);