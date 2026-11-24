# 批次23 三层像素对比报告（实名认证 P103-P104）

> 日期：2026-11-16
> 口径：批次19-21 既定——文字层（文本/x/y/w/h/fs/fw/color，±4px 容差、按序对齐、SKIP `Cancel`、
> SKIP `\ue601` 返回箭头、SKIP `uni-page-head` 框架层标题）、关键元素几何（±2px/圆角/背景归一，
> SKIP 框架层 BODY/HTML/UNI-*/uni-page-head/uni-actionsheet/modal/toast）、整图 MAE（2x 截图逐像素）。

## 结果：ALL PASS

| 页 | 内容 | 文字层 | 几何层 | 整图 MAE |
|---|---|---|---|---|
| P103 | `/pages/certification/certification` 实名认证-已完成态 | 6/6 失败 0 | 0 失败 | **1.81** |
| P104 | `/pages/certification/certificationStatus` 认证结果-失败态 | 4/4 失败 0 | 0 失败 | **2.13** |

## 原站真实态（侦查结论，如实复刻不臆造）

### P103 已完成实名认证态

- 本账号 `getUserInfo` 实测 `real_name_authentication=1`、`real_name=王*绵`、
  `id_card=520******838`（页面二次脱敏显示 `520************838`）→ 直访显示已完成态为真实态。
- 整页背景图 `cert-verified.png`（原站 `static/huotu/cert-verified.png`，1125×2436=3x），
  `background-size:375px` 含粉渐变 + 3D 勾 + 底部云朵，本地化 `public/static/huotu/` +
  `src/assets/images/b23-cert-verified.png`。
- 标题「实名认证」17px/700 黑 @(154,11 h23)，返回箭头 back_btn.png 7×13 @(19,16)。
- 主文案「当前已完成实名认证」20px/700 rgb(217,52,55) @(99,396 h27)；
  「姓名：王*绵」@(150,436)、「身份证号：520************838」14px/400 rgb(197,202,211) @(93,460)；
  隐私说明 11px/400 rgb(154,160,171) 两行 @(31,693 w314 h34)。
- 底部按钮 @(20,756 335×44) bg rgb(197,202,211) br12 +「已完成实名」14px/400 白 @(153,768)。

### P104 认证结果失败态

- 无认证流程参数直访 `certificationStatus` 显示失败态为原站真实态，如实复刻不臆造成功态。
- 整页纯白，`cj-page-bg.png` 404 破图如实不落地；中间 140×140 @(118,264) + 280×280 @(-22,124)
  透明空 DIV（截图无可见图标），如实留空。
- 标题「认证结果」17px/700 黑 @(154,11 h23)；「实名认证失败」24px/500 rgb(51,51,51) @(116,136 h26)；
  描述「该身份信息认证失败，请重试」14px/400 rgb(122,122,122) @(97,176 h15)。
- 深灰按钮 @(101,484 174×46) bg #313131 br12 +「重新认证」15px/600 白 @(157,501)。
- 原生 `uni-page-head__title` 16px/700 @(155,13) 与自定义 17px 标题重叠 → 框架层 SKIP。

## 字体 metrics 校准

- P104 三处文本 range 顶本地 PuHuiTi 比原站系统栈高 4~6px，用 transform translateY 补偿
  （失败标题 +8 / 描述 +5 / 按钮文字 +5），校准后文字层 0 失败。
- P103 主文案/字段/隐私说明用 transform translateY 2~3px 补偿，文字层 0 失败。

## P0 规则扫描

- Emoji 功能图标：0；紫粉渐变：0；AI 模板味文案/弹跳缓动：0；硬编码色仅注释文字描述（允许例外），
  CSS 色值全部 Token 化。**ALL CLEAR**。

## 交付范围

- 前端：`pages/certification/{certification,certificationStatus}.vue` + 各自 `.css`；
  路由 2 条（498 → 510 行）。
- 资源：`b23-cert-verified.png`（原站逐像素同图）、`b23-back-btn.png`（同源 b19 图）。
- 后端：**零改动**（复用已存在的 `Index::getConfig` + `User::getUserInfo`），零新增端点。
- SQL：`increment_v18_batch23.sql` 仅占位记录（零 schema change）。
- 导航页：`m.html` 追加 P103/P104 入口（num 81/82），dist 同步。
