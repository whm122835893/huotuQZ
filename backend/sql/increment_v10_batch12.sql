-- 批次12 商品详情（P077-P079）增量迁移
-- 数据来源：原站 /api/box/collection/ProductDetail/ids/33?batch_id=4 与 /ids/20?batch_id=3 真实快照
--           原站 /api/index/afficheList {search:2026登机牌,page:1} 返回 8 条相关公告真实快照

CREATE TABLE IF NOT EXISTS `ht_product` (
  `id` int(11) NOT NULL COMMENT '商品id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1盲盒 2藏品',
  `main_image` varchar(255) NOT NULL DEFAULT '' COMMENT '主图',
  `listimg` varchar(255) NOT NULL DEFAULT '' COMMENT '列表图',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '发售价',
  `total_num` int(11) NOT NULL DEFAULT 0 COMMENT '发行量',
  `flux` int(11) NOT NULL DEFAULT 0 COMMENT '流通量',
  `batch_id` int(11) NOT NULL DEFAULT 0 COMMENT '批次id',
  `publisher_name` varchar(50) NOT NULL DEFAULT '' COMMENT '发行方',
  `publisher_logo` varchar(255) NOT NULL DEFAULT '',
  `sale_status_text` varchar(20) NOT NULL DEFAULT '' COMMENT '销售状态',
  `soldout` tinyint(1) NOT NULL DEFAULT 0,
  `release_time` bigint(20) NOT NULL DEFAULT 0,
  `release_time_date_text` varchar(30) NOT NULL DEFAULT '',
  `payload` mediumtext DEFAULT NULL COMMENT '原站完整详情快照JSON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='商品详情快照(批次12)';

INSERT INTO `ht_product` (`id`,`name`,`goods_type`,`main_image`,`listimg`,`price`,`total_num`,`flux`,`batch_id`,`publisher_name`,`publisher_logo`,`sale_status_text`,`soldout`,`release_time`,`release_time_date_text`,`payload`) VALUES (33,'2026泡泡探险',2,'/uploads/20261108/b15c345d56b5c3af601ae03834625014.jpg','/uploads/20261108/b15c345d56b5c3af601ae03834625014.jpg','11.11',200000,0,4,'火兔','/uploads/20240118/17b679f0562f639f96344f08e59e0729.jpg','3',0,1794115500,'2026-11-08 20:18:00','{"id":33,"issuer_id":0,"flux":0,"face_status":0,"series_id":2,"name":"2026泡泡探险","introduce_image":"","main_image":"/uploads/20261108/b15c345d56b5c3af601ae03834625014.jpg","total_num":200000,"total_num_show":0,"price":"11.11","sale_total_num":"","release_time":1794115500,"ant_chain":null,"purchasing_num":200,"buyneed":null,"listimg":"/uploads/20261108/b15c345d56b5c3af601ae03834625014.jpg","soldout":0,"3d_url_type":"img","3d_url":"","purchase_information":"                            ","creator_id":0,"hasStock":1,"sale_status_text":3,"release_time_text":0,"release_time_date_text":"2026-11-08 20:18:00","imageinfo":{"imagewidth":"200","imageheight":"200"},"is_like":0,"is_sub":false,"buy_num":0,"publisher":{"name":"火兔","company_logo":"/uploads/20240118/17b679f0562f639f96344f08e59e0729.jpg","user":[]},"issuer":null,"series":{"id":2,"name":"火兔2026","pattern":1,"resale_put_start_time":"22:56:46","resale_put_end_time":"22:56:46","resale_buy_start_time":"22:56:46","resale_buy_end_time":"22:56:46"},"batch":{"id":4,"collection_id":33,"start_time":1794140280,"end_time":1794140280,"num_list":"","purchase_quantity":0,"total_num":200000,"zuzao_id":0,"sale_total_num":0,"front_display":1,"fictitious_stock":0,"createtime":1794115778,"updatetime":1794140615,"open_draw":0,"sign_up_start_time":null,"sign_up_end_time":null,"draw_virtual_num":0,"draw_time":0,"draw_user_num":0,"sign_up_user_num":0,"riority_status":0,"riority_time":1794115680,"weigh":4,"title":"","has_num":1}}') ON DUPLICATE KEY UPDATE `payload`=VALUES(`payload`),`batch_id`=VALUES(`batch_id`);
INSERT INTO `ht_product` (`id`,`name`,`goods_type`,`main_image`,`listimg`,`price`,`total_num`,`flux`,`batch_id`,`publisher_name`,`publisher_logo`,`sale_status_text`,`soldout`,`release_time`,`release_time_date_text`,`payload`) VALUES (20,'2026登机牌',2,'/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg','/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg','3.98',688888,0,3,'火兔','/uploads/20240118/17b679f0562f639f96344f08e59e0729.jpg','3',0,1793702160,'2026-11-05 20:18:00','{"id":20,"issuer_id":0,"flux":0,"face_status":0,"series_id":2,"name":"2026登机牌","introduce_image":"","main_image":"/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg","total_num":688888,"total_num_show":0,"price":"3.98","sale_total_num":"","release_time":1793702160,"ant_chain":null,"purchasing_num":1000,"buyneed":null,"listimg":"/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg","soldout":0,"3d_url_type":"img","3d_url":"/uploads/20261103/88748beb27fa6100fa3346938cb8edd0.jpg","purchase_information":"                            ","creator_id":0,"hasStock":1,"sale_status_text":3,"release_time_text":0,"release_time_date_text":"2026-11-05 20:18:00","imageinfo":{"imagewidth":"200","imageheight":"200"},"is_like":0,"is_sub":false,"buy_num":0,"publisher":{"name":"火兔","company_logo":"/uploads/20240118/17b679f0562f639f96344f08e59e0729.jpg","user":[]},"issuer":null,"series":{"id":2,"name":"火兔2026","pattern":1,"resale_put_start_time":"22:56:46","resale_put_end_time":"22:56:46","resale_buy_start_time":"22:56:46","resale_buy_end_time":"22:56:46"},"batch":{"id":3,"collection_id":20,"start_time":1793881080,"end_time":1793881080,"num_list":"","purchase_quantity":0,"total_num":688888,"zuzao_id":0,"sale_total_num":0,"front_display":1,"fictitious_stock":0,"createtime":1793715915,"updatetime":1793881314,"open_draw":0,"sign_up_start_time":null,"sign_up_end_time":null,"draw_virtual_num":0,"draw_time":0,"draw_user_num":0,"sign_up_user_num":0,"riority_status":0,"riority_time":1793715780,"weigh":3,"title":"","has_num":1}}') ON DUPLICATE KEY UPDATE `payload`=VALUES(`payload`),`batch_id`=VALUES(`batch_id`);

CREATE TABLE IF NOT EXISTS `ht_product_notice` (
  `id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '关联搜索词(商品名)',
  `title` varchar(300) NOT NULL DEFAULT '' COMMENT '公告标题',
  `affiche_type` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '1',
  `url` varchar(255) NOT NULL DEFAULT '',
  `createtime` bigint(20) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `idx_keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='商品相关公告快照(批次12)';

INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (21,'2026登机牌','活动 | 持票登临豪华舱，乘风直上破穹苍。 《2026豪华舱》合成活动今日开启！','活动','1','',1794061391,1) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (19,'2026登机牌','活动 | 星开万里征途，黛藏凌云志，栗起震八方。《星黛栗》合成&抢合活动来袭！','活动','1','',1794039010,2) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (17,'2026登机牌','活动 | 《一飞冲天》限量分解活动来袭！','活动','1','',1794025763,3) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (15,'2026登机牌','活动 | 《大吉大利》限量合成活动开启！','活动','1','',1793964074,4) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (13,'2026登机牌','活动 | 《天官赐福》资格合成活动今日开启！','官方','1','',1793940337,5) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (11,'2026登机牌','火兔公告｜《2026登机牌》开启委托','官方','1','',1793888026,6) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (10,'2026登机牌','火兔公告 | 《熊小七》限量合成活动今日开启','活动','1','',1793887396,7) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
INSERT INTO `ht_product_notice` (`id`,`keyword`,`title`,`affiche_type`,`type`,`url`,`createtime`,`sort`) VALUES (9,'2026登机牌','火兔公告｜《2026登机牌》开启寄售','寄售','1','',1793884372,8) ON DUPLICATE KEY UPDATE `keyword`=VALUES(`keyword`),`createtime`=VALUES(`createtime`),`sort`=VALUES(`sort`);
