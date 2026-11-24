# 批次22 三层像素对比报告（活动精选 P102）

> 日期：2026-11-16
> 口径：批次19-21 既定——文字层（文本/x/y/w/h/fs/fw/color，±4px 容差、按序对齐、SKIP `Cancel`）、
> 关键元素几何（±2px/圆角/背景归一，SKIP 框架层 BODY/HTML/UNI-*、uni-actionsheet/modal/toast）、
> 整图 MAE（2x 截图逐像素）。

## 结果：ALL PASS

| 页 | 内容 | 文字层 | 几何层 | 整图 MAE |
|---|---|---|---|---|
| P102 | `/pages/activeSelected/activeSelected` 活动精选（活动中心 + 空态） | 4/4 失败 0 | 0 失败 | **0.35** |

## 原站真实态（侦查结论，如实复刻不臆造）

- 整页 + navbar 均 `rgb(244,246,250)=#F4F6FA`（多点采样实测）；标题「活动中心」17px/700 黑 @(154,11 h23)；
  返回箭头 back_btn.png 7×13 @(19,16)。
- 顶部两个分类入口 `.item` @(16,60) 164×64 与 @(195,60) 164×64，背景图 `active-banner-bg1/bg2.png`
  原站 OBS 域跨域加载失败 + `cj-page-bg.png`/`active-new.png`/`active-lottey.png` 均 **404 破图**，
  实测卡片背景 = 页面 #F4F6FA（背景图 404 后隐形），仅白字「最新活动」@(90,86)、
  「抽奖中心」@(269,86)、15px/400 `rgb(255,255,255)` 浮于页面底上——如实复刻隐形卡 + 白字，不臆造图片。
- 接口：`POST /api/Activity/category` 返回 `data:[]`、`POST /api/activity/index` 返回
  `data:{total:0,per_page:10,current_page:1,last_page:0,data:[]}` → 两个分类下均无活动 →
  开箱空态图 142×142 @(116.5,236) +「目前没有任何数据」13px 灰 @(135,389)。无 TabBar。

## 字体 metrics 校准

- banner 白字：本地 PuHuiTi 字体 range 顶比原站系统栈高 5px（h21 vs h16），
  通过 `transform: translate(22px, 5px)` 同时校正 x 偏移（卡片内居中 @68 → 原站 @90）与 y 偏移，
  校准后 x=90/269、y=86 全部对齐，文字层 0 失败。

## 图片本地化

- 返回箭头 b19-back-btn.png、空态图 b19-empty-data.png 与 P102 原站同源图**逐像素一致**，
  直接复用批次19 已本地化资源，零重复下载；banner 背景图原站 404，不落地任何图片文件。

## 产物

- `01_side_by_side.png`：原站/Clone 并排总图
- `b22_base_activeSelected.png` / `b22_clone_activeSelected.png`：2x 截图
- `b22_snap.js` / `b22_compare.py`：抓图与对比脚本（可直接重跑复现）
