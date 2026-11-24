# OPEN-DECISIONS 悬而未决登记册

> 铁律：只追加 + 就地关闭（OPEN → RESOLVED，补 Resolution 字段），禁止整篇重写。
> 每次 Phase 开始时自动复现未决项到工作上下文最前面，逐条判断能否关闭。
> 三类固定 slug：`waiting-on-external-condition` / `design-decision-to-evaluate` / `existing-design-boundary`

## 汇总

当前：**0 未决 + 3 已决**（全量收官，所有待定项已关闭）

## 登记条目

| Date | Source | Slug | Open Item | Related Constraints | Current Leaning | Blocked By | Resolves When | Status |
|------|--------|------|-----------|---------------------|-----------------|------------|---------------|--------|
| 2026-11-12 | Phase 3 / 批次11 门禁复核 | `design-decision-to-evaluate` | `frontend/src/router/index.js` 达 331 行，超过「单文件≤300行」硬规则 | 该文件为纯声明式路由装配表、零业务逻辑；页面清单 113 页，路由条目随批次线性增长，全量后必然远超 300 行；用户硬规则第 6 条「不私自增删改已完成内容」；RoleVerdict 协议只标三类阻断（正确性缺陷／需求未满足／契约安全数据完整性破坏），不标风格偏好或为覆盖率而覆盖率 | 判为**门禁例外，不阻断交付，本批不擅自重构**；待全量页面完成后一次性按域拆分为 `router/routes/core.js`、`shop.js`、`order.js`、`collection.js` + `index.js` 仅做装配（拆分时动态 import 路径须由 `../pages/` 改为 `../../pages/` 并重跑构建与五 Tab 回归） | 等清单 113/113 全量完成；在此之前每批拆分都会反复改动已完成路由，违背用户硬规则第 6 条 | 清单全量完成且用户确认统一重构时 | RESOLVED (2026-11-19) |
| 2026-11-13 | Phase 3 / 批次14 入口联动 | `existing-design-boundary` | 已完成页 `pages/my/my.vue`（批次3）「我的钱包」入口仍为 `todo('我的钱包')` 占位，弹「页面将在后续批次交付」；原站实测该入口点击后跳转 `#/pages/wallet/wallet`，而 P082 本批已交付 | 用户硬规则第 6 条「不私自增删改已完成内容」——改动已完成页需用户授权；钱包页本身已通过导航页 `m.html` 与直接 URL 完全可点、发布域实测通过，不构成功能阻断 | **本批零改动**已完成页；钱包页独立交付。若需入口联动，将 `todo('我的钱包')` 替换为 `router.push('/pages/wallet/wallet')` 并补一次五 Tab 回归 | 等用户授权改动已完成页 | 用户确认「允许联动我的页钱包入口」后 | RESOLVED (2026-11-14) |
| 2026-11-14 | Phase 3 / 批次16 入口侦察 | `existing-design-boundary` | 已完成页 `pages/my/my.vue`（批次3）「邀请海报」按钮仍为 `todo('邀请海报')` 占位。原站实测该按钮点击后**不跳转**，而是在 my 页内弹出邀请海报浮层（红兔插画 + 二维码 + `HT173634` 邀请码 + 「复制链接」「保存海报」按钮 + 关闭 X），属于 my 页自身功能，**不在批次16 的 P084/P085 两页清单内** | 用户硬规则第 6 条「不私自增删改已完成内容」；且海报弹层与邀请活动页/记录页是不同功能，不是简单路由联动；P084/P085 已通过导航页与直接 URL 完全可点、发布域实测通过，不构成功能阻断 | **本批零改动**已完成页；如实登记原站弹层契约（截图 `b16_my_invite_click.png`）。若用户需要该弹层，作为 my 页增强单独立项（涉及 canvas 海报绘制/保存图片，非本批 2 页范围） | 等用户明确是否需要补做「邀请海报」浮层 | 用户裁决：a) 不需要则关闭；b) 需要则授权改动 my.vue 并单独立项 | RESOLVED (2026-11-15) |

## 复核记录

- 2026-11-12 批次11 交付前复核：该文件 331 行，未触发三类阻断，已通过 `npx vite build`（✓ built in 1.60s）与五 Tab 回归；P0 全量扫描（emoji／紫粉渐变／AI 模板味／弹跳缓动）0 违规。维持 OPEN，转交用户裁决是否需要提前拆分。
- 2026-11-12 批次12 交付前复核：注册 3 条新路由后 `frontend/src/router/index.js` 由 331 → **350 行**，仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过、五 Tab 回归与发布域验证通过。维持 OPEN，继续等 113/113 全量完成后统一按域拆分。
- 2026-11-13 批次13 交付前复核：注册 2 条新路由后 `frontend/src/router/index.js` 由 350 → **362 行**，仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过、五 Tab 回归与发布域验证通过。维持 OPEN，继续等 113/113 全量完成后统一按域拆分。
- 2026-11-13 批次14 交付前复核：注册 1 条新路由后 `frontend/src/router/index.js` 由 362 → **368 行**，仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过、五 Tab 回归与发布域验证通过。维持 OPEN，继续等 113/113 全量完成后统一按域拆分。
- 2026-11-13 批次14 新增第 2 条未决（我的页钱包入口 `todo` 占位未联动）：系用户硬规则第 6 条边界，非缺陷阻断；钱包页已可独立访问，交用户裁决是否授权改动已完成页。
- 2026-11-14 批次15 开工前关闭第 2 条：**Resolution** —— 用户明确「授权」。`pages/my/my.vue` 仅做最小闭环改动：新增 `useRouter()` + `openWallet()`（`router.push('/pages/wallet/wallet')`），并把「我的钱包」卡片的 `@click="todo('我的钱包')"` 替换为 `@click="openWallet()"`；其余 10 处 `todo()` 占位、视觉/文案/结构全部零改动。`npx vite build` 通过；本地 3000 与发布域双端 Puppeteer 实测：点击该卡片 hash 由 `#/pages/my/my` → `#/pages/wallet/wallet`，落地页文案「我的钱包 进入钱包」，`PAGE_ERRORS: none`。状态 OPEN → **RESOLVED**。
- 2026-11-14 批次15 交付前复核：注册 1 条新路由后 `frontend/src/router/index.js` 由 368 → **374 行**，仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过、五 Tab 回归与发布域验证通过。维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2 条保持 RESOLVED。
- 2026-11-14 批次16 交付前复核：注册 2 条新路由后 `frontend/src/router/index.js` 由 374 → **386 行**，仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过、五 Tab 回归与发布域验证通过。维持第 1 条 OPEN。新增第 3 条未决（my 页「邀请海报」浮层）：原站为页内弹层、非跳转、非本批清单范围，零改动已完成页，交用户裁决。


- 2026-11-15 批次17 开工前关闭第 3 条：**Resolution** —— 用户明确「批次17.再把批次三的占位做掉」，
  授权改动已完成页 `pages/my/my.vue`。新建 `components/InvitePoster.vue` 复刻原站页内海报浮层
  （红兔插画 + 头像/昵称/ID + HT173634 邀请码 + 二维码 + 复制链接/保存海报 + 关闭 X）；
  my.vue 仅把「邀请海报」的 `@click="todo('邀请海报')"` 换成 `showPoster = true`，
  并挂载浮层组件。三层像素对比浮层 5 个文字节点 5/5、几何 4/4 全通过；
  本地与发布域双端实测点击弹出、复制链接 toast「已复制」、关闭后浮层消失。状态 OPEN → **RESOLVED**。
- 2026-11-15 批次17 交付前复核：注册 5 条新路由（返佣 3 页 + 设置 + 抽奖）后
  `frontend/src/router/index.js` 由 386 → **418 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`npx vite build` 通过、16 页 + 五 Tab 回归、发布域 11 页 + 6 API 验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。
- 2026-11-15 批次17 新增边界登记（非阻断，如实记录）：P087 原站接口 404 导致白屏、
  抽奖页 1s 后自动返回 my、该态「抽 5 次」按钮不渲染、poster 前 29 个文字节点属已完成的
  「我的」页背景（本批未改动其视觉/CSS）、小字号 Range 高度跨平台字体度量差异（P086 表头
  改用系统字体族 bbox 21px→17px 贴近原站 16px）。详见 `docs/02-API文档.md` 批次17 边界说明与
  `docs/compare/batch17/09_summary.png`。

- 2026-11-15 批次18 交付前复核：注册 3 条新路由（masterRank / rankList / userBuy）后
  `frontend/src/router/index.js` 由 418 → **437 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`npx vite build` 通过、15 页 + 五 Tab 回归、发布域 11 页 + 3 API 验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-15 批次18 新增边界登记（非阻断，如实记录）：
  原站同名端点 `newrank1` / `marketrebang` 早期批次已实现且被已交付首页/市场页调用（演示数据），
  与本批侦查到的「当前真实态」（名人堂未开启 / 6 条真实榜单）不一致；为遵守硬规则第 6 条
  「不私自增删改已完成内容」，新增 `/api/rank/*` 三专用端点承载真实态快照，已交付端点与页面零改动。
  `/static/tangpai/*` 共 17 个资源原站全部 404 破图，不渲染/不本地化/不臆造替代；
  名人堂「名人堂加载失败」为瞬时 toast，按批次13/15/17 惯例 SKIP，稳定态快照等 4.5s 采集。
  详见 `docs/02-API文档.md` 批次18 边界说明与 `docs/compare/batch18/06_summary.png`。
- 2026-11-15 批次18 新增待评估项（不立 OPEN 条目，随清单推进自然消解）：
  顶部 Tab「关注」目标 `/pages/market/attentionList`（P025）尚未交付，点击沿用共享 TabBar
  既有「该页面将在后续批次交付」提示，不臆造未侦查页面；P025 交付后改为真实跳转。

- 2026-11-16 批次19 交付前复核：注册 2 条新路由（lottery/record / lottery/rule）后
  `frontend/src/router/index.js` 由 437 → **449 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`npx vite build` 通过、14 页 + 五 Tab 回归、发布域 11 页 + 复用端点验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-16 批次19 新增边界登记（非阻断，如实记录）：
  P093 中奖记录原站实际调 `/api/lottery/statistics`（非 `/lottery/record`），返回 `data:[]` 空态，
  本项目复用批次17 同名端点（返回体逐字一致），零新增端点；P094 活动规则正文来自
  `uni.getStorageSync('lotteryRule')`，本账号恒 null + getConfig 全部规则 key 为空字符串，
  空白规则卡是真实态，无页面专用接口、零 Schema 变更。原站 `cj-page-bg.png` 404 破图如实登记。
  P092「我的中奖」toast 占位联动到 record?id=2（视觉零改动，延续批次3/17 占位清理）。
  详见 `docs/02-API文档.md` 批次19 边界说明与 `docs/compare/batch19/04_summary.png`。

- 2026-11-16 批次20 交付前复核：注册 5 条新路由（draw 五页）后
  `frontend/src/router/index.js` 由 449 → **480 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`npx vite build` 通过、17 页 + 五 Tab 回归、发布域 10 页 + 端点验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-16 批次20 新增边界登记（非阻断，如实记录）：
  本批 5 页均为无参数态静态真实态——P096 中签结果永久加载态（chunk JSON.parse(undefined)
  异常）如实复刻；P099 唯一接口 `GET /api/score/get_my_share_details` 原站 HTTP 404 空响应体
  （与批次17「HTTP 200 + body 404 JSON」形态不同），本项目按实测形态分别复刻（exit 绕过
  debug trace 附加）。上传框加号 IconAdd.png / 规则卡 icon_cp.png 原站 404 破图如实登记为空白。
  uicon 字体（alicdn font_2225171）本地化 public/static/fonts/d20-uicon.woff（ttf 在 Chrome
  FontFace 解析 error，同源 woff 正常）。五页原站无固定入口（依赖活动参数/分享深链），
  按路由直接访问验收，不臆造入口。
  详见 `docs/02-API文档.md` 批次20 边界说明与 `docs/compare/batch20/README.md`。

- 2026-11-16 批次21 交付前复核：注册 2 条新路由（activity 两页）后
  `frontend/src/router/index.js` 由 480 → **492 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`vite build` 通过、17 页 + 五 Tab 回归（排除 P100 预期纯白）、
  发布域 12 页 + 2 个新端点验证通过。维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。
  第 2、3 条保持 RESOLVED。
- 2026-11-16 批次21 新增边界登记（非阻断，如实记录）：P100 抽签纯白是原站真实态
  （GET /api/draw/get「活动不存在」code0 → 组件渲染抛异常 → uni-page-body 空），
  不臆造抽奖界面；P101 中签记录 drawLog 空数组 → 开箱空态，图片复用批次19 逐像素同图资源。
  两页背景纯白（区别于批次20 #F5F5F5）。新增 Draw::get/Draw::drawLog 两个静态端点，
  零 schema change。详见 docs/compare/batch21/README.md 与 docs/02-API文档.md 批次21 章节。

- 2026-11-16 批次22 交付前复核：注册 1 条新路由（activeSelected）后
  `frontend/src/router/index.js` 由 492 → **498 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`vite build` 通过、18 页 + 五 Tab 回归、发布域 13 页 + 2 个新端点验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-16 批次22 新增边界登记（非阻断，如实记录）：P102 顶部两个分类入口背景图全部 404
  （OBS 域 active-banner-bg1/bg2 跨域失败 + cj-page-bg/active-new/active-lottey 均 404），
  实测卡片背景 = 页面 rgb(244,246,250)（隐形卡）+ 白字，如实复刻不臆造图片；接口
  Activity/category(data:[]) + activity/index(total:0) → 开箱空态，图片复用批次19 逐像素同图资源。
  新建 controller/Activity.php 两个静态端点，零 schema change。详见 docs/compare/batch22/README.md。

- 2026-11-16 批次23 交付前复核：注册 2 条新路由（certification 两页）后
  `frontend/src/router/index.js` 由 498 → **510 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`vite build` 通过、20 页 + 五 Tab 回归、发布域 15 页验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-16 批次23 新增边界登记（非阻断，如实记录）：P103 已完成实名态是本账号
  `real_name_authentication=1` 真实态（getUserInfo real_name/id_card 字段页面二次脱敏），
  P104 认证结果失败态为无参数直访真实态，两页均静态快照复刻、零新增 API、零 schema change。
  P104 原生 nav 双标题（16px 框架壳 + 17px 自定义层重叠）按框架层惯例在对比层 SKIP
  （采集文本带 pc 父元素 class），uni-page-head 白底元素几何层 SKIP。详见
  `docs/compare/batch23/` 与 `docs/02-API文档.md` 批次23 章节。

- 2026-11-16 批次24 交付前复核：注册 2 条新路由（kline 两页）后
  `frontend/src/router/index.js` 由 510 → **522 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`vite build` 通过、全路由回归、发布域验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-16 批次24 新增边界登记（非阻断，如实记录）：P105/P106 为深色主题真实数据快照
  （非空的静态页），行情数据固定值（`112.34CNY`/`-0.53%`/`30000`），banner u-swiper 两张
  原站 JPG 本地化、次图屏外如实复刻；价格/涨跌幅 `rgb(51,51,51)` 低对比为原站真实设计。
  零新增 API（复用 getConfig/getUserInfo/getBanner 且 getBanner 返回逐字一致）、零 schema change。
  对比脚本 fs 加浮点容差（Chromium 7px→7.00001px），避免 7px Tab 文字误报。详见
  `docs/compare/batch24/` 与 `docs/02-API文档.md` 批次24 章节。

- 2026-11-17 批次3 交付前复核：注册 14 条新路由（我的域二级页）后
  `frontend/src/router/index.js` 由 522 → **536 行**，仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；全 96 路由 name/path 唯一性校验通过；`vite build` 通过；
  14 条新路由逐条直达 14/14 OK；全路由回归、发布域验证通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-17 批次3 路由一致性专项登记（用户重点要求）：用户要求检查「路由乱没乱」，
  已做三重校验——(1) name/path 无重复；(2) 14 新路由直达渲染无 pageerror、title 正确；
  (3) my 页 6 个入口跳转目标全部已注册无断链。已交付页 setting.vue 的入口仍为 toast 占位，
  按「不改已交付页」原则未改动其绑定（本批 14 页作为可直接访问路由交付，与原站直访一致）。
  结论：路由未乱、无断链、无冲突。

- 2026-11-17 批次4 交付前复核：注册 11 条新路由后
  `frontend/src/router/index.js` 由 536 → **547 行**（实际 107 路由对象），仍为纯声明式装配、零业务逻辑，
  未触发三类阻断；`vite build` 通过、11 页 + 全路由 name/path 唯一性校验、11 条直达 + 五 Tab 回归通过。
  维持第 1 条 OPEN，继续等 113/113 全量完成后统一按域拆分。第 2、3 条保持 RESOLVED。
- 2026-11-17 批次4 边界登记（非阻断，如实记录）：P027 detail / P032 sellBegBuy / P033 sellInfo
  无参数直访时原站 fallback 渲染首页（body class=`pages-index-index`、API 组合与首页一致），
  本项目不改已交付首页，三路由复用 index.vue，克隆与本地首页逐字一致（P032 40/40）。
  对比口径改为静态骨架校验，动态公告/发售数据/pill·seg·tab 状态色按批次18/20惯例 SKIP。
  P032「藏品不存在」瞬时 toast SKIP；P030 marketGoodsDetail HTTP500 空壳静态快照复刻。
  零新增端点、零 schema change。详见 `docs/compare/batch4/README.md`。

- 2026-11-18 批次25-29 收官交付前复核：注册 7 条新路由后 `frontend/src/router/index.js` 达 114 条路由声明，
  仍为纯声明式装配、零业务逻辑，未触发三类阻断；`npx vite build` 通过（2.36s）、7 条路由直达 7/7 OK、
  全 114 路由 name/path 唯一、P0 全量扫描 0 违规。**113/113 全量已完成**。
  第 1 条（router/index.js 超 300 行）的 Blocked By 条件「等清单 113/113 全量完成」现已满足——
  按约定「清单全量完成且用户确认统一重构时」方才拆分，故状态维持 OPEN，待用户确认是否启动一次性按域拆分
  （core/shop/order/collection + index 装配，动态 import 路径 `../pages/` → `../../pages/` 并重跑构建与五 Tab 回归）。
  若用户不打算重构，可将第 1 条以「门禁例外、声明式装配不影响可维护性」就地关闭。


- 2026-11-19 全量收官后关闭第 1 条：**Resolution** —— 用户裁决选 B：认定 `router/index.js`
  作为纯声明式「网址→页面」路由对照表（114 条路由、564 行，零业务逻辑），超出「单文件≤300行」
  属可接受的门禁例外，不启动按域拆分。理由：路由表本质是配置清单而非逻辑代码，强行拆分增加维护成本、
  收益有限，且需改动 import 路径与重跑五 Tab 回归，得不偿失。**零代码改动**，文件维持 564 行。
  至此登记册 **0 未决 + 3 已决**，所有待定项关闭。
