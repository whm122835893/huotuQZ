# 批次3 我的域二级页 三层像素对比报告

日期：2026-11-17
页面：P009-P019 / P021 / P022 / P024（14 页）
口径：批次19 三层像素对比（文字节点 / 关键几何 / 整图 MAE）

## 对比结果（全部 ALL PASS）

| 页面 | 文字层 | 几何层 | MAE | 真实态要点 |
|------|--------|--------|-----|-----------|
| P009 关于我们 | 5/5 | 0 fail | 0.91 | 白底协议页，去评分/用户协议/隐私政策 |
| P010 修改操作密码 | 3/3 | 0 fail | 0.66 | 6 验证码框截图隐形真实态 |
| P011 设置操作密码 | 7/7 | 0 fail | 0.81 | register-bg 红渐变整页背景 |
| P012 添加收货地址 | 11/11 | 0 fail | 2.70 | 表单卡 br12 + 默认开关 + 保存按钮 |
| P013 收货地址 | 2/2 | 0 fail | 0.30 | empty-data 空态 + address_list 恒空 |
| P014 注销账号申请 | 12/12 | 0 fail | 11.27 | 长文本卡片，字体回退 Times New Roman |
| P015 存证查询 | 4/4 | 0 fail | 5.05 | 双 textarea + 7px 极小占位符 |
| P016 赋能中心 | 0/0 | 0 fail | 0.37 | 原生 nav 空页真实态 |
| P017 流转记录 | 3/3 | 0 fail | 0.46 | 空态图 + 参数错误 toast |
| P018 个人信息 | 6/6 | 0 fail | 3.48 | 头像破图灰块 + 脱敏手机号 |
| P019 加入社群 | 1/1 | 0 fail | 0.09 | 二维码加载失败 193×193 灰块 |
| P021 隐私设置 | 2/2 | 0 fail | 2.34 | 排行信息开关（黑底开启态） |
| P022 客服 | 3/3 | 0 fail | 1.17 | 灰底 + 蓝电话/邮件 SVG + 待定文案 |
| P024 VIP权益中心 | 1/1 | 0 fail | 0.89 | 黑底 + 5 张 static/vip 图叠放 |

> P014 MAE 11.27 说明：文字层 12/12 + 几何层 0 fail + 并排图视觉一致，背景区 MAE 0-2。
> 高 MAE 来自原站 WebView 把中文回退到 Times New Roman（字形窄细）与本地 Chromium 渲染的系统性
> 字形差异，已显式锁定 `font-family: "Times New Roman", serif` 对齐。属既定可接受的字体度量差异。

## 路由一致性专项检查（用户重点要求）

| 检查项 | 结果 |
|--------|------|
| 96 路由 name/path 唯一性 | PASS，无重复 |
| 14 条新路由逐条直达 | 14/14 OK，title 正确、无 pageerror |
| my 页 6 个入口跳转目标 | 全部已注册，无断链 |
| 已交付页改动 | 零改动（setting.vue 入口保持 toast） |
| 全路由回归 | 95 路由 0 错误、0 非预期空白 |
| 发布域实测 | 14/14 OK |

## 门禁

- P0 扫描 ALL CLEAR：emoji 0 / 紫粉渐变 0 / AI 模板味 0；硬编码色仅 `#fff`/`#000` 例外。
- 后端：2 个新端点（address_list / getRecord）实测与原站逐字一致，零 schema change。
- 构建：vite build 通过，dist 导航页同步（num 85-98）。

## 文件

- `b3_compare.py`：三层对比脚本
- `b3_base_*.png` / `b3_clone_*.png`（在 recon 目录）：原站/clone 2x 截图
- `*_side_by_side.png`：14 页并排对比总图
- `b3_base_*_dom.json` / `b3_clone_*_dom.json`：DOM 采集数据

## 验收链接

- 导航页：`https://a68261aa230177548.app.workbuddy.host/m.html?sharecode=C2APU7DXA7`
- 示例 P014：`https://a68261aa230177548.app.workbuddy.host/?sharecode=C2APU7DXA7#/pages/my/cancellation`
- 示例 P024：`https://a68261aa230177548.app.workbuddy.host/?sharecode=C2APU7DXA7#/pages/my/vip`
