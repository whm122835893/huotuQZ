-- ============================================================
-- 增量迁移 v6 · 批次8 订单域（P054-P064）
-- 侦查来源：原站 api.hnyiqin.asia 2026-11-11 实拍
--   /api/order/order/orderList（4 条真实订单）
--   /api/order/order/orderDetail/ids/1493003
--   /api/market/trade/begBuyOrderList、/api/market/Prepayment/myOrderlist（均空）
-- 用户本地 id=1（原站 user_id=173634，映射到本地账号 17587881293）
-- ============================================================

-- 1. 常规订单表（字段对齐原站 orderList/orderDetail 返回）
DROP TABLE IF EXISTS `ht_order`;
CREATE TABLE `ht_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `order_sn` varchar(64) NOT NULL DEFAULT '' COMMENT '订单编号',
  `order_type` tinyint(4) NOT NULL DEFAULT 3 COMMENT '3空投转入',
  `order_status` tinyint(4) NOT NULL DEFAULT 2 COMMENT '2进行中/已完成',
  `pay_type` tinyint(4) NOT NULL DEFAULT 1,
  `goods_id` int(11) NOT NULL DEFAULT 0,
  `goods_type` tinyint(4) NOT NULL DEFAULT 2,
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_cover_image` varchar(255) NOT NULL DEFAULT '',
  `series_id` int(11) NOT NULL DEFAULT 2,
  `series_name` varchar(120) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `uc_ids` varchar(64) NOT NULL DEFAULT '',
  `collection_code` varchar(255) NOT NULL DEFAULT '' COMMENT '资产编号JSON数组',
  `artel_order` varchar(32) NOT NULL DEFAULT 'order',
  `crystal_type` varchar(16) DEFAULT NULL,
  `crystal_number` varchar(16) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT 0,
  `updatetime` int(11) NOT NULL DEFAULT 0,
  `pay_time` int(11) DEFAULT NULL,
  `transaction_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_sn` (`order_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='常规订单';

-- 2. 我的求购订单表（原站当前为空数据）
DROP TABLE IF EXISTS `ht_beg_buy_order`;
CREATE TABLE `ht_beg_buy_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `order_sn` varchar(64) NOT NULL DEFAULT '',
  `goods_id` int(11) NOT NULL DEFAULT 0,
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_cover_image` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `num` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1求购中 2已成交 3已取消',
  `createtime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='我的求购订单';

-- 3. 我的委托订单表（原站当前为空数据）
DROP TABLE IF EXISTS `ht_entrust_order`;
CREATE TABLE `ht_entrust_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `order_sn` varchar(64) NOT NULL DEFAULT '',
  `goods_id` int(11) NOT NULL DEFAULT 0,
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_cover_image` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `num` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1委托中 2已成交 3已取消',
  `createtime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='我的委托订单';

-- 4. 种入原站 4 条真实订单（user_id 映射到本地 1）
INSERT INTO `ht_order` (`id`,`user_id`,`order_sn`,`order_type`,`order_status`,`goods_id`,`goods_name`,`goods_cover_image`,`series_name`,`num`,`total_price`,`uc_ids`,`collection_code`,`createtime`,`updatetime`) VALUES
(1493003, 1, '2026110718034310252571878789', 3, 2, 28, '2026兔耳',          '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', '火兔2026', 1, 0.00, '2643699', '["61247/85268"]',  1794045823, 1794045823),
(1484414, 1, '2026110717580710249554132268', 3, 2, 28, '2026兔耳',          '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', '火兔2026', 1, 0.00, '2598496', '["16044/85268"]',  1794045487, 1794045487),
(1476919, 1, '2026110521024553564952958189', 3, 2, 20, '2026登机牌',        '/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg', '火兔2026', 1, 0.00, '2243825', '["377825/688888"]', 1793883765, 1793883765),
(169114,  1, '2026110315393353555053848100', 3, 2, 3,  '2026潮酷兔空投卡二','/uploads/20261103/27a8d4c90fbc3dc59b26092839f0a680.jpg', '火兔2026', 1, 0.00, '104904',  '["93904/300000"]',  1793691573, 1793691573);
