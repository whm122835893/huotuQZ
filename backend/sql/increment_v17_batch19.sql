-- ============================================================================
-- 批次19 抽奖域（P093 中奖记录 / P094 活动规则）增量迁移
-- 日期：2026-11-15
-- 结论：零 schema change —— 本批两页均为静态真实态，无新增数据消费者表。
-- ============================================================================
-- 依据（原站页面内 fetch + chunk 逆向，2026-11-15）：
--   P093 record：onLoad -> lotteryStatistics({lottery_id:id||''})
--                -> {"code":1,"msg":"successfully !!!","data":[]}（空态）
--                复用批次17 已实现的 POST /api/lottery/statistics，返回体完全一致。
--   P094 rule：正文来自 uni.getStorageSync('lotteryRule')，本账号实测恒为 null；
--              getConfig 中 draw_lots_rule / draw_lots_notice / helpdraw_rule /
--              share_rule 等全部规则 key 均为空字符串，P092 不写入该 storage。
--              -> 空白规则卡是当前真实态，无页面专用接口、无 DB 读取。
-- 按「无产物不设席位」P0 规则：接口全为空态/静态快照且无持久化数据，
--   不建无消费者的空表；不新增端点、不新增列、不新增种子数据。
-- 本批无新建表 / 无 ALTER / 无 INSERT；仅作记录以便迁移序列可追溯。
-- ============================================================================

SELECT 'batch19: no schema change (P093 reuses /api/lottery/statistics; P094 static empty-rule card, no endpoint)' AS note;
