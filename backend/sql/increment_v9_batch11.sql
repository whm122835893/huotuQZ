-- =============================================================
-- 批次11 盲盒（P072-P076）增量脚本
-- 数据来源：原站 api.hnyiqin.asia 真实快照（2026-11-12 侦查）
--   /api/box/blind_box/list → 2 个盲盒（msg「发售日历」）
--   /api/box/blind_box/openlog {blind_box_id,page,list_rows} → total=0 空分页壳
--   /api/user_collection/user_collection/openbox {openid} → code:0「没有啦」
--   /api/user_collection/user_collection/openBoxs {openid} → code:0「请勿重复提交」
--   /api/box/blind_box/BoxDetail/ids/33 → code:0「找不到该盲盒，或者已经下架了」
--   /api/user_collection/user_collection/detail/ids/2643699 → product.flux=0, collectionlist=null
-- 说明：原站该账号无持有盲盒、无开盒记录，故不臆造种子数据；结果页由 uni-app
--       H5 storage 契约驱动（blindBoxInfo / blindBoxResultList / blindBoxResult），不入库。
-- =============================================================

SET NAMES utf8mb4;

-- 1) 盲盒商品定义（原站 /api/box/blind_box/list 真实 2 条）
DROP TABLE IF EXISTS `ht_blind_box`;
CREATE TABLE `ht_blind_box` (
  `id`             INT(11)       NOT NULL,
  `name`           VARCHAR(120)  NOT NULL DEFAULT '',
  `listimg`        VARCHAR(255)  NOT NULL DEFAULT '',
  `price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_num`      INT(11)       NOT NULL DEFAULT 0 COMMENT '发行总量',
  `flux`           INT(11)       NOT NULL DEFAULT 0 COMMENT '流通量',
  `release_time`   BIGINT(20)    NOT NULL DEFAULT 0,
  `release_date_time_text` VARCHAR(32) NOT NULL DEFAULT '',
  `publisher_name` VARCHAR(120)  NOT NULL DEFAULT '火兔',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='盲盒商品定义（原站发售日历快照）';

INSERT INTO `ht_blind_box`
(`id`,`name`,`listimg`,`price`,`total_num`,`flux`,`release_time`,`release_date_time_text`) VALUES
(33, '2026泡泡探险', '/uploads/20261108/b15c345d56b5c3af601ae03834625014.jpg', 11.11, 200000, 0,     1794140280, '2026-11-08 20:18'),
(20, '2026登机牌',   '/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg', 3.98,  688888, 50888, 1793881080, '2026-11-05 20:18');

-- 2) 开盒记录（原站 total=0，仅建结构，字段来自 openRecord render 组件引用）
DROP TABLE IF EXISTS `ht_blind_box_openlog`;
CREATE TABLE `ht_blind_box_openlog` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `blind_box_id`    INT(11)      NOT NULL DEFAULT 0,
  `user_id`         INT(11)      NOT NULL DEFAULT 1,
  `nickname`        VARCHAR(64)  NOT NULL DEFAULT '',
  `collection_name` VARCHAR(120) NOT NULL DEFAULT '',
  `createtime`      INT(11)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_box_time` (`blind_box_id`,`createtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='开盒记录（原站该账号 0 条）';

-- 3) 用户持有盲盒（原站 antMycollection{types:1} 空态；供 openbox/openBoxs 判定，0 条种子）
DROP TABLE IF EXISTS `ht_user_blind_box`;
CREATE TABLE `ht_user_blind_box` (
  `id`              INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11) NOT NULL DEFAULT 1,
  `blind_box_id`    INT(11) NOT NULL DEFAULT 0,
  `collection_id`   INT(11) NOT NULL DEFAULT 0,
  `collection_code` INT(11) NOT NULL DEFAULT 0,
  `num`             INT(11) NOT NULL DEFAULT 0 COMMENT '可开启个数',
  PRIMARY KEY (`id`),
  KEY `idx_user_box` (`user_id`,`blind_box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户持有盲盒（原站该账号 0 条）';

-- 4) 藏品定义补「流通量」字段（原站 product.flux；detail/ids 快照 flux=0）
ALTER TABLE `ht_collection`
  ADD COLUMN IF NOT EXISTS `flux` INT(11) NOT NULL DEFAULT 0 COMMENT '流通量' AFTER `total_num`;
