-- 批次14 钱包（P082）增量迁移
-- 数据来源：原站 api.hnyiqin.asia 真实响应（header token + version:1.0.1）
--   /api/user/getUserInfo                     -> data.huifu_account = 1
--   /api/wallet/huifu/wallet                  -> code:1「请求成功」+ data.info{...}
--   /api/wallet/huifu/openAccount             -> code:0「您已经开户了」data:null
--   /api/order/pay/CreateHuifuAccountOrder    -> code:0「您已经支付开户手续费，请勿重复操作」data:null
-- 说明：ht_user 增列后，User::getUserInfo() 因 auth() 取整行而自动带出 huifu_account，
--       已完成控制器零代码改动（硬规则第 6 条）。

ALTER TABLE `ht_user`
  ADD COLUMN IF NOT EXISTS `huifu_account` TINYINT(1) NOT NULL DEFAULT 0
  COMMENT '汇付钱包开户状态(原站 getUserInfo.huifu_account)' AFTER `huifu_service_charge`;

UPDATE `ht_user` SET `huifu_account` = 1 WHERE `username` = '17587881293';

CREATE TABLE IF NOT EXISTS `ht_wallet_huifu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '归属用户',
  `openstatus` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '开户状态',
  `sys_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '系统id',
  `extension` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '扩展字段',
  `acct_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '账户id',
  `resp_desc` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '响应描述',
  `resp_code` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '响应码',
  `mer_priv` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '商户私有字段',
  `user_cust_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '用户客户号',
  `redirect_url` VARCHAR(512) NOT NULL DEFAULT '' COMMENT '汇付钱包H5跳转地址(原站快照)',
  `mer_cust_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '商户客户号',
  `biz_product_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '业务产品id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='汇付钱包快照(批次14, 原站真实响应1条)';

-- 原站 /api/wallet/huifu/wallet 真实响应快照（uuid 为侦查当次值，仅作本地数据保存，不运行时调用）
INSERT INTO `ht_wallet_huifu`
  (`user_id`,`openstatus`,`sys_id`,`extension`,`acct_id`,`resp_desc`,`resp_code`,
   `mer_priv`,`user_cust_id`,`redirect_url`,`mer_cust_id`,`biz_product_id`)
SELECT 1, 1, '', '', '', '成功', 'C00000', '', '',
       'https://hfpay.cloudpnr.com/h5/?uuid=hfpwallet6666000243803635indexb361a9a3-5e7e-4711-a0db-449618b905e3',
       '6666000243803635', ''
WHERE NOT EXISTS (SELECT 1 FROM `ht_wallet_huifu` WHERE `user_id` = 1);
