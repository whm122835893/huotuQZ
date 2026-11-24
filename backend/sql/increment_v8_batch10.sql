-- =============================================================
-- 批次10 寄售大厅（P069-P071）增量脚本
-- 数据来源：原站 api.hnyiqin.asia 真实快照（2026-11-11 侦查）
--   /api/user_collection/user_collection/detail/ids/{2643699,2243825,104904}
--   /api/user_collection/user_collection/show_resale_money
--   /api/order/order/myConsignment {status,goods_type,page} / {keyword}
-- 说明：原站该账号寄售订单 total=0，故 ht_consignment 无种子数据，
--       仅建表结构（字段来自原站前端 myConsignmentsItem 组件引用，如实记录）。
-- =============================================================

SET NAMES utf8mb4;

-- 1) 藏品定义补齐「寄售价格限制/费率」字段（原站 product 同名字段，真实值）
ALTER TABLE `ht_collection`
  ADD COLUMN `reprice_limit`        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '寄售价格上限' AFTER `price`,
  ADD COLUMN `reprice_min_limit`    DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '寄售价格下限' AFTER `reprice_limit`,
  ADD COLUMN `platform_resale_rate` INT(11)       NOT NULL DEFAULT 6    COMMENT '平台寄售服务费率(%)' AFTER `reprice_min_limit`,
  ADD COLUMN `price_ratio`          INT(11)       NOT NULL DEFAULT 90   COMMENT '最低限价比例(昨日均价%)' AFTER `platform_resale_rate`,
  ADD COLUMN `price_pro`            TINYINT(1)    NOT NULL DEFAULT 1    COMMENT '价格保护开关' AFTER `price_ratio`;

-- 原站真实 product 快照（3 个藏品）
UPDATE `ht_collection` SET
  `reprice_limit` = 5.00, `reprice_min_limit` = 2.00, `platform_resale_rate` = 6, `price_ratio` = 90, `price_pro` = 1
  WHERE `id` = 28;  -- 2026兔耳
UPDATE `ht_collection` SET
  `reprice_limit` = 5.00, `reprice_min_limit` = 1.00, `platform_resale_rate` = 6, `price_ratio` = 90, `price_pro` = 1
  WHERE `id` = 20;  -- 2026登机牌
UPDATE `ht_collection` SET
  `reprice_limit` = 0.00, `reprice_min_limit` = 0.00, `platform_resale_rate` = 5, `price_ratio` = 90, `price_pro` = 1
  WHERE `id` = 3;   -- 2026潮酷兔空投卡二

-- 2) 寄售金额配置（show_resale_money 的真实返回快照，price=100 时的基准）
--    原站返回：minprice=4, price_pro=1, price_price_ratio=90,
--    pay_platform_service_rate=0.06, share_proportion_rate=0.00,
--    platform_service_price=6.00, share_proportion_price=0.00, real_price=94.00,
--    low_price=5, low_price_text=五元
DROP TABLE IF EXISTS `ht_consignment_config`;
CREATE TABLE `ht_consignment_config` (
  `id`                       INT(11)       NOT NULL AUTO_INCREMENT,
  `minprice`                 DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '最低限价',
  `price_pro`                TINYINT(1)    NOT NULL DEFAULT 1    COMMENT '价格保护开关',
  `price_price_ratio`        INT(11)       NOT NULL DEFAULT 90   COMMENT '最低限价比例(%)',
  `pay_platform_service_rate` DECIMAL(6,4) NOT NULL DEFAULT 0.0600 COMMENT '支付平台服务费率',
  `share_proportion_rate`    DECIMAL(6,4)  NOT NULL DEFAULT 0.0000 COMMENT '版权分成费率',
  `low_price`                DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '低价预警阈值',
  `low_price_text`           VARCHAR(32)   NOT NULL DEFAULT ''    COMMENT '低价预警文案',
  `tips`                     VARCHAR(512)  NOT NULL DEFAULT ''    COMMENT '限价规则说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='寄售金额配置（原站 show_resale_money 快照）';

INSERT INTO `ht_consignment_config`
  (`minprice`,`price_pro`,`price_price_ratio`,`pay_platform_service_rate`,`share_proportion_rate`,`low_price`,`low_price_text`,`tips`)
VALUES
  (4.00, 1, 90, 0.0600, 0.0000, 5.00, '五元',
   '为保护寄售市场整体用户权益，寄售出价下限为昨日平均成交价90%，若低于最低限价或高于最高限价藏品将无法寄售，敬请知悉！');

-- 3) 我的寄售订单表（原站该账号 total=0，仅建结构，字段来自前端组件引用）
--    myConsignmentsItem 用到：item.goods_cover_image / goods_name / price / status /
--    id / user_collection.collection_code / user_collection.total_num
DROP TABLE IF EXISTS `ht_consignment`;
CREATE TABLE `ht_consignment` (
  `id`                INT(11)       NOT NULL AUTO_INCREMENT,
  `user_id`           INT(11)       NOT NULL DEFAULT 0   COMMENT '所属用户',
  `user_collection_id` INT(11)      NOT NULL DEFAULT 0   COMMENT '用户藏品实例id',
  `goods_name`        VARCHAR(120)  NOT NULL DEFAULT ''  COMMENT '藏品名称',
  `goods_cover_image` VARCHAR(255)  NOT NULL DEFAULT ''  COMMENT '藏品封面',
  `goods_type`        TINYINT(1)    NOT NULL DEFAULT 2   COMMENT '2数字藏品 / 1盲盒',
  `price`             DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '寄售价格',
  `status`            TINYINT(1)    NOT NULL DEFAULT 1   COMMENT '1寄售中 / 2已售出',
  `collection_code`   VARCHAR(32)   NOT NULL DEFAULT ''  COMMENT '藏品编号',
  `total_num`         INT(11)       NOT NULL DEFAULT 0   COMMENT '发行量',
  `createtime`        INT(11)       NOT NULL DEFAULT 0,
  `updatetime`        INT(11)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`,`status`,`goods_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='我的寄售订单（原站该账号为空，无种子数据）';
