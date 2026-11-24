# 批次21 三层像素对比报告（活动域 P100–P101）

> 日期：2026-11-16
> 口径：批次19/20 既定——文字层（文本/x/y/w/h/fs/fw/color，±4px 容差、按序对齐、SKIP `Cancel`）、
> 关键元素几何（±2px/圆角/背景归一，SKIP 框架层 BODY/HTML/UNI-*、uni-actionsheet/modal/toast）、
> 整图 MAE（2x 截图逐像素）。

## 结果：ALL PASS

| 页 | 内容 | 文字层 | 几何层 | 整图 MAE |
|---|---|---|---|---|
| P100 | `/pages/activity/draw` 抽签（活动不存在 → 纯白页） | 0/0 失败 | 0 失败 | **0.00** |
| P101 | `/pages/activity/record` 中签记录（空态图 + 提示） | 2/2 失败 0 | 0 失败 | **0.35** |

## 原站真实态（侦查结论，如实复刻不臆造）

- **P100 抽签**：原站 `GET /api/draw/get` 返回 `{"code":0,"msg":"活动不存在","data":null}`；
  前端组件渲染时抛异常（pageerror `"Object"`），`<uni-page-body>` 内容为空 → **整页纯白**。
  这是无活动参数直访的真实态，原站并行请求 `/api/draw/drawLog`（data:[]）与
  `/api/user/ranking`（types:2）均不产生可见内容。Clone 复刻为纯白空页 + 等价接口调用，
  截图逐像素一致（MAE 0.00）。
- **P101 中签记录**：原站 `POST /api/draw/drawLog` 返回 `data:[]` → 空态。
  navbar 白底 44px、返回箭头 back_btn.png 7×13 @(19,16)、标题「中签记录」17px/700 黑 @y11 h23；
  navbar 后透明空条带 `.br-12.pa-16` @(16,44) 343×32（draw_notice 为空残留）；
  开箱空态图 142×142 @(116.5,276)、提示「目前没有任何数据」13px #999 @(135,429)。无 TabBar。
- **背景色**：两页整页纯白 rgb(255,255,255)（多点采样实测），区别于批次20 的 #F5F5F5。

## 图片本地化

- 返回箭头 `b19-back-btn.png`、空态图 `b19-empty-data.png` 复用批次19 已本地化资源——
  与 P100/P101 原站同源图**逐像素一致**（426×426 / 26×46，ImageChops 差异为空），不重复下载。

## 产物

- `01_side_by_side.png`：两页原站/Clone 并排总图
- `b21_base_*.png` / `b21_clone_*.png`：2x 截图各 2 张
- `b21_snap.js` / `b21_compare.py`：抓图与对比脚本（可直接重跑复现）
