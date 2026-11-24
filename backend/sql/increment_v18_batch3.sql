-- ============================================================================
-- 增量脚本 v18 - 批次3 我的域 14 页（P009-P019/P021/P022/P024）
-- 日期：2026-11-17
-- 说明：本批 14 页为静态真实态快照复刻，零 schema change。
--   - 12 页仅复用全局接口 Index::getConfig + User::getUserInfo（后端已存在）。
--   - P013 收货地址列表：原站 POST /api/user/address_list 恒空 → User::addressList
--     直接返回 code:1/msg:地址列表/data:[]，不建无消费者的地址表。
--   - P017 流转记录：原站 POST /api/user_collection/user_collection/getRecord
--     无参数直访返回 code:0/msg:参数错误/data:null → User::getRecord 如实复刻。
-- 按「无产物不设席位」P0 规则：不建空表；本脚本仅占位记录，可安全重复执行。
-- ============================================================================

SELECT 'batch3 我的域 14 页：零 schema change，2 个静态端点（addressList/getRecord）无数据表' AS note;
