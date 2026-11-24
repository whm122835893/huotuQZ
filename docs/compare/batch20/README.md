# 批次20 三层像素级对比报告 —— 抽奖活动五页 P095-P099

- 日期：2026-11-16
- 原站：`https://h5.hnyiqin.asia`（header token 登录态）
- 本地：`http://localhost:3000`（ht_token 登录态，账号 17587881293）
- 视口：375×812，deviceScaleFactor=2
- 对比脚本：`b20_snap.js`（DOM/截图采集）+ `b20_compare.py`（三层比对）
- 口径：沿用批次19——文字节点 ±4px 全字段（text/x/y/w/fs/fw/color）；关键元素几何 ±2px
  （背景/圆角归一）；整图 MAE < 8；SKIP：Cancel（uni-actionsheet 系统隐藏项）、
  \ue601 返回箭头（本批改为真实文本节点后已纳入对比）、旋转动画 spinner 包围盒、
  uni 框架包装层（BODY/HTML/UNI-*，本地以等价 div 实现）

## 结论：五页全 ALL PASS

| 页面 | P 码 | 文字层 orig/clone | 文字 fails | 几何 fails | 整图 MAE | 结果 |
|------|------|------------------|-----------|-----------|----------|------|
| communityShare | P095 | 14/14 | 0 | 0 | 1.75 | PASS |
| drawResult | P096 | 2/2 | 0 | 0 | 0.50 | PASS |
| InvitationFriendAssist | P097 | 12/12 | 0 | 0 | 1.21 | PASS |
| Leaderboard | P098 | 5/5 | 0 | 0 | 0.89 | PASS |
| shareCommunityGroup | P099 | 12/12 | 0 | 0 | 1.66 | PASS |

## 关键复刻事实（原站实测，2026-11-16）

1. **P095 分享助力**：黑卡 padding 12px 20px（非 15px）、计数句模板带空格
   `您已成功获得助力 {{count}} 次`；分享图片按钮 68×33 br8 无可见边框（白字 244,246,250）；
   上传框 80×80 #E5E5E5 br12（内部加号 IconAdd.png 原站 404，渲染空白，如实复刻）；
   表头三列 120/86/69 居中；空态 uicon \ue62f 90px #C0C4CC + 暂无分享记录；
   规则卡 343×66 带图标占位 15×15（icon_cp.png 原站 404）；底部 fixed 76px 提交按钮 338×46。
2. **P096 中签结果**：onLoad 缺页面参数 → JSON.parse(undefined) 抛异常 → loading 恒 true，
   永久 spinner（26×26 圆环 3px 边框 #C8C8C8 旋转）+「加载中...」12px，wrapper @165,348。
   加载层透明覆盖（navbar 可见）。
3. **P097 邀请好友助力**：邀请按钮 343×46 @16,26；黑卡 343×51 padding15；
   表头四列 62/78/109/62 无间隙；空态「数据为空」；规则卡 343×66；无底部 fixed。
4. **P098 参与人数**：黑卡全宽 375×57 padding 12px 20px；数字 fw700 666 色；
   底部 fixed 白底 91px，按钮 360×46 @8。
5. **P099 分享社群**：同 P095 结构；规则卡 343×64 **无图标**（原站 E 无 u-icon），
   标题 15px/400 黑色（与 P095 的 16px/700 暗灰不同）；提交按钮 bg rgb(20,20,22)
   br0（br-33 类计算值 0px）@16,423，非 fixed；唯一接口
   GET /api/score/get_my_share_details?type=1|2 → 原站 HTTP 404 空响应体
   （curl 实测 content-length:0），前端 catch 后积分默认 0 静态渲染。

## 字体与图标

- 原站 uview uicon 字体（at.alicdn.com `font_2225171_8kdcwk4po24`，211 glyphs）本地化为
  `frontend/public/static/fonts/d20-uicon.woff`，同时覆盖 `\ue601` 返回箭头（系统导航栏）
  与 `\ue62f` 空态图标，五个页面统一引用，与原站同源同 glyph。
- 注意：alicdn 原始 ttf 在 Chrome FontFace 下解析 error（post 表 2 字节异常），
  ttf 重整后仍被拒，改用同源 woff 后 `document.fonts` 确认 loaded。

## 字体 metrics 系统差校准

原站字体（系统栈）range 高约比本地 PuHuiTi 小 5px，导致文字 range 顶系统性偏移 -2~-5px。
本批通过 margin/padding 链式补偿校准（黑卡行 padding-top 5、上传链 21/11/21/11、
按钮 line-height 21 + padding-top 5、空态文字 margin-top 15、分享图片按钮 padding 11px 6px 6px），
校准后全部文字 dy ≤ 2。视觉与 MAE 均已验证。

## 附件

- `b20_base_*.png` / `b20_clone_*.png`：五页 2x 截图
- `b20_snap.js`：DOM 采集脚本（文字节点 + 可见元素 + 截图）
- `b20_compare.py`：三层比对脚本
