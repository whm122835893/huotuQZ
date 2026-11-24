-- 批次15 签到（P083）增量迁移
-- 数据来源：原站 api.hnyiqin.asia 真实响应（header token + version:1.0.1）
--   POST /addons/signin/api.index/index      -> code:1, msg:"", time:1794588752, data{...}
--   POST /addons/signin/api.index/monthSign  -> code:1, msg:"", data:{"04":"04","05":"05"}
--   POST /api/user/getUserInfo（批次1 已有）-> data.nickname / id
-- 说明：原站 chunk pages-sign-index.07b21d92.js（模块 9b1b 页面 / 5f3a 接口层）。
--       config_json 为原站 data 原样快照，控制器 json_decode 后原样返回，零臆造。
--       monthSign 原站实测对 month=2026-10 亦返回同一份 data，本项目如实复刻（忽略 month 差异）。

CREATE TABLE IF NOT EXISTS `ht_sign_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `snapshot_time` INT(11) NOT NULL DEFAULT 0 COMMENT '原站响应顶层 time（快照基准，避免浏览器时区漂移）',
  `config_json` TEXT NOT NULL COMMENT '原站 /addons/signin/api.index/index 的 data 原样 JSON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='签到配置快照(批次15, 原站真实响应1条)';

INSERT INTO `ht_sign_config` (`id`,`snapshot_time`,`config_json`)
SELECT 1, 1794588752, '{"signinscore":{"s1":"1","s2":"2","s3":"3","s4":"4","s5":"5","s6":"6","s7":"7","sn":"7"},"isfillup":"1","fillupscore":"100","fillupdays":"3","ranklist":[{"user_id":274743,"days":13,"user":{"id":274743,"nickname":"136****4412","avatar":"/defautAvatar.png","url":"/u/274743"}},{"user_id":194893,"days":12,"user":{"id":194893,"nickname":"江山","avatar":"/defautAvatar.png","url":"/u/194893"}},{"user_id":159454,"days":12,"user":{"id":159454,"nickname":"139****6462","avatar":"/defautAvatar.png","url":"/u/159454"}},{"user_id":310352,"days":12,"user":{"id":310352,"nickname":"176****4936","avatar":"/defautAvatar.png","url":"/u/310352"}},{"user_id":355228,"days":11,"user":{"id":355228,"nickname":"Firsw6Cph5m","avatar":"/defautAvatar.png","url":"/u/355228"}},{"user_id":208905,"days":11,"user":{"id":208905,"nickname":"156****3009","avatar":"/defautAvatar.png","url":"/u/208905"}},{"user_id":228718,"days":11,"user":{"id":228718,"nickname":"137****8115","avatar":"/defautAvatar.png","url":"/u/228718"}},{"user_id":201131,"days":11,"user":{"id":201131,"nickname":"139****3620","avatar":"/defautAvatar.png","url":"/u/201131"}},{"user_id":331715,"days":10,"user":{"id":331715,"nickname":"156****4573","avatar":"/defautAvatar.png","url":"/u/331715"}},{"user_id":174272,"days":10,"user":{"id":174272,"nickname":"134****3209","avatar":"/defautAvatar.png","url":"/u/174272"}}],"successions":0,"is_signin":false,"self_rank":0,"score":3,"msg":"今天签到可获得 1 积分,请点击签到领取积分"}'
WHERE NOT EXISTS (SELECT 1 FROM `ht_sign_config` WHERE `id` = 1);

CREATE TABLE IF NOT EXISTS `ht_sign_month` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `month` VARCHAR(7) NOT NULL DEFAULT '' COMMENT '月份 YYYY-MM',
  `days_json` TEXT NOT NULL COMMENT '原站 monthSign data 原样 JSON（已签日期 key/value 均为补零字符串）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='签到月份记录快照(批次15)';

INSERT INTO `ht_sign_month` (`month`,`days_json`)
SELECT '2026-11', '{"04":"04","05":"05"}'
WHERE NOT EXISTS (SELECT 1 FROM `ht_sign_month` WHERE `month` = '2026-11');

-- 原站 getUserInfo.id = 173634（页面「UID 173634」）；本地 ht_user.id = 1，
-- 增列保存原站真实 UID，User::getUserInfo() 因 auth() 取整行自动带出（零代码改动）。
ALTER TABLE `ht_user`
  ADD COLUMN IF NOT EXISTS `origin_uid` INT(11) NOT NULL DEFAULT 0
  COMMENT '原站 getUserInfo.id(签到页 UID 展示)' AFTER `id`;

UPDATE `ht_user` SET `origin_uid` = 173634 WHERE `username` = '17587881293';
