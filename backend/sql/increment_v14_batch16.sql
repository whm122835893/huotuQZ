-- 批次16 邀请域（P084-P085）增量迁移
-- 数据来源：原站 api.hnyiqin.asia 真实响应（header token + version:1.0.1）
--   POST /api/user/ranking {"types":2} -> code:1, msg:"查询成功", time:1794619570, data{...}
--   POST /api/user/invite   {"inviter_code":"INVALID_PROBE_CODE_XYZ"} -> code:0「活动未开启」（无效码安全探测，未建立真实邀请关系）
--   POST /api/user/getUserInfo（批次1 已有）-> data.inviter_code = HT173634
-- 说明：原站 chunk pages-invitation-invitation.243bf971.js / invitationrecord.2d8b2c65.js
--       data_json 为原站 ranking 的 data 原样快照，控制器 json_decode 后原样返回，零臆造。
--       inviter_code 本地 ht_user 已存在该列且主账号值为 HT173634，无需迁移。
--       getConfig(sharebg...) 由批次1 既有 Index::getConfig 处理，默认态不渲染海报，本批不改动。

CREATE TABLE IF NOT EXISTS `ht_invitation_ranking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `snapshot_time` INT(11) NOT NULL DEFAULT 0 COMMENT '原站 ranking 响应顶层 time',
  `data_json` TEXT NOT NULL COMMENT '原站 ranking 的 data 原样 JSON（data/my/activityRule）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='邀请好友列表快照(批次16, 原站真实响应1条)';

INSERT INTO `ht_invitation_ranking` (`id`,`snapshot_time`,`data_json`)
SELECT 1, 1794619570, '{"data":{"my_list":[],"vip_time_num":0,"real_name_num":0,"yx_num":0,"total_num":0,"buy_num":0},"my":"","activityRule":""}'
WHERE NOT EXISTS (SELECT 1 FROM `ht_invitation_ranking` WHERE `id` = 1);
