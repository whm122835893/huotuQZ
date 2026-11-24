-- 火兔复刻项目 数据库初始化（对齐 docs/03-数据库设计文档.md v1）
-- 引擎 InnoDB / utf8mb4

CREATE TABLE IF NOT EXISTS `ht_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '账号(手机号)',
  `nickname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '昵称(脱敏)',
  `mobile` VARCHAR(20) NOT NULL DEFAULT '',
  `email` VARCHAR(100) NOT NULL DEFAULT '',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '/defautAvatar.png',
  `password` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'bcrypt',
  `salt` VARCHAR(30) NOT NULL DEFAULT '',
  `pay_password` VARCHAR(255) NOT NULL DEFAULT '',
  `money` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `score` INT NOT NULL DEFAULT 0,
  `level` TINYINT NOT NULL DEFAULT 1,
  `gender` TINYINT NOT NULL DEFAULT 1,
  `birthday` DATE NULL,
  `bio` VARCHAR(255) NOT NULL DEFAULT '',
  `group_id` INT NOT NULL DEFAULT 0,
  `inviter_user_id` BIGINT NOT NULL DEFAULT 0,
  `inviter_code` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '邀请码 HT+id',
  `is_realname` TINYINT NOT NULL DEFAULT 0,
  `transfer_permission` TINYINT NOT NULL DEFAULT 0,
  `huifu_service_charge` TINYINT NOT NULL DEFAULT 1,
  `sandpay_status` TINYINT NOT NULL DEFAULT 0,
  `yibao_status` TINYINT NOT NULL DEFAULT 0,
  `is_show_rank` TINYINT NOT NULL DEFAULT 1,
  `buy_num` INT NOT NULL DEFAULT 0,
  `status` ENUM('normal','disable') NOT NULL DEFAULT 'normal',
  `createtime` BIGINT NOT NULL DEFAULT 0,
  `logintime` BIGINT NOT NULL DEFAULT 0,
  `prevtime` BIGINT NOT NULL DEFAULT 0,
  `jointime` BIGINT NOT NULL DEFAULT 0 COMMENT '注册时间',
  `loginip` VARCHAR(50) NOT NULL DEFAULT '',
  `joinip` VARCHAR(50) NOT NULL DEFAULT '',
  `loginfailure` INT NOT NULL DEFAULT 0,
  `successions` INT NOT NULL DEFAULT 1,
  `maxsuccessions` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_mobile` (`mobile`),
  UNIQUE KEY `uk_inviter_code` (`inviter_code`),
  KEY `idx_inviter` (`inviter_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户';

CREATE TABLE IF NOT EXISTS `ht_user_token` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL,
  `token` CHAR(36) NOT NULL COMMENT 'uuid',
  `expiretime` BIGINT NOT NULL DEFAULT 0,
  `createtime` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录态';

CREATE TABLE IF NOT EXISTS `ht_config` (
  `name` VARCHAR(50) NOT NULL,
  `value` TEXT NULL,
  `group` VARCHAR(30) NOT NULL DEFAULT 'basic',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';

-- 配置基线（对齐原站 getConfig 可观测字段）
INSERT IGNORE INTO `ht_config` (`name`,`value`,`group`) VALUES
('appName','火兔','basic'),
('pay_time_limit','300','basic'),
('default_pay_type','huifu','basic'),
('resell_collection','1','basic'),
('transfer_collection','1','basic'),
('isAgreement','1','basic'),
('pay_audit','0','basic'),
('versionCode','1.0.0','basic');
