-- ============================================================
-- 增量迁移 v7 · 批次9 我的资产（P065-P068）
-- 侦查来源：原站 api.hnyiqin.asia 2026-11-11 实拍
--   /api/user_collection/user_collection/antMycollection {types:2} → 3 个藏品（持有 4 份）
--   /api/user_collection/user_collection/antMycollection {types:1} → 盲盒空态 total=0
--   /api/user_collection/user_collection/getDetail {collection_id:28/20/3,types:2}
--   /api/user_collection/user_collection/detail/ids/2643699
--   /api/user_collection/user_collection/likeCollection {keyword:"2026"} → 3 条联想
-- 用户本地 id=1（原站 user_id=173634 映射到本地账号 17587881293）
-- ============================================================

-- 1. 藏品定义表（对齐原站 collection / product 子对象字段）
DROP TABLE IF EXISTS `ht_collection`;
CREATE TABLE `ht_collection` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL DEFAULT '',
  `main_image` varchar(255) NOT NULL DEFAULT '',
  `listimg` varchar(255) NOT NULL DEFAULT '',
  `total_num` int(11) NOT NULL DEFAULT 0 COMMENT '发行总量',
  `series_id` int(11) NOT NULL DEFAULT 2,
  `series_name` varchar(120) NOT NULL DEFAULT '',
  `publisher_name` varchar(120) NOT NULL DEFAULT '火兔' COMMENT '创作者',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weither_resale` tinyint(1) NOT NULL DEFAULT 1,
  `weither_regift` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='藏品定义';

INSERT INTO `ht_collection` (`id`,`name`,`main_image`,`listimg`,`total_num`,`series_id`,`series_name`,`publisher_name`) VALUES
(28, '2026兔耳',           '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', 85268,  2, '火兔2026', '火兔'),
(20, '2026登机牌',         '/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg', '/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg', 688888, 2, '火兔2026', '火兔'),
(3,  '2026潮酷兔空投卡二', '/uploads/20261103/27a8d4c90fbc3dc59b26092839f0a680.jpg', '/uploads/20261103/27a8d4c90fbc3dc59b26092839f0a680.jpg', 300000, 2, '火兔2026', '火兔');

-- 2. 用户持有汇总表（对齐原站 antMycollection.data[] 每项）
DROP TABLE IF EXISTS `ht_user_collection`;
CREATE TABLE `ht_user_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `collection_id` int(11) NOT NULL,
  `num` int(11) NOT NULL DEFAULT 1 COMMENT '持有份数',
  `sell_num` int(11) NOT NULL DEFAULT 0 COMMENT '寄售数量',
  `lock_num` int(11) NOT NULL DEFAULT 0,
  `is_collection` tinyint(1) NOT NULL DEFAULT 0,
  `can_compound` tinyint(1) NOT NULL DEFAULT 0,
  `utime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_type` (`user_id`,`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户持有藏品汇总';

INSERT INTO `ht_user_collection` (`user_id`,`collection_id`,`num`,`sell_num`,`utime`) VALUES
(1, 28, 2, 0, 1794045823),
(1, 20, 1, 0, 1793883765),
(1, 3,  1, 0, 1793691573);

-- 3. 用户藏品实例表（每个编号 = 一份；对齐 getDetail.list[] 与 detail/ids/{} 返回）
DROP TABLE IF EXISTS `ht_user_collection_item`;
CREATE TABLE `ht_user_collection_item` (
  `id` int(11) NOT NULL COMMENT '用户藏品实例ID（原站真实主键）',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `collection_id` int(11) NOT NULL,
  `collection_code` int(11) NOT NULL DEFAULT 0 COMMENT '编号 #61247',
  `rz_code` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `types` tinyint(4) NOT NULL DEFAULT 2,
  `source_type` tinyint(4) NOT NULL DEFAULT 5,
  `from_price` decimal(10,2) DEFAULT NULL COMMENT '购入价，空态显示 --',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT '寄售价，空态显示 --',
  `order_id` int(11) NOT NULL DEFAULT 0,
  `user_hash` varchar(64) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT 0,
  `updatetime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_coll` (`user_id`,`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户藏品实例(编号)';

-- 4 份真实实例：兔耳 2 份 + 登机牌 1 份 + 潮酷兔 1 份
INSERT INTO `ht_user_collection_item`
(`id`,`user_id`,`collection_id`,`collection_code`,`rz_code`,`status`,`types`,`source_type`,`from_price`,`createtime`,`updatetime`,`user_hash`) VALUES
(2643699, 1, 28, 61247, '00061247', 1, 2, 5, NULL, 1794045823, 1794045823, '0Jn5Go0M3Cm1Kl0Y'),
(2598496, 1, 28, 16044, '00016044', 1, 2, 5, NULL, 1794045487, 1794045487, ''),
(2243825, 1, 20, 377825,'000377825',1, 2, 5, NULL, 1793883765, 1793883765, ''),
(104904,  1, 3,  93904, '00093904', 1, 2, 5, NULL, 1793691573, 1793691573, '');
