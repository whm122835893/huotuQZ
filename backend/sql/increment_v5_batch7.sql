-- ============================================================
-- 批次7 增量迁移 · 水晶（龙晶）商城域 P047-P053
-- 侦查日期：2026-11-09
-- 原站当前为未开放状态：list / crystalDetail / exchangeRecord 均 total=0，
-- 本项目保持同构空表，不臆造业务数据。
-- 注意：本文件所有可能与 MySQL 保留字冲突的列名一律加反引号。
-- ============================================================

-- 1. 龙晶商城商品
CREATE TABLE IF NOT EXISTS `ht_crystal_goods` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_name` VARCHAR(120) NOT NULL DEFAULT '' COMMENT '商品名',
  `type` TINYINT NOT NULL DEFAULT 10 COMMENT '10数字藏品 20盲盒 30实物',
  `crystal_type` VARCHAR(16) NOT NULL DEFAULT 'yellow' COMMENT 'yellow黄/purple紫/blue蓝',
  `crystal_number` INT NOT NULL DEFAULT 0 COMMENT '所需龙晶数量',
  `limited` INT NOT NULL DEFAULT 0 COMMENT '限量份数',
  `stock` INT NOT NULL DEFAULT 0 COMMENT '剩余库存',
  `exchange_status` TINYINT NOT NULL DEFAULT 0 COMMENT '0敬请期待 1倒计时 2兑换中 4已兑完 5水晶不足',
  `list_image` VARCHAR(255) NOT NULL DEFAULT '',
  `main_image` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(500) NOT NULL DEFAULT '',
  `content` TEXT NULL COMMENT '商品详情富文本',
  `batch_id` INT NOT NULL DEFAULT 0,
  `collection_id` INT NOT NULL DEFAULT 0,
  `blind_box_id` INT NOT NULL DEFAULT 0,
  `sort` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  `createtime` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_crystal_type` (`crystal_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='龙晶商城商品';

-- 2. 龙晶明细（获取/消耗流水）
CREATE TABLE IF NOT EXISTS `ht_crystal_detail` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL DEFAULT 0,
  `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1获取 2消耗',
  `type_text` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '龙晶获取/龙晶消耗',
  `crystal_number` INT NOT NULL DEFAULT 0 COMMENT '带正负号',
  `memo` VARCHAR(120) NOT NULL DEFAULT '' COMMENT '事项（签到奖励/兑换商品）',
  `createtime` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='龙晶明细';

-- 3. 兑换记录
CREATE TABLE IF NOT EXISTS `ht_crystal_exchange_record` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL DEFAULT 0,
  `goods_id` INT NOT NULL DEFAULT 0,
  `goods_name` VARCHAR(120) NOT NULL DEFAULT '',
  `list_image` VARCHAR(255) NOT NULL DEFAULT '',
  `crystal_number` INT NOT NULL DEFAULT 0,
  `num` INT NOT NULL DEFAULT 1,
  `status` TINYINT NOT NULL DEFAULT 1,
  `createtime` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='龙晶兑换记录';

-- 4. 用户龙晶余额（原站 getUserInfo 字段：crystal_yellow / crystal_purple / crystal_blue，当前均为 0）
ALTER TABLE `ht_user`
  ADD COLUMN IF NOT EXISTS `crystal_yellow` INT NOT NULL DEFAULT 0 COMMENT '黄水晶',
  ADD COLUMN IF NOT EXISTS `crystal_purple` INT NOT NULL DEFAULT 0 COMMENT '紫水晶',
  ADD COLUMN IF NOT EXISTS `crystal_blue`   INT NOT NULL DEFAULT 0 COMMENT '蓝水晶';
