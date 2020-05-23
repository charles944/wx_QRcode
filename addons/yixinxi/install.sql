DROP TABLE IF EXISTS `qn_yixinxi_tpl`;
CREATE TABLE `qn_yixinxi_tpl` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action` char(80) NOT NULL DEFAULT '' COMMENT '行为名称，如:reg,changemobile',
  `template_content` varchar(140) NOT NULL DEFAULT '' COMMENT '模板内容',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `action` (`action`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='模板配置表';



INSERT INTO `qn_yixinxi_tpl` (`id`, `action`, `template_content`, `status`, `create_time`) VALUES
(1, 'register', '感谢您的注册，您的验证码为：{code}，验证码5分钟内有效，请勿泄漏给他人。', 1, 1525411158),
(2, 'bindmobile', '您的验证码为：{code}，验证码5分钟内有效，请勿泄漏给他人', 1, 1532259837),
(3, 'unbindmobile', '您的验证码为：{code}，验证码5分钟内有效，请勿泄漏给他人', 1, 1532259865),
(4, 'cash', '您的验证码为：{code}，验证码5分钟内有效，请勿泄漏给他人。', 1, 1532259893);