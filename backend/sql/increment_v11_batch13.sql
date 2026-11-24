-- 批次13 转赠（P080-P081）增量迁移
-- 数据来源：原站 /api/user/aboutGift {page,types} 真实响应（该账号 total=0）
--           原站 /api/user/ParamsToUser {phone} 真实响应（用户列表）
--           原站 /api/user_collection/user_collection/reGiftOrder 真实响应（密码错误）
-- 说明：原站该账号两个 Tab 均无转赠记录，故本表 0 条种子，如实还原空态，不臆造记录。

CREATE TABLE IF NOT EXISTS `ht_gift_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 我转赠的 2 我收到的',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '记录归属用户',
  `re_user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '对方用户id',
  `user_collection_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '藏品实例id',
  `collection_id` int(11) NOT NULL DEFAULT 0 COMMENT '藏品id',
  `collection_code` varchar(30) NOT NULL DEFAULT '' COMMENT '藏品编号',
  `user_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '接收人手机号(types=1)',
  `from_user_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '转赠人手机号(types=2)',
  `createtime` bigint(20) NOT NULL DEFAULT 0 COMMENT '转赠时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_types` (`user_id`,`types`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='转赠记录(批次13, 原站当前账号0条)';
