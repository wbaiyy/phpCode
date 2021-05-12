CREATE TABLE `audit_message` (
  `message_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息主键ID',
  `site_code` char(10) NOT NULL COMMENT '所属站点',
  `rule_id` bigint(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核模板规则ID主键',
  `audit_sort` tinyint(1) NOT NULL COMMENT '审核类型优先级',
  `audit_mark` varchar(20) NOT NULL COMMENT '审核模板类型简码（唯一)',
  `audit_name` varchar(100) NOT NULL COMMENT '审核名称',
  `audit_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '审核说明',
  `business_data` text COMMENT '业务数据，数据格式JSON，如价格审核为PriceBusinessData序列化的JSON',
  `business_uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '暂存业务数据ID(全局唯一)',
  `audit_status` tinyint(2) NOT NULL DEFAULT '21' COMMENT '10：操作人撤销,20：规则引擎校验，自动通过,21：规则引擎校验，自动拒绝,22：规则全不匹配，自动通过,30：人工审核中,31：人工审核通过,32：人工审核驳回',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息提交时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_user` varchar(64) NOT NULL DEFAULT '' COMMENT '提交人',
  `update_user` varchar(64) NOT NULL DEFAULT '' COMMENT '更新人',
  `workflow_id` bigint(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息工作流ID',
  `template_id` bigint(10) unsigned NOT NULL DEFAULT '0' COMMENT '模板ID',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '所属模块',
  `message_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '消息备注',
  `wh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录修改时间',
  PRIMARY KEY (`message_id`),
  KEY `site_code` (`site_code`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `template_id` (`template_id`) USING BTREE,
  KEY `audit_mark` (`audit_mark`) USING BTREE,
  KEY `idx_audit_name` (`audit_name`(20)) USING BTREE,
  KEY `idx_create_time` (`create_time`) USING BTREE,
  KEY `idx_audit_status` (`audit_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=996198 DEFAULT CHARSET=utf8 COMMENT='审核消息';



CREATE TABLE `audit_record` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message_id` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息ID',
  `workflow_id` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '工作流ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核人USERID',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '审核人名',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '2' COMMENT '处理状态，1通过，2驳回',
  `audit_explain` varchar(255) NOT NULL DEFAULT '' COMMENT '审核说明',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '节点顺序,数字越小，优先级越高',
  `wh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录修改时间',
  PRIMARY KEY (`id`),
  KEY `inx_workflow_id` (`workflow_id`) USING BTREE,
  KEY `inx_message_id` (`message_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8 COMMENT='审核记录';



CREATE TABLE `audit_rule` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `process_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '规则处理方式，1通过，2驳回，3人工审核',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '排序，值越小优先级越高',
  `items_relation` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '对比方式: 1与，2或',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '规则描述',
  `template_id` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属模板',
  `workflow_id` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '工作流ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `create_user` varchar(64) NOT NULL DEFAULT '' COMMENT '创建人',
  `update_user` varchar(64) NOT NULL DEFAULT '' COMMENT '最后更新人',
  `base_profit_margin` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '基准利润率（用于计算亏损金额）',
  `wh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inx_title` (`title`,`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='审核规则';



CREATE TABLE `audit_template` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `audit_mark` varchar(32) NOT NULL DEFAULT '' COMMENT '模板唯一标识',
  `title` varchar(40) NOT NULL DEFAULT '' COMMENT '标题',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `belong_site` varchar(64) NOT NULL DEFAULT '' COMMENT '所属站点，多选用逗号隔开',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '优先级:1-A,2-B,3-C,4-D',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `create_user` varchar(64) NOT NULL DEFAULT '' COMMENT '创建人',
  `update_user` varchar(64) NOT NULL DEFAULT '' COMMENT '最后修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inx_type` (`audit_mark`) USING BTREE,
  KEY `inx_title_create_update_user` (`title`,`create_user`,`update_user`),
  KEY `unq_title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='审核模板';



CREATE TABLE `audit_workflow` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键|王彬|2018-7-19',
  `audit_type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '工作流审核类型,1逐级审核|王彬|2018-7-19',
  `title` varchar(40) NOT NULL DEFAULT '' COMMENT '工作流名称|王彬|2018-7-19',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '工作流描述|王彬|2018-7-19',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间|王彬|2018-7-19',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近修改人|王彬|2018-7-19',
  `create_user` varchar(128) NOT NULL DEFAULT '' COMMENT '创建人|王彬|2018-7-19',
  `update_user` varchar(128) NOT NULL DEFAULT '' COMMENT '最后更新人|王彬|2018-7-19',
  `wh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='审核工作流';


CREATE TABLE `audit_workflow_item` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键|王彬|2018-7-19',
  `workflow_id` bigint(10) unsigned NOT NULL DEFAULT '0' COMMENT '工作流模板ID|王彬|2018-7-19',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '处理人ID|王彬|2018-7-19',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '审核人名',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '处理排序,数字越小，优先级越高|王彬|2018-7-19',
  `wh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_worker` (`workflow_id`,`user_id`,`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8 COMMENT='审核人';




INSERT INTO `audit_rule` VALUES (4, '000', 1, 1, 2, '圣诞歌丰盛的', 18, 0, 1537839351, 1537839351, 'zhangwei', '', 0.000, '2020-02-07 11:24:36');
INSERT INTO `audit_rule` VALUES (20, '系统通过', 1, 1, 2, '系统通过', 8, 0, 1571472520, 1585553709, 'zhouhui', 'zhangwei', 0.000, '2020-03-30 15:35:08');
INSERT INTO `audit_rule_item` VALUES (8, 'obs', 1, 'changeType', '=', '1', 4, '2020-02-06 11:20:38');
INSERT INTO `audit_rule_item` VALUES (49, 'obs', 1, 'changeType', '=', '2', 4, '2020-02-06 11:20:38');
INSERT INTO `audit_rule_item` VALUES (260, 'obs', 1, 'changeType', '=', '1', 20, '2020-02-06 11:20:38');
INSERT INTO `audit_rule_item` VALUES (261, 'obs', 1, 'changeType', '=', '2', 20, '2020-02-06 11:20:38');











