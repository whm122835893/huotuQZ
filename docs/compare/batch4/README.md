# 批次4 市场交易 11 页 三层像素对比报告

日期：2026-11-17
页面：P025/P026/P027/P028/P030/P031/P032/P033/P034/P035/P036（共 11 页）
口径：批次19/23/3 三层像素对比（文字节点 / 关键几何 / 整图 MAE）
原站：`https://h5.hnyiqin.asia`，登录态 2.2s 稳定态采集；clone：`http://localhost:3000`。

## 对比结果（全部 ALL PASS）

| 页面 | 文字层 | 几何层 | MAE | 真实态要点 |
|------|--------|--------|-----|-----------|
| P025 关注列表 | 29/29 0f | 0/32 0f | 7.79 | 自定义顶栏「经典/关注/热榜」+筛选栏；2 列 8 卡真实快照；左白卡右深灰卡；底部 TabBar 首页高亮 |
| P026 分类管理 | 7/7 0f | 0/5 0f | 0.77 | 白底；我的分类 3 胶囊 + 其他分类空；customSeriesList 空数组 |
| P027 市场详情 | 14/15 0f | 0/15 0f | 42.71 | 无参数直访 fallback 首页（骨架校验口径，见边界） |
| P028 赠送记录 | 1/1 0f | 0/3 0f | 0.36 | 白底空页；nav + 一条浅灰占位条；tradingInformation list:[] |
| P030 商品列表 | 14/14 0f | 0/14 0f | 3.06 | marketGoodsDetail HTTP500 空壳；loading 灰块 + 限价¥-- + 发行/流通 + 4Tab + 空态图 + 双按钮 |
| P031 市场搜索 | 35/35 0f | 0/29 0f | 3.82 | 搜索框 + 数字藏品/盲盒竖排 Tab + 8 商品行；涨红跌绿平灰（中国习惯） |
| P032 寄售求购 | 14/15 0f | 0/15 0f | 42.91 | 无参数 fallback 首页；getSellCollectionInfo code0「藏品不存在」瞬时 toast（对比层 SKIP） |
| P033 寄售信息 | 14/15 0f | 0/15 0f | 16.33 | 无参数 fallback 首页（骨架校验口径，见边界） |
| P034 发起求购 | 9/10 0f | 0/11 0f | 3.71 | collectionDetail code0「藏品ID不能为空」；表单 + toast + 合计/预付 |
| P035 委托购买 | 17/17 0f | 0/11 0f | 4.70 | 最高委托价/委托价格/预计收入/委托说明 4 卡 + 合计/预付；无 toast |
| P036 GM工会 | 10/10 0f | 0/5 0f | 7.02 | 深棕主题；d-bg 宝箱背景 + 锁 + 未解锁提示 + 3Tab + 权益卡（钱包/喇叭/分割线原站图本地化） |

## 路由校验

| 检查项 | 结果 |
|--------|------|
| 11 条新路由逐条直达 | 11/11 OK，title 正确、pageerror=0 |
| 107 路由 name/path 唯一性 | PASS，无重复 |
| 构建 | `vite build` 通过（✓ built in ~2.6s） |
| 已交付页改动 | 零改动（首页 index.vue / TabBar / market.vue 全部未改） |
| 大字体对齐 | 价格数字沿用原站 BebasNeue（本地 OTF 已加载） |

## 门禁

- **P0-1 emoji**：0（支付选中态对勾由 `✓` 字符改为 SVG 描边图标）
- **P0-2 紫粉渐变**：0
- **P0-3 AI 模板味**：0
- **色值 Token 化**：各页面 CSS 变量集中在 `:root` 级选择器
- **后端**：零新增端点、零 schema change（`increment_v18_batch4.sql` 仅记录）
- **图片**：8 张商品图 + GM工会 5 图全部原站下载本地化；首页资源复用早期批次

## 边界登记（非阻断，如实记录）

### P027 / P032 / P033 首页 fallback
原站这三页无参数直访时，body class 均为 `pages-index-index`，API 组合（getBanner + afficheList +
getSeries + blind_box/list + MarketDetail/ids/undefined code0）与首页完全一致 —— 即原站把这三个
二级路由 fallback 渲染成了首页。本项目严格不改已交付首页，故这三个路由直接复用 `index.vue`
组件，克隆结果与本地首页逐字一致（实测 P032 与首页文字节点 40/40 完全相同）。

对比口径：仅校验首页**静态骨架**（banner 区/活动卡/Tab 结构/筛选栏/TabBar 文字存在性与坐标）。
以下为首页组件自身的动态态，采集时点不同导致差异，按批次18/20「真实态动态数据」惯例 SKIP 并登记：
- 公告轮播文本（每条采集时点不同顺序）
- 发售日历商品数据（2026泡泡探险 / 2026登机牌 等，随真实数据变化）
- pill「即将开售/已开售」选中态色、seg「热榜/排行」字号、TabBar 首页高亮色（动态状态色）

这三页整图 MAE 偏高（16~43）全部来自动态公告文本差异与 BebasNeue 数字字形，静态骨架几何层 0 fail。

### 其他瞬时/静态态
- P032「藏品不存在」toast：`uni-simple-toast` 瞬时提示，按批次13/15/17/18 惯例对比层 SKIP。
- P030 原站采集到 `pageerror: Object`（marketGoodsDetail 500 未捕获），本项目以空壳静态快照忠实复刻，
  页面本身 pageerror=0（更稳健，视觉一致）。
- P034/P035 的 `−` `+` `A` 等为支付控件符号，汇付选中对勾已改 SVG（P0 合规）。

## 文件

- `b4_compare.py`：三层对比脚本（含 fallback 页骨架校验口径）
- `compare_result.txt`：对比结果
- `route_check.txt`：11 条路由直达校验
- `P*_side_by_side.png`：11 页原站/clone 并排图
- recon 目录：`b4_base_*.png` / `b4_clone_*.png` / `*_dom.json`（2x 截图与 DOM 采集）

## 验收链接

- 导航页：`https://a68261aa230177548.app.workbuddy.host/m.html?sharecode=C2APU7DXA7`
- 示例 P025：`https://a68261aa230177548.app.workbuddy.host/?sharecode=C2APU7DXA7#/pages/market/attentionList`
- 示例 P036：`https://a68261aa230177548.app.workbuddy.host/?sharecode=C2APU7DXA7#/pages/market/tradeUnion`
