# 火兔 H5 — API 文档（逆向侦查 v1）

> 基站：`https://api.hnyiqin.asia`
> 协议：全部 POST（JSON body）；部分列表接口以 query 传参（如 `?type=10`、`?page=1`）
> 鉴权：请求头 `token: <登录返回的uuid>`（30 天有效）；公共头 `version: 1.0.1`、`Content-Type: application/json`
> 响应包格式：`{"code":1,"msg":"...","time":"<unix>","data":{...}}`（code=1 成功，0 失败，500 异常）
> 复刻约定：本项目后端按 ThinkPHP 6 重新实现同名端点，前端以 `/api` 前缀调用自有后端，**不依赖原站**

---

## 1. 通用约定

| 项 | 值 |
|----|-----|
| 时间 | 响应 `time` 为服务端 Unix 秒 |
| 分页 | 入参 `page`（有时 `list_rows`）；出参 `{total, per_page, current_page, last_page, data[]}` |
| 图片 | 返回相对路径 `/uploads/...`，前端拼 OSS 域名 |
| CORS | 允许 H5 域，OPTIONS 预检 |

## 2. 端点清单（主包逆向提取 66 个 + 流量捕获补充）

### 2.1 首页 / 公共

| 端点 | 入参 | 出参核心 | 说明 |
|------|------|----------|------|
| `/api/index/getConfig` | `{key:"resell_collection,copyright,..."}` | 40+ 配置键值 | appName/appLogo/支付类型/协议开关/赔付时限300s 等 |
| `/api/index/getVersion` | - | 版本信息 | app 升级检查 |
| `/api/banner/show/getBanner` | `{}` | `{total,data:[{id,image,url,url_type,is_show}]}` | 首页轮播 |
| `/api/index/afficheList` | `page` | 公告分页 `{id,image,title,createtime,type,affiche_type}` | 首页滚动公告+公告中心 |
| `/api/index/affichetypelist` | - | 公告分类 | 发现页分类图标（官方/运营/活动/寄售/空投/求购/限价/合成） |
| `/api/index/getSeries` | `?type=10/20` | 系列+内置项（-1 推荐/-2 我的关注/0 全部） | 10=藏品 20=盲盒 |
| `/api/index/getCollectionGoods` | `{type,page,series_id}` | 藏品商品分页 | 首页商品卡列表 |
| `/index/customSeriesAdd` / `customSeriesDel` / `customSeriesList` | - | 自定义系列 | 市场自建分类 |
| `/api/index/gettest` | - | - | 测试 |
| `/diypage/index`（addons） | - | 装修模块 JSON 流 | 首页装修化区块（menu 等） |

### 2.2 用户域 `/api/user/*`

| 端点 | 入参 | 出参/说明 |
|------|------|-----------|
| `login` | `{account,password}` | `userinfo{id,nickname,mobile,avatar,score,inviter_code,token,expiretime,expires_in:2592000}` |
| `register` | `{mobile,password,code?}` | 注册 |
| `resetpwd` | - | 重置密码（忘记密码） |
| `getUserInfo` | - | 全量用户：money/score/level/verification/inviter/实名状态/支付通道费率(huifu_service_charge,sandpay_status,yibao_status)/transfer_permission 等 |
| `profile` | - | 修改资料 |
| `sms/send` + `sms/getCapt` | `{mobile,event}` | 短信验证码（send 前先 getCapt 图形验证） |
| `setPayPassword` / `checkPaypass` | - | 支付密码 |
| `RealAuthentication` | - | 实名认证 |
| `bindBank` / `getBankInfo` | - | 银行卡 |
| `invite` | - | 邀请关系/邀请码 |
| `bei_yao_list` | - | 被邀请列表 |
| `commision_info` / `commison_rank` | - | 佣金 |
| `masterlist` / `masterinfo` | - | 大师列表 |
| `ranking` / `newrank1` | - | 排行 |
| `applyIssuer` / `getIssuer` | - | 发行方申请 |
| `setshowrank` | - | 排行榜显示开关 |
| `setbuynum` | - | 购买次数设置 |
| `aliPhoneOauth` | - | 阿里手机号一键登录 |
| `aboutGift` | - | 转赠说明 |
| `getybdjbtoken` | - | 易宝电签 token |
| `delAccount` | - | 注销（my/cancellation） |
| `ParamsToUser` | - | 参数转赠用户校验 |

### 2.3 藏品/收藏 `/api/user_collection/*`

| 端点 | 说明 |
|------|------|
| `user_collection/getList` | 我的藏品分页 |
| `user_collection/getDetail` `/detail/ids/` | 藏品详情（持有序号） |
| `user_collection/ListGoodsDetail` / `ListDetail` | 列表商品详情 |
| `user_collection/collectionCertificate` | 藏品证书 |
| `user_collection/getRecord` | 流转记录 |
| `user_collection/likeCollection` | 关注/喜欢 |
| `user_collection/show_resale_money` | 预估寄售所得 |
| `user_collection/reGift` / `reGiftOrder` | 转赠发起/订单 |
| `user_collection/antMycollection` | 蚂蚁链资产映射 |
| `user_collection/doBoxCollection` / `BoxCollectionList` / `HaveBoxCollection` | 盲盒开盒/盒列表 |
| `user_collection/exchange/usercollectionList` | 兑换 |
| `/api/box/collection/CollectionList`、`ProductDetail/ids/` | 盲盒商品 |
| `/api/box/blind_box/list` | 首页盲盒模块 |
| `box.Collection/getCollectionEarlyPurchaseCoupons` | 早购券 |

### 2.4 市场交易（流量捕获 + 子包推断）

| 端点 | 说明 |
|------|------|
| `/api/market/market/getMarketList` | 市场列表 `{name,series_id,id,listimg,total_num,reprice_limit,deal_num,min_price,is_like,market_fluctuate,goods_type,product{}}` |
| market/search、marketGoodsList | 市场搜索/系列商品 |
| market/detail + market/detail/* | 藏品市场详情（K线入口 kline/info、kline/buy） |
| market/submitEntrust、order/order/myConsignment | 寄售提交/我的寄售 |
| market/submitBegBuy、sellBegBuy | 求购 |
| market/attentionList | 关注列表 |
| market/giftRecord | 转赠记录 |
| market/tradeUnion | 工会交易 |
| consignments/* | 寄售大厅/我的寄售/搜索 |
| order/*：list、entrust(list/info)、beg(list/info)、orderSubmit、marketDetail、defaultDetail、regiftDetail、firstSaleConfirm、search | 订单全链路 |

### 2.5 插件（addons）

| 端点 | 说明 |
|------|------|
| `/addons/signin/api.index/index` | 签到面板：`signinscore{s1..s7,sn}`、补签 `isfillup/fillupscore:100/fillupdays:3`、`ranklist` |
| `/addons/signin/api.index/dosign` | 签到 |
| `/addons/signin/api.index/monthSign` | 月签到日历 |
| `/addons/diypage/index` | 装修页 |

### 2.6 其他

- `/api/synthesis/themeinfo` — 合成主题（活动 Tab）
- `/api/synthesis/*`（合成 record/result/schemeDetails 对应）
- `/api/lottery/*`、`/api/draw/*` — 抽奖/抽奖活动
- `/common/getAttachment`、`/common/uploadaccess` — 上传
- `/auth/index/gettoken` — 三方授权

## 3. 前端请求封装约定（原站行为）

- 统一 POST + JSON；query 传 page/type
- token 过期返回特定 code 后跳登录页
- version 头用于灰度/审核开关

## 4. 复刻实现优先级

P0：login/register/sms、getConfig、getBanner、afficheList、getSeries、getCollectionGoods、getUserInfo、getList(藏品)、getMarketList、market/detail、myConsignment
P1：寄售/求购/订单全链路、盲盒、合成、签到、钱包
P2：抽奖、排行、K线、装修页、其余子页

---

## 首页批次新增接口（P008，已实现并实测）

> 响应统一格式 `{code,msg,time,data}`；`code=1` 成功。数据全部来自本项目 MySQL。

| Method | Path | 功能 | 请求体 | 关键响应字段 |
|--------|------|------|--------|--------------|
| POST | /api/banner/show/getBanner | 首页轮播图 | `{}` | `data.data[]: id,image,url,url_type,is_show` |
| POST | /api/index/afficheList | 公告列表 | `{}` | `data.data[]: id,title,createtime,type,url,affiche_type` |
| POST | /api/index/getSeries | 类型下拉数据 | `{type:10}` | `series_name/series_id(-1推荐,-2我的关注,0全部)/is_def` |
| POST | /api/box/blind_box/list | 发售日历（首页商品列表） | `{list_rows:10,page:1}` | `id,name,listimg,price,total_num,release_time,release_date_time_text,sale_status(1即将/2已开售),publisher{}` |
| POST | /api/market/market/marketrebang | 热榜 | `{}` | `id,name,total_num,flux,listimg,reprice_limit,hotnum(成交量),is_like,min_price` |
| POST | /api/user/newrank1 | 购入值排行（名人堂） | `{}` | 当前与原站同构：`code:0, msg:"名人堂未开启"` |
| POST | /api/index/getConfig | 全局配置 | `{}` | `sitename,logo` |

### 侦查记录（原站行为）
- 首页商品列表真实接口为 `/api/box/blind_box/list`（msg="发售日历"），**不是** getCollectionGoods（后者需登录+系列参数）
- 「即将开售/已开售」胶囊切换**不发起新请求**，为前端本地过滤（sale_status）
- 「类型」下拉数据来自 `/api/index/getSeries`，选项：推荐 / 我的关注 / 全部
- 「热榜」接口 `/api/market/market/marketrebang` 空请求体；排名1显示奖章图（home-rank-top1.png），2+ 显示红色序号
- 「排行」接口 `/api/user/newrank1` 当前返回 `code:0 名人堂未开启`，页面显示我的排名卡 + 前三领奖台（值均为"-"）+ 空状态图
- 开售倒计时条仅当 `release_time` 未到时显示：`今天/明天/MM.DD HH:MM 开售 + HH : MM : SS`；开售后隐藏
- 图片资源：原站 CDN 已废弃部分路径（如发行方 logo 返回 NoSuchKey），本项目统一使用本地 `/uploads` 由自建后端托管

## 我的批次新增接口（P020，已实现并实测）

> 鉴权：POST header `token`；**发布域名代理层会剥离自定义请求头**，故前端同时把 token 写入请求体，后端 `auth()` 兼容 header/body 双通道。

| Method | Path | 说明 | 入参 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/user/getUserInfo | 个人信息（已扩展） | `{}`（token） | 新增 `real_name_authentication`（实名徽章开关）、`real_name`（脱敏）、`statistics{invited_count,buy_count,sale_count,join_day,collection_count,blindbox_count}`（与原站同构）、`is_vip` |
| POST | /api/order/order/myConsignment | 我的-寄售中列表 | `{status:1,types:2,goods_type:2,page:1}` | 分页结构 `total/per_page/current_page/last_page/data[]`；寄售模块交付前返回空列表 |
| POST | /api/market/market/getMarketList | 关注列表（series_id=-2） | `{goods_type:2,page,list_rows,series_id}` | `data[]`；市场模块交付前返回空数组 |

### 侦查记录（原站我的页行为）
- 页面数据源：`getUserInfo`（统计/实名/资产数）+ `myConsignment`（寄售中）+ `getMarketList{series_id:-2}`（关注）
- `statistics` 与原站同构：邀请0/购入0/出售0/加入天数7/藏品2/盲盒0（按本项目 DB 实算，种子数据与原站账号状态一致）
- 「寄售中/关注」胶囊：选中态 `border 1px #C5CAD3, radius 14, h28`，文字 14px bold #212121；未选中无框灰字 #9AA0AB
- 「关注」Tab 选中后显示市场表头：名称/成交量（左）、地板价/涨跌幅（右），11px #9AA0AB，带上下排序箭头，空态箱子隐藏
- 头像 72x72 r16（默认头像映射 mine-default-avatar.png）；横幅 mine-bg.png 375x180；已实名徽章 74x26 跟随昵称
- 原站默认头像字段 `/defautAvatar.png`（拼写如此），前端映射为本地兔标资源

### 踩坑记录（pitfalls.jsonl 同步）
- `proxy/strips-custom-headers`：workbuddy 发布域名代理会剥离自定义请求头 `token` → 后端 auth() 增加 body token 兜底，前端 request.js 拦截器同步写入 `data.token`

## 市场批次新增接口（P029，已实现并实测）

| Method | Path | 说明 | 入参 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/market/market/getMarketList | 藏品市场列表 | `{goods_type:2,page,list_rows,series_id}` | `data` 为**纯数组**；项含 `name/listimg/deal_num/min_price(可null→前端显示"--")/market_fluctuate_new(涨跌幅%)/total_num/product{}`；series_id -1推荐/-2关注→空数组 |
| POST | /api/market/market/marketList | 盲盒市场列表 | `{goods_type:1,page,list_rows,series_id:3}` | 分页结构 `total/per_page/current_page/last_page/data[]`（当前空） |
| POST | /api/index/getSeries | 增加 type=20 | `?type=20` | `[{series_name:'盲盒系列',series_id:3,...}]` |
| POST | /api/user/newrank1 | 购入值排行（升级） | `{}`（token） | `code:1` + `headUser[20]/list/rank1/rank2/my1/my2{myrank:'未上榜',price}`；rank2=购入值榜（市场-排行展示），种子数据与原站快照一致 |

### 数据表
- `ht_market`：市场商品（id/name/series_id/listimg/total_num/deal_num/min_price可NULL/fluctuate/goods_type/sort）
- `ht_rank`：购入值榜（rank_type 1/2、pos、nickname、price）

### 侦查记录（原站市场页行为）
- 顶部四标签：藏品（默认）/盲盒/关注/排行；选中 16px bold #212121 + 红下划线 24x3
- 「推荐」卡 120x64 #EEF1F5 + market-recommend.png 角标(33x14)；选中卡 #FDECEE + 1px #D93437 边框
- 排行数据源 newrank1：**时段性开放**（每日晚24时结算，晚间返回榜单，白天返回"名人堂未开启"）；本项目固定返回榜单
- 原站排行榜像统一为火兔 logo（uploads CDN 原图已失效），本项目统一使用本地 logo.png
- 首页-排行标签（P008）当前仍为"名人堂未开启"静态呈现；原站在榜单开放时段首页同样展示榜单——如需同步可后续小改（涉及已交付页面，等确认）

## 发现批次新增接口（P038，已实现并实测）

| Method | Path | 说明 | 入参 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/index/afficheList | 公告列表（升级） | `{page,list_rows,category}`（可全缺省） | 带参=发现页分页结构 `{total,per_page,current_page,last_page,data[]}`；无参=首页兼容（per_page=15 一次全出）；`category`=affichetypelist id（0全部），项含 `id/title/createtime(时间戳)/type/url/affiche_type` |
| POST | /api/index/affichetypelist | 公告分类列表 | `{}` | `data` 为**纯数组** `[{id:1,name:'官方'},...{id:8,name:'合成'}]` |

### 数据表
- `ht_affiche_type`：公告分类（id 1-8：官方/运营/活动/寄售/空投/求购/限价/合成）
- `ht_affiche`：重播种为原站同款 12 条（id 4-15、标题/类型/时间戳逐条对齐原站接口快照）

### 侦查记录（原站发现页行为）
- 分类图标为**白色 PNG + CSS filter 变色**：选中 filter:none（白图标红底 #D93437），未选中 `brightness(0) invert(62%)`（灰图标灰底 #F0F3F6）；求购与官方共用 notice-official.png（原站 DOM 实测）
- 图标映射：全部=notice-all/官方=notice-official/运营=notice-operation/活动=notice-activity/寄售=notice-consign/空投=notice-airdrop/求购=notice-official(复用)/限价=notice-limit/合成=notice-new
- 公告列表接口原站请求体：`{"page":1,"list_rows":10,"category":0}`；上拉加载 page++
- 长标题自然换行（white-space:pre-line，两行时卡高 134），箭头与标题第一行对齐
- 原站 uni-image 视觉层为 background-image，内层 img opacity:0——抓样式需看背景层

### 踩坑记录（pitfalls.jsonl 同步）
- `vite/dynamic-url-template`：`new URL(\`...${n}\`, import.meta.url)` 模板字符串动态拼资源路径在 vite dev 可用但响应式数据生成时漏字段（icon undefined → src=null 破图）；修复=map 时显式带 icon 字段
- `tabbar/missing-active-asset`：TabBar active 图标文件未提前下载（tab-market-discover.png 404），交付 Tab 页前必须核对 assets 中所有被引用文件存在

## 批次5 新增接口（P037/P039/P040/P041/P042 · 2026-11-08）

| Method | Path | 功能 | 请求 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/index/afficheDetail/ids/{id} | 公告详情 | 路径参数 id | `data`：`{id,collection_ids,title,describe,content(富文本HTML),type,image,createtime,updatetime,url,read,sort,publish_time,status,top,affiche_type_id,image_type,is_wxpusher,collection[]}`；不存在时 `data:null`（与原站一致） |
| POST | /api/index/afficheList（增强） | 公告列表+搜索 | 新增 `search` 参数 | `search` 按标题+正文模糊匹配（原站实测正文命中也返回，如搜「熊小七」total=4 命中 id 17/13/12/10 顺序一致） |
| POST | /api/discover/ant_news/list | 资讯列表 | `{page,list_rows}` | 分页结构；原站该表为空，本项目保持同构（ht_ant_news 空表） |
| POST | /api/discover/ant_news/info | 资讯详情（P037） | `{id}` | `data` 或 `null`；msg `successfully !!!` |
| POST | /api/discover/ant_news/category | 资讯分类 | `{}` | 空数组 |
| POST | /api/discover/rumor_wall/list | 辟谣墙列表 | `{page,list_rows}` | 分页结构（ht_rumor_wall 空表） |
| POST | /api/discover/rumor_wall/info | 辟谣详情（P042） | `{id}` | `data` 含 `profile{avatar,nickname}`；不存在 `null` |
| POST | /api/discover/rumor_wall/getcomment | 评论列表 | `{rumor_id,page,list_rows}` | 分页结构，项含 `profile` |
| POST | /api/discover/rumor_wall/getReply | 回复列表 | `{comment_id}` | 纯数组 |
| POST | /api/discover/rumor_wall/comment | 发表评论 | `{rumor_id,content,parent_id?,reply_user_id?}` | 需登录；成功 msg「评论成功」 |
| POST | /api/discover/rumor_wall/release | 发表辟谣（P041） | `{content,images[],is_anonymous}` | 需登录；成功 msg「发表成功」 |

### 前端页面要点（批次5）
- 公告类型头图映射（原站 `/static/notice/*.png`，已本地化为 `notice-banner-*.png`）：1官方→guanfang / 2运营→yunying / 3活动→huodong / 4寄售→jishou / 5空投→kongtou / 7限价→xianjia / 8合成→hecheng；名称关键词按 空投→寄售→限价→合成/上新→活动→运营→官方 顺序匹配
- 搜索历史：localStorage `notice-history`，最多 10 条、去重、最新在前；聚焦且有历史时显示
- 搜索结果时间格式 `YYYY.MM.DD HH:mm:ss`；类型图标仅映射 上新/活动/限价/寄售，其余 fallback official
- 空数据态对齐：newsDetails（ant_news 空）整页白屏；wallDetails（rumor_wall 空）整页仅居中「加载中...」无导航栏——均与原站实测渲染一致

## 批次6 新增接口（P043-P046 合成域 · 2026-11-08）

| Method | Path | 功能 | 请求 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/synthesis/themeList | 合成主题列表（P043） | `{}` | 数组，项含 `play[]`（玩法：title/image/start_time毫秒/end_time/real_start_time/real_end_time/stock/classify/type/description） |
| POST | /api/synthesis/themeinfo | 主题详情 | `{thm}` | 不存在时 `code:0 msg:"未开启"`（与原站一致） |
| POST | /api/synthesis/playInfo | 玩法详情（P046） | `{play_id}` | 不存在 `msg:"玩法不存在"`；已结束 `msg:"活动已结束"`；成功返回 play 字段 + `rule_text[]` 材料方案 |
| POST | /api/synthesis/material | 活动材料校验 | `{play_id,classify}` | 需登录；未进行中 `code:3003`（前端跳合成详情页）；无资格 `code:4003`（前端 toast「您未获得合成资格」）——错误码与原站 confim 逻辑对齐 |
| POST | /api/synthesis/selectCollection | 用户可选藏品 | `{play_id,classify}` | 需登录；克隆站暂无藏品域返回 `[]` |
| POST | /api/synthesis/synthesizeNow | 立即合成 | `{play_id,grp,classify}` | 需登录；材料不足 `code:0` |
| POST | /api/synthesis/recordList | 合成记录列表（P044） | `{page,list_rows}` | 需登录；分页结构 `{total,per_page,current_page,last_page,data[]}`，项含 `consume[]/method/create_time`；method 10销毁/20保留/30回收 |
| POST | /api/synthesis/recordInfo | 记录详情（P045） | `{record_id}` | 需登录；`data.result[]`（合成结果藏品，首项 image/main_image 为弹窗封面） |

### 前端页面要点（批次6）
- 合成 Tab 状态机（与原站 `checkTimeBetween` 一致）：未开始「即将开始」绿标签 #E8F8EF/#00B578 → 进行中「进行中」蓝标签 #E8F3FF/#2D8CFF → 结束或 stock=0「已结束」灰标签 #EEE/#9AA0AB；非结束态卡片底部显示倒计时（天:时:分:秒，BebasNeue），进行中倒计时盒红底 #FFE8E8/#D93437，未开始灰底 #EEF1F5/#212121
- GO 按钮（home-notice-go.png 50×24）仅「进行中且 stock≠0」响应；已结束点击无反应（与原站实测一致）
- 合成详情页背景为 CSS 渐变（原站 body 实测）：`linear-gradient(#E89294, #F3B6B8 28%, #F7F9FC 62%, #FFF)`；通道 tab 仅「通道A」显示文字并选中 #E60012，通道B/C 空占位（原站模板即如此）
- P046 底部按钮禁用态逻辑与原站一致：材料方案为空时视为已满足（0==0），按钮黑色可用态
- P045/P046 结果弹窗结构：弹窗 264×340（compound-modal-bg.png 整图）+ 右上 X + cover 100×100 r8 + 底部确认热区 180×44

## 批次7 新增接口（P047-P053 水晶/龙晶商城域 · 2026-11-09）

> 原站实际渲染文案为「**龙晶**」（JS 包 navigationBarTitleText 写「水晶」，以实测 DOM 为准）。
> 侦查结论：原站龙晶商城当前**未开放**——`list`/`crystalDetail`/`exchangeRecord` 全部返回 `total=0` 空分页；
> `info` 对任意 id 返回 `code:0 商品不存在`；`exchange` 无操作密码返回「请输入操作密码」。
> 本项目后端保持同构空表，不臆造业务数据；前端默认态 1:1 复刻空态，`?demo=1` 为同构占位数据的结构预览。

| Method | Path | 功能 | 请求 | 响应要点 |
|--------|------|------|------|----------|
| POST | /api/crystal_goods/list | 商品列表（P053） | `{type:"10",page:1,crystal_type:""}` | 分页 `{total,per_page:15,current_page,last_page,data[]}`；msg `successfully !!!`；type 10数字藏品/20盲盒/30实物，crystal_type yellow/purple/blue |
| POST | /api/crystal_goods/info | 商品详情（P047/P048/P049） | `{goods_id,id?,batch_id?}` | 存在时 `data` 含 `goods_name/crystal_type/crystal_number/limited/stock/list_image/main_image/product{}`；不存在 `code:0 msg:"商品不存在" data:null` |
| POST | /api/crystal_goods/crystalDetail | 龙晶明细（P050） | `{page:1,type?}` | 需登录；分页结构，项含 `type(1获取/2消耗)/crystal_number/memo/createtime` |
| POST | /api/crystal_goods/exchangeRecord | 兑换记录（P052） | `{page:1}` | 需登录；分页结构，项含 `goods_name/list_image/crystal_number/num/create_time` |
| POST | /api/crystal_goods/exchange | 立即兑换（P051） | `{goods_id,number,address_id,pay_password}` | 需登录；无密码 `code:0 "请输入操作密码"`；水晶不足 `"水晶不足"`；成功扣减用户龙晶并写明细+记录 |
| POST | /api/crystal_goods/getCardCode | 卡密 | `{}` | 原站空实现，返回 `data:null` |
| POST | /api/user/getUserInfo（扩展） | 龙晶余额 | `{}` | 追加 `crystal_yellow / crystal_purple / crystal_blue`（当前账号均为 0），商城页「我的龙晶」为三者之和 |

### 前端页面要点（批次7，均为原站 DOM 实测值）
- P053 龙晶商城：横幅 375×255（lj_bg.png，100% 100%）；白色导航标题 17px/700 @ (154,10.5)；「我的龙晶」15px 白 @ (54,117) + 图标 21×22 @ (33,112)；数值 40px 白 @ (33,134)；右侧竖排「龙晶规则」29×82 @ (346,122) 半透明黑底、radius 8px 0 0 8px；双入口白卡 309×64 @ (33,197) r11，两图标 41×41 @ (45,208.5)/(187.5,208.5)，文字 14px #0A0A0A
- 空态：empty-data.png 142×142 @ (116.5,333)，「暂无数据」13px #999 @ (161.5,486)
- P050 龙晶明细：u-sticky 104（导航44 + tab60）；三 tab 14px #333，中心 x=82.5/187.5/292.5；蓝色下划线 18×4 #2A51EF @ (73.5,89)；右上角原站 iconfont 未加载显示 16px 白色文本「more-cate」@ (295,66)，按实测保留；空态 142 @ (116.5,304) + 文案 @ (161.5,457)
- P052 兑换记录：空态 142 @ (116.5,260) + 文案 @ (161.5,413)；有数据时白卡 80×80 图 + 名称/数量/时间 + 右箭头
- P051 确认订单：**无商品上下文时整页白屏**（原站实测）；`?demo=1` 预览结构：商品行/收货地址/创作者/区块链地址/订单金额/黄水晶余额/底部「立即兑换」
- P047/P048/P049 详情页：原站 `getDetails` 失败（商品不存在）后 `setTimeout(backPage,1500)` 退回首页，故默认态 1.5s 后返回；`?demo=1` 预览主图、黑色信息卡、STORY、购买须知（原文 3 条，含原站重复编号「3.」）与底部「水晶不足」
- 分包 JS 逆向出的参数名：goods `?id=`；collection `?id=&collection_id=&batch_id=`；box `?id=&blind_box_id=`；exchange 数据来自 `uni.getStorageSync("exchange")` 而非接口
- 商城商品卡字段（分包模板）：`goods_name/limited/crystal_number/crystal_type/stock/exchangeStatus`，状态文案 敬请期待/倒计时/兑换中/已兑完/水晶不足


## 批次8 订单域（P054–P064，2026-11-11 侦查）

> 与批次7 不同：订单域**有真实数据**。原站 `orderList` 返回 4 条真实订单（user_id=173634），
> `orderDetail/ids/1493003` 返回完整订单详情；求购/委托列表为空。已按原字段种入本项目 MySQL
> （`increment_v6_batch8.sql`），默认访问读自建后端，零原站运行时依赖。

| Method | Path | 功能 | 请求体 | 响应/行为 |
|--------|------|------|--------|-----------|
| POST | /api/order/order/orderList | 订单列表（P060） | `{page,status,order_type}` | 分页壳 + 4 条真实订单；status=1/3 空，0/2 返回全部（原站实测） |
| POST | /api/order/order/orderDetail/ids/{id} | 常规订单详情（P056） | `{}` | 完整订单 + `product`/`card`/`surplus_time_text` |
| POST | /api/order/order/saleMarketOrderDetail | 市场订单详情（P061） | `{id}` | 订单详情，collection_code 解码为数组 |
| POST | /api/order/order/giveOrderDetail/ids/{id} | 赠送订单详情（P063） | `{}` | 订单详情 |
| POST | /api/market/trade/begBuyOrderList | 我的求购（P055） | `{page}` | 空分页（total=0） |
| POST | /api/market/trade/begBuyOrderInfo | 求购详情（P054） | `{id}` | 空 id →「订单ID不能为空」 |
| POST | /api/market/Prepayment/myOrderlist | 我的委托（P058） | `{page}` | 空分页（total=0） |
| POST | /api/market/Prepayment/myOrderInfo | 委托详情（P057） | `{id}` | 空 id →「订单ID不能为空」 |

- 无独立接口的 3 页：P064 search（前端按订单号/商品名本地过滤）、P062 orderSubmit 与 P059 firstSaleConfirm（数据来自 `uni.getStorageSync("confirmOrder")`）。
- **storage 关键坑**：uni-app H5 的 `setStorageSync(key, obj)` 实际写入 localStorage 的格式是 `{"type":"object","data":<obj>}`，直接写裸 JSON 会导致页面读到 undefined，渲染出 `￥NaN` / 「订单信息缺失」。mock 侦查必须按此格式注入。
- 默认态照原站：orderSubmit 无上下文显示 `￥NaN` + 「未支持该支付通道收款」；firstSaleConfirm 显示「订单信息缺失」toast + 灰色禁用「立即购买」；marketDetail/regiftDetail 无 id 时 1.5s 退回首页。`?demo=1` 提供有数据态结构预览（占位数据来自原站真实订单快照）。

### 前端页面要点（批次8，均为原站 DOM 实测值）
- P060 订单列表：主 Tab 容器 `274.2×44 @(50.4,0)` 四等分，选中 16px/700 + 红色下划线 `32×3 #D93437`；状态胶囊容器高 68（pill `28h @y64`）；卡片 `335×131 @(17,112)` r12，间隔 10；订单号/时间 10px #9AA0AB、商品图 54×54 r6、商品名 14px/500、状态「空投转入成功」11px #00B578 @(275,157)、实付款 ¥0.00（数字 18px）；底部「已经到底了~」。
- P056 订单详情：盾牌 `90×90 @(20,56)`（order-completed.png）+ 状态文字 18px/700 @(118,88)；白卡 `335×412 @(20,158)` r12，内含商品行、资产编号标签、7 行信息、实付；底部条 `70h @y742`，按钮 `130@(16,755)` + `203@(156,755)` h44 r8。
- P055/P058 求购/委托列表：圆角 Tab 条 `347×40 @(17,44)`，空态箱 `142 @(116.5,166)` + 「目前没有任何数据」。
- P054/P057 求购/委托详情：空壳态为盾牌 + 白卡「订单详情/订单金额:/订单ID号:」；有数据态多商品卡、订单详情卡（含分账ID号/支付方式）、订单状态时间轴、双按钮。
- 图片本地化：`order-completed.png`（红色盾牌 270×270）、`order-icon-copy.png`、`order-clear.png`（原站 `/static/cw/shanchu.png`）、3 张订单商品图；复用已有 `empty-data.png`/`back_btn.png`/`icon-right.png`。


## 批次9 我的资产（P065–P068，2026-11-11 侦查）

> 资产域**有真实数据**。原站 `antMycollection{types:2}` 返回 3 个藏品（持有 4 份），
> `getDetail` 返回每个藏品下的编号实例，`detail/ids/{实例id}` 返回完整藏品详情，
> `likeCollection{keyword}` 返回名称联想。全部按原字段种入本项目 MySQL（`increment_v7_batch9.sql`），
> 默认访问读自建后端，零原站运行时依赖。

| Method | Path | 功能 | 请求体 | 响应/行为 |
|--------|------|------|--------|-----------|
| POST | /api/user_collection/user_collection/antMycollection | 我的藏品列表（P067） | `{types,page,collection_id}` | `types=2` 分页壳 + 3 个藏品（含 `collection` 子对象、`num` 持有数）+ `current_count:4`；`types=1`（盲盒 Tab）total=0 空态 |
| POST | /api/user_collection/user_collection/getDetail | 持仓详情（P066） | `{collection_id,types,page}` | `{list:分页实例[], collection:藏品定义}`；兔耳 2 个编号 #61247/#16044，`from_price/sale_price` 为 null → 前端显示 `--` |
| POST | /api/user_collection/user_collection/detail/ids/{id} | 藏品详情（P065） | `{}` | 单实例 + `product`（name/main_image/total_num/publisher/series）+ 持有者 `user`；不存在 → `code:0「收藏不存在或者已经被删除了」` |
| POST | /api/user_collection/user_collection/likeCollection | 搜索联想（P068） | `{keyword}` | `[{collection_id,name}]`，按名称模糊匹配；空 keyword 返回 `[]` |

- **路由传参坑**：`detail/ids/<ids>` 是路径参数，控制器签名 `detail($ids = 0)` 并兼容 body `id`（对齐 Order.php `orderDetail`）。
- **页面跳转链（原站实测）**：P067 主列表点卡片 → `holdingsDetail?id={collection_id}&types=2`；P066 点编号卡 → `collectionDetails?id={实例id}`（如 2643699）；点「查看寄售市场」停留原地并 toast「藏品不存在」。
- **跨模块跳转（如实记录，不臆造）**：P065 顶部「转赠记录」→ `/pages/regift/record`（批次13 P080，尚未交付，当前点击会白屏）；底部「立即转赠」按钮点击后 URL 不变。
- **默认态与预览态**：4 页默认读自建后端真实数据（与原站一致）；`?demo=1` 提供结构预览兜底（占位数据来自原站真实快照，仅存在于前端 `demo.js` 不入库）。

### 前端页面要点（批次9，均为原站 DOM 实测值）
- P067 主列表：navbar 44px（bg `#F7F9FC`），居中胶囊 `131×32 @(122,6)` bg `#ECEEF2` r16，选中红块 `64×28 @(124,8)` bg `#D93437` r14；「藏品」14px/500 白 /「盲盒」14px/500 `#9AA0AB`；卡片白 r12，图 `80×80 @(32,76)` r8，名称 15px/500 `#212121 @(124,95)`，「库存：N份」12px `#9AA0AB @(124,120)`，右箭头 12×12 @(331,110)；卡片纵向间隔 116px（y=76/192/308）；「下拉刷新」提示 14px #808080。
- P066 持仓详情：navbar 标题 17px/700 居中；藏品图 `80×80 @(16,56)` r8，名称 16px/500 `#212121 @(108,64)`，「藏品总数 2 / 寄售数量 0」12px `#9AA0AB`，「查看寄售市场 >」11px `#9AA0AB @(282,88)`；「编号」12px + 排序箭头 10×10，右侧「寄售优先」12px + 单选圆圈（未选中）；编号卡白 r? 约 `113×80 @(16,176)`，`#61247` 16px/700 @(25,188)、「购入价 ¥ --」「寄售价 ¥ --」10px，两卡 x=25/138。
- P065 藏品详情：顶部主视觉 `375×436` = 藏品主图放大 124% + `filter:blur(28px)` 铺底（**原站真实实现，非渐变**，符合 P0）；白色返回箭头 + 右上「转赠记录」12px 白；标题「2026兔耳」16px/500 @(153,87) 居中；主图 `220×220 @(78,120)` r20；「发行」红标签 `40×24 bg #FF4144` + 数量 `48×24 bg #FFEBEB #D93437`；白卡 `343×62 @(16,406)` r12「藏品持有者 175****1293」；「创作者 火兔」「认证时间 --」白卡；「购买须知」16px/700 + 灰底说明框 `295×250 @(40,668)` r10 12px #666（文案前端写死，原站 API 该字段为空白）；底部黑色按钮 `343×46 @(16,756)` r14「立即转赠」15px/600 白。
- P068 搜索：navbar 44px，白色圆形输入框 `343×36 @(16,64)`（搜索 iconfont 放大镜 + 清除按钮），输入触发 `likeCollection(keyword)`；联想结果每行 50px 14px #555（数字）/ #4A4A4A（中文），x=16 起；空态箱 `142×142 @(117,396)` + 「目前没有任何数据」13px #999 @(135,549)。
- 图片本地化：3 张藏品图沿用订单域已下载的真实图（`coll-tuer.jpg`/`coll-dengjipai.jpg`/`coll-chaoku.jpg`，md5 与 `order-goods-*.jpg` 一致）；图标复用 `back_btn.png`/`back_back_white.png`/`icon-right.png`/`market-sort.png`/`empty-data.png`。


## 批次10 寄售大厅（P069–P071，2026-11-11 侦查）

> `consignments` **不是列表页，是「寄售下单表单」**：`onLoad(A){ var a=A.id, t=A.pageType; ... }`
> 必须带 `id`（用户藏品实例 id，逗号分隔可批量）+ `pageType`；**无 id 时原站整页白屏**（`id.split(",")` 报错），
> 与批次7/8 空数据域一致，本项目默认态如实还原，`?demo=1` 提供结构预览兜底。
> `myConsignment`/`search` 共用同一接口 `/api/order/order/myConsignment`（后者多传 keyword + 三 Tab goods_type），
> 原站该账号寄售订单 total=0 → 恒为空分页壳。

| Method | Path | 功能 | 请求体 | 响应/行为 |
|--------|------|------|--------|-----------|
| POST | /api/user_collection/user_collection/detail/ids/{id} | 寄售表单藏品信息（P069） | `{}` | 复用批次9 接口；product 补全 `reprice_limit`(5.00/5.00/0.00)、`reprice_min_limit`、`platform_resale_rate`(6/6/5)、`price_ratio`(90)、`price_pro` |
| POST | /api/user_collection/user_collection/show_resale_money | 寄售金额试算（P069） | `{price:100,user_collection_id:"2643699"}` | `{minprice:4,price_pro:1,tips,price_price_ratio:90,pay_platform_service_rate:"0.06",share_proportion_rate:"0.00",platform_service_price:"6.00",share_proportion_price:"0.00",real_price:"94.00",low_price:5,low_price_text:"五元"}`；金额按 `price×费率` 动态算 |
| POST | /api/order/order/myConsignment | 我的寄售（P070）+ 寄售搜索（P071） | `{status:"",goods_type:2,page:1}` 或 `{keyword:"2026",status:"",goods_type:"",page:1}` | 统一分页壳 `{total:0,per_page:10,current_page:1,last_page:0,data:[]}`，msg「寄售列表」 |
| POST | /api/market/market/getMarketList | （侦查到，非本批页面调用） | `{keyword,goods_type,page}` | 返回真实寄售市场行情列表（含 min_price/market_value/market_fluctuate），属**批次4 市场域**范畴；本批三页均未调用，如实记录不实现 |

- **搜索页三 Tab（原站实测 DOM）**：`tabList2=[{name:"全部",type:""},{name:"数字藏品",type:"2"},{name:"盲盒",type:"1"}]`，选中色 `#DF0007` 14px/500，未选中 `#ABABAB` 14px；渲染条件 `!showHistory && keyword`（**有输入文字才显示 Tab**）。`search(a)` 仅在确认搜索（回车/点"搜索"）时 `refresh()→getList()`，输入过程不请求。
- **搜索历史**：`uni.getStorageSync('consignments-history')`，`showHistory = history.length && hasFocus`；`setHistory` 最多 10 条、去重 unshift；「清除」走 `removeStorageSync`。存储契约同 uni-app H5：`{"type":"...","data":...}`。
- **列表项字段（原站组件 myConsignmentsItem 引用，如实记录）**：`goods_cover_image / goods_name / price / status(1寄售中·2已售出) / id / user_collection.collection_code / user_collection.total_num`；文案「共 1 件寄售」「取消寄售」「确认取消此寄售？」。原站该账号无数据，本项目 `ht_consignment` 仅建结构不臆造种子。
- **页面跳转链（原站实测）**：P069 由「持仓详情 → 寄售按钮」进入，带 `id={实例id}&pageType`；本批三页彼此无跳转（search 的"搜索"按钮停留原页刷列表）。P069 底部「确认寄售」→ 价格校验 → 二次确认弹窗 → 操作密码弹窗（属提交动作，本项目按原站实现前端交互，不臆造提交结果）。
- **默认态与预览态**：P069 无 `id` 白屏（1:1 原站），带 `?id=2643699` 读真实数据呈现完整表单；P070/P071 默认空态；`?demo=1` 提供有数据态结构预览（占位数据来自原站真实快照，仅前端 `demo.js`，不入库）。

### 前端页面要点（批次10，均为原站 DOM 实测值）
- 三页 navbar 均 44px 白底，返回箭头 `back_btn.png` 深色 `7×13 @(19,16)`，标题 17px/700 居中（P069「寄售」P070「我的寄售」）。
- P069 寄售表单：页面 bg `#F7F9FC`；卡片1 白 `335×195 @(20,56)` r12，藏品图 `75×75 @(32,72)` r8 `bg #F2F4F7`，名称 14px/500 `#212121 @(119,88)`，「买入价」12px `#9AA0AB @(119,114)` + 「¥」14px；「寄售价格」14px/500 `@(32,163)` + 问号图标 `12×12 @(91,167)`；输入框 `311×44 @(32,191)` bg `#F7F9FC` r8，「￥」16px @(44,202) + 占位「请输入寄售价格」14px #9AA0AB @(64,199)。卡片2 白 `335×105 @(20,263)` r12「收款账号」14px/500 @(32,275)，红圆「A」`20×20 @(40,322)` bg `#D93437` r50% +「汇付钱包」14px @(68,327)，右侧红色勾选 20×20 @(315,322)。卡片3 白 `335×110 @(20,392)` r12「预计收入」14px/500 @(32,408)，「综合服务费 (6%)」12px `#787F8D @(32,441)` /「- ¥0.00」14px `#212121 @(312,440)`，「预计收益」12px /「¥0.00」18px `#D93437 @(311,465)`。底部按钮 `343×52 @(16,751)` r14 bg `#C2C5CC`（未输入时禁用灰）「确认寄售」16px/500 白。
- P070 空态：空态图 `142×142 @(117,144)`（原站 `/static/huotu/empty-data.png`，批次9 已本地化）+「目前没有任何数据」13px `#999 @(135,297)`。
- P071 搜索：navbar 无标题，圆角搜索框 `287×34 @(54,5)` bg `#F2F2F2` r100（含放大镜 `consign-search.png` + 占位「请输入搜索内容」+ 清除按钮），右侧「搜索」文字 14px @(341,19)；输入后三 Tab「全部」@(76,44) /「数字藏品」@(162,44) /「盲盒」@(276,44)；有搜索历史时显示历史词 +「清除」。
- 图片本地化：藏品主图沿用批次9 的 `coll-tuer.jpg`/`coll-dengjipai.jpg`/`coll-chaoku.jpg`（原站 `/uploads/20261107/` `/uploads/20261103/`）；新增下载 `consign-q.png`（问号，原站 `/static/icon/q.png`）、`consign-search.png`（放大镜，原站 `/static/icon/search3.png`）；空态图复用 `empty-data.png`，返回箭头复用 `back_btn.png`。


## 批次11 盲盒（P072–P076，2026-11-12 侦查）

> **5 页里只有 `openRecord` 与 `blindBoxDetails` 发业务 API，其余 3 页纯前端 storage 驱动**：
> `index` 读 `blindBoxInfo`、`result` 读 `blindBoxResultList`、`result2` 读 `blindBoxResult`
> （uni-app H5 storage 契约 `{"type":"object","data":<obj>}`，与批次8/10 一致）。
> `blindBoxDetails` 复用批次9 的 `detail/ids/{id}`，**无 id 时原站整页白屏**（`detail/ids/undefined`
> 返回「收藏不存在或者已经被删除了」）；带真实实例 id `2643699` 呈现完整藏品详情。
> 与批次9 `collectionDetails` 的差异：**多一个「流通」徽标**（`product.flux`，快照 = 0），
> 且 `product.collectionlist` 有值时额外渲染「数字藏品描述 / 您可能抽到以下作品中的 1 个」区块
> （概率 + 总量 + 稀有度图标）—— 原站该快照 `collectionlist=null`，故区块不渲染。

| Method | Path | 功能 | 请求体 | 响应/行为 |
|--------|------|------|--------|-----------|
| POST | /api/box/blind_box/openlog | 开盒记录（P074） | `{blind_box_id,page:1,list_rows:20}` | 分页壳 `{total:0,per_page:"20",current_page:1,last_page:0,data:[]}`，msg「开盲盒记录」；有数据项字段 `id/createtime/userss.nickname/collections.name`（前端 `formatDate` + 「抽中」） |
| POST | /api/user_collection/user_collection/openbox | 逐个拆开（P073 按钮） | `{openid}` | 原站对无可开盲盒返回 `code:0「没有啦」`；本项目按 `ht_user_blind_box` 判定，有可开盲盒返回 `{name,main_image,collection_code,rarity_icon}` |
| POST | /api/user_collection/user_collection/openBoxs | 一键开启（P073 按钮） | `{openid}` | 原站重复提交返回 `code:0「请勿重复提交」`；有可开盲盒返回数组（一次最多 10 个），项字段 `{name,listimg,collection_code,rarity_icon}` |
| POST | /api/box/blind_box/BoxDetail/ids/{id} | 盲盒商品详情（侦查到，本批 5 页均未调用） | `{origin,crystal_goods_id}` | 原站对 id=33/20 均返回 `code:0「找不到该盲盒，或者已经下架了」`，本项目如实同构返回，不臆造详情数据 |
| POST | /api/box/blind_box/list | 盲盒列表（批次2 首页发售日历已用） | `{page,list_rows}` | 复用 `Index.php::blindBoxList` 读 `ht_goods`；原站真实 2 条：id=33「2026泡泡探险」11.11、id=20「2026登机牌」3.98。**本批未改动此接口** |
| POST | /api/user_collection/user_collection/detail/ids/{id} | 盲盒详情（P072，复用批次9） | `{}` | 复用 `Collection.php::detail`；`product` 补 `flux`（流通量，快照 0），供 P072 第二个徽标 |

- **开盒动作交互链（原站实测 chunk `9ec9`）**：
  `index`「一键开启」→ `openBoxs(info.blindBoxId)` → `setStorageSync("blindBoxResultList", data)` → `redirectTo(/pages/blindBox/result)`；
  「逐个拆开」→ `openBox(info.blindBoxId)` → 黑色全屏遮罩 5 秒 → `setStorageSync("blindBoxResult", data)` → `redirectTo(/pages/blindBox/result2)`；
  「立即寄售」→ `route(/pages/consignments/consignments, {id: info.id, pageType:1})`（批次10 已完成，真实可跳）。
- **结果页按钮跳转差异（原站 render 实测）**：`result`「我的数字藏品」= `navigateBack`；`result2`「我的数字藏品」= `redirectTo(/pages/myCollection/myCollection)`；两者「继续购买」= `switchTab(/pages/index/index)`。
- **结果页文案差异（一字不差还原）**：`result` 列表项用半角「藏品编号: #61247」，空态提示「商品陆续从链上转入个人中心，如有延迟请耐心等待」；`result2` 用全角「藏品编号：#61247」，无提示语。
- **默认态与预览态**：5 页默认均按原站（storage 无值 / 分页壳空 / 无 id 白屏）；`?demo=1` 仅前端 `pages/blindBox/demo.js` 结构预览（占位值来自原站真实快照），不入库。

### 前端页面要点（批次11，均为原站 DOM 实测值）
- 五页 navbar 均 44px 白底（`openRecord` 与页面同色 `#F4F6FA`），返回 `7.3×13 @(19,15.5)`，标题 17px/700 居中。
- **P073 我的盲盒**：页面 bg `#F7F9FC`；灰占位方块 `240×240 @(68,135)` 白边 4px r20 + 阴影，内图 `232×232 @(72,139)` r16 bg `#F5F5F5`；「共有 N 个」白胶囊 h28 r14，12px `#787F8D` + BebasNeue 18px `#D93437`；「一键开启」`250×44 @(63,435)` `#D93437` r14 16px 白；提示 11px `#9AA0AB @(132,495)`；底部固定栏 h62 白底，两按钮 flex:1 `h44` 间距 10px（立即寄售白底 1px `#212121` 边 / 逐个拆开红底）。
- **P072 盲盒详情**：主视觉 375×436，主图放大 124% + `blur(28px)` 铺底 + `rgba(255,255,255,0.04)` 遮罩；右上「转赠记录」12px 白 `@(321,16)`；标题 16px/500 `@(153,87)`；主图 `220×220 @(78,120)` r20；**双徽标 `@y=354`**：发行 `#FF4144` + `#FFEBEB/#D93437`（x=106 w88）、流通 `#212121` + `#CDCCD5`（x=206 w63），BebasNeue 14px；白色内容区 `margin-top:-30px`，白卡 r14（持有者 62px / 创作者+认证时间 124px 带分割线 / 购买须知）；底部「立即转赠」黑 `#313131`。
- **P074 开盒记录**：页面 bg `#F4F6FA`；空态 `empty-data.png 142×142 @(117,60)` + 「目前没有任何数据」13px `#999 @(135,213)`；有数据行 `padding:12px 16px`，左昵称 14px + 日期 12px，右「抽中」+ 藏品名 14px。
- **P075 盲盒结果**：页面白底，content `345px 居中` padding 13px；列表项白卡 `319×75 @(28,57)` r8，图 `59×59 @(36,65)` r2，名称 13px/500，藏品编号 12px（数字 BebasNeue 12px `#999`），稀有度图标 `59×55 @(280,67)`；提示 10px `#7A7A7A` 居中；footer h85 bg `#F4F6FA`，双按钮 `160×46 @y=747` gap 20px（左 `#F1F0F4` r25 / 右 `#313131` r12）。
- **P076 盲盒结果2**：稀有度图标 `80×59 @(148,96)`，主图 `200×200 @(88,171)` r5，名称 16px/500 `#555` 居中，藏品编号 12px `#999`；footer 白底、按钮 gap 10px（与 P075 的 20px 不同，`mr-10` vs `mr-20`）。
- **图片本地化（全部取自原站）**：新增 `bb-paopao.jpg`（`/uploads/20261108/b15c345d…`，2026泡泡探险）、`bb-dengjipai.jpg`（`/uploads/20261103/88748beb…`）、`bb-rarity-icon.png`（`/static/cw/rank_icon_1.png` 稀有度）；复用已有 `coll-tuer.jpg`、`empty-data.png`（md5 与原站一致）、`back_btn.png`。数字字体复用全局 `assets/fonts/BEBASNEUE-1.OTF`（注册名 **'Bebas Neue'**，带空格）。


---

## 批次12 商品详情（P077-P079）

> 侦查来源：原站 chunk `pages-product-box.63e86ee4.js` / `pages-product-collection.e1777387.js` / `pages-product-notice.a80836db.js`；
> 直连原站 `https://api.hnyiqin.asia/api/...`，header 带 `token` + `version: 1.0.1`。

### 原站契约

| 页面 | 路由 | 原站接口 | 请求 | 原站实测响应 |
|------|------|----------|------|--------------|
| P077 盲盒详情 | `/pages/product/box?id=<id>` | `POST /api/box/blind_box/BoxDetail/ids/<ids>?origin=<o>&crystal_goods_id=<c>` | `{}` | 当前账号对 id 1-40/50/60/100/2643699 均 `code:0「找不到该盲盒，或者已经下架了」`；chunk 内 `getDetails` 失败后 `setTimeout(backPage, 1500)` |
| P078 藏品详情 | `/pages/product/collection?id=<id>&batch_id=<b>` | `POST /api/box/collection/ProductDetail/ids/<ids>?batch_id=<b>` | `{}` | `code:1「商品详情」` + 完整 data |
| P079 相关公告 | `/pages/product/notice?keyword=<商品名>` | `POST /api/index/afficheList` | `{"search":"<keyword>","page":1}` | 分页 `{total,per_page,current_page,last_page,data}` |

- **错误码（原站实测）**：`ProductDetail/ids/33` 无 batch → `code:0「参数错误」`；`ids/20?batch_id=4` → `code:0「找不到是哪个批次」`；`ids/33?batch_id=4` 与 `ids/20?batch_id=3` → `code:1`。
- **入口参数来源**：首页 chunk `pages-index-index` 的 `goRecommendDetail(a)`：`1==a.goods_type` → `/pages/product/box?id=<id>`，否则 → `/pages/product/collection?id=<id>&batch_id=<a.batch_id>`。发售日历两条均 `goods_type=2`（id 33 / batch 4「2026泡泡探险」，id 20 / batch 3「2026登机牌」），故当前首页只进 P078。
- **相关公告入口**：`box` / `collection` chunk 的 `goRelatedNoticeSearch()` → `/pages/product/notice?keyword=<product.name>`；无 keyword 时页面**不发起任何列表请求**（原站纯 navbar 空态）。
- **公告搜索结果**：`search=2026登机牌` → 8 条（原站返回含大量非关键词命中的关联公告，并非简单 LIKE）；`search=2026泡泡探险` → `total=0` 空态。

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据源 | 说明 |
|--------|------|--------|--------|------|
| POST | `/api/box/collection/ProductDetail/ids/<ids>?batch_id=<b>` | `Product::productDetail` | `ht_product` | 完整商品详情快照，含三种错误码 |
| POST | `/api/product/relatedNotice` | `Product::relatedNotice` | `ht_product_notice` | `{search,page}`，按原站真实返回快照分页 |
| POST | `/api/box/blind_box/BoxDetail/ids/<ids>` | `BlindBox::boxDetail`（批次11 已有，未改动） | — | 如实返回「已下架」 |

> **路径差异说明**：原站 P079 走 `/api/index/afficheList`，但该路径在批次5 已用于发现页公告列表，且原站 `search` 返回的 8 条与 `ht_affiche` 按 LIKE 检索结果不一致。为不改动已完成接口、且不臆造搜索逻辑，本项目改用 `/api/product/relatedNotice` 返回原站真实快照；前端页面视觉与交互完全一致。

### 前端数据契约
- `pages/product/demo.js`：`routeQuery()` / `isDemo()` / `localImg()` / `formatNum()` / `fmtDate()`（固定 UTC+8，避免浏览器时区导致日期偏差）/ `BUY_NOTICE`（原站 chunk 内硬编码购买须知正文）。
- 图片全部本地映射：复用 `bb-paopao.jpg`、`bb-dengjipai.jpg`、`empty-data.png`、`mine-default-avatar.png`、`back_btn.png`；新增 `detail-unfollow.png` / `detail-notice.png` / `detail-market.png`（从 `h5.hnyiqin.asia/static/huotu/` 下载，60×60 RGBA）。
- **box 默认态**：调 `BoxDetail` 失败 → 显示提示并 1.5s 后回到首页（还原原站 `backPage`）；`?demo=1` 用本项目 `ht_product` id=33 真实快照渲染完整结构（与批次11 demo 态同理）。像素对比基准为原站「注入真实 ProductDetail 数据」的渲染态。
- **notice 三态**：无 keyword 纯 navbar / `keyword=2026登机牌` 8 条列表 / `keyword=2026泡泡探险` 空态「目前没有任何数据」。

### 交互链
- 三 Tab：「公告」→ `/pages/product/notice?keyword=<商品名>`；「市场」→ `/pages/market/market`；「关注」保留占位。
- 底部：P078「已售罄，去市场看看」→ 市场；P077「¥<price> 立即抢购」为下单面板占位。


---

## 批次13 转赠（P080-P081）

> 侦查来源：原站 chunk `pages-regift-record.e129bcde.js` / `pages-regift-regift.1dbb3414.js`；
> 直连原站 `https://api.hnyiqin.asia/api/...`，header 带 `token` + `version: 1.0.1`。

### 原站契约

| 页面 | 路由 | 原站接口 | 请求 | 原站实测响应 |
|------|------|----------|------|--------------|
| P080 转赠记录 | `/pages/regift/record` | `POST /api/user/aboutGift` | `{"page":1,"types":1\|2}` | `code:1「转赠记录」` 分页 `{total:0,per_page,current_page,last_page,data:[]}`，两个 Tab 均空态 |
| P081 转赠 | `/pages/regift/regift?id=<藏品实例id>` | `POST /api/user_collection/user_collection/detail/ids/<ids>` | `{}` | `code:1「详情」`（复用批次9 已有接口）；无 id 时 `ids/undefined` → `code:0「收藏不存在或者已经被删除了」`，chunk 内 1.5s `backPage()` 回首页 |
| P081 选择用户 | 同上页 | `POST /api/user/ParamsToUser` | `{"phone":"<手机号>"}` | `code:1「用户列表」`，data 为**单个对象** `{id,nickname,avatar,mobile(脱敏),ntf_url,nft_url,url}`；查无此人 data 为 null，前端 toast「用户不存在」 |
| P081 确认转赠 | 同上页 | `POST /api/user_collection/user_collection/reGiftOrder` | `{"pay_password":"123456","re_user_id":173634,"user_collection_id":"2643699"}` | `code:0「密码错误」`（原站实测；该账号未设置/未知操作密码） |

- **入口来源**：`pages-myCollection-collectionDetails` 与 `pages-blindBox-blindBoxDetails` 的 navbar 右侧「转赠记录」→ `$u.route("/pages/regift/record")`；底部「立即转赠」→ `/pages/regift/regift?id=<实例id>`。本项目批次9/11 已完成页**已预埋 `toRegiftRecord()` 指向该路由**，本批零改动即联动（回归实测两个入口均正确跳转）。
- **onLoad 参数**：`onLoad(A){ this.userCollectionId = A.id }`，`onShow` 调 `getUserCollectionDetails(id)`；搜索输入 `$u.debounce(searchUser, 500)`；确认弹窗 `u-popup` + `u-code-input(maxlength:6, dot:true, borderColor:transparent)`；`submit()` 组装 `form.re_user_id` / `form.user_collection_id` / `form.pay_password`。
- **文案**：原站 chunk 硬编码「转增」（非「转赠」），如实保留未改写；须知 3 条、温馨提示、24 小时未领取退还提示见 `demo.js`。
- **Tab 契约**：`tabs:[{name:"我转赠的",type:1},{name:"我收到的",type:2}]`，`search:{page:1,types:1}`；切 Tab 重新请求 `types=2`。列表项字段（chunk 内，原站当前 0 条不渲染）：`product.{name,listimg,total_num}`、`usercollection.{types,collection_code}`、`{createtime}`、`user.mobile` / `from_user.mobile`，两行「转赠时间/转入时间」「接收人/转赠人」。

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据源 | 说明 |
|--------|------|--------|--------|------|
| POST | `/api/user/aboutGift` | `Regift::aboutGift` | `ht_gift_record` | 按 `user_id + types` 分页，当前 0 条 |
| POST | `/api/user/ParamsToUser` | `Regift::paramsToUser` | `ht_user` | 按 `mobile` / `username` 匹配，`mobile` 脱敏为前 6 位 + `*****` |
| POST | `/api/user_collection/user_collection/reGiftOrder` | `Regift::reGiftOrder` | `ht_user.pay_password` | 真实校验本地支付密码；当前账号未设置，故如实返回「密码错误」，**不臆造成功路径** |
| POST | `/api/user_collection/user_collection/detail/ids/<ids>` | `Collection::detail`（批次9 已有，未改动） | `ht_user_collection_item` | 直接复用，返回 `product.name/listimg` 与 `user.nickname` |

### 前端数据契约
- `pages/regift/demo.js`：`routeQuery()` / `localImg()` / `fmtDate()` / `GIFT_TITLE` / `GIFT_NOTICE[3]` / `GIFT_TIP` / `SEARCH_PH`。
- 图片全部本地映射：复用 `coll-tuer.jpg`（2026兔耳粉色礼帽主图）、`empty-data.png`（转赠记录空态插画）、`back_btn.png`；新增 `defaut-avatar.jpg`（原站 `u-avatar` 默认头像，为 chunk 内 **base64 内联 JPG**，三个域名直连均 404，故从原站 DOM 抓 `img.src` 保存，仍属原站下载）。
- **不臆造 demo 列表态**：原站 `aboutGift` 两 Tab 均 `total=0`，空态即真实行为；本批 5 组像素对比全部采用原站真实态（记录默认 / 我收到的 / 转赠表单 / 搜索用户 / 操作密码弹窗），未构造任何假记录。
- **regift 无 id**：调 `detail` 失败后 1.5s 回首页（还原 `backPage`）；`?id=2643699` 渲染完整表单。

### 交互链
- 输入手机号 → 500ms 防抖 → `ParamsToUser` → 用户卡片（默认圆形头像 + 脱敏昵称）；查无此人 toast「用户不存在」。
- 「确认转赠」→ 操作密码弹窗 → 输满 6 位 → `reGiftOrder` → toast 后端 msg；未选用户时提交直接关闭弹窗（还原原站 `if(!this.user.id) return`）。


---

## 批次14 钱包（P082）

> 侦查来源：原站 chunk `pages-wallet-wallet.35f56741.js`（模块 `f410` 页面 / `6fe5` 接口层）；
> 直连原站 `https://api.hnyiqin.asia/api/...`，header 带 `token` + `version: 1.0.1`。

### 原站契约

| 页面 | 路由 | 原站接口 | 请求 | 原站实测响应 |
|------|------|----------|------|--------------|
| P082 我的钱包 | `/pages/wallet/wallet` | `POST /api/user/getUserInfo` | `{}` | `code:1「个人信息」`，`data.huifu_account = 1` → `openstatus=true` → 按钮「进入钱包」 |
| P082 进入钱包 | 同上页 | `POST /api/wallet/huifu/wallet` | `{}` | `code:1「请求成功」`，`data{openstatus:true, info{sys_id,extension,acct_id,resp_desc:"成功",resp_code:"C00000",mer_priv,user_cust_id,redirect_url:"https://hfpay.cloudpnr.com/h5/?uuid=...",mer_cust_id:"6666000243803635",biz_product_id}}` |
| P082 开通钱包 | 同上页 | `POST /api/wallet/huifu/openAccount` | `{}` | `code:0「您已经开户了」` `data:null`（该账号已开户） |
| P082 服务费下单 | 同上页 | `POST /api/order/pay/CreateHuifuAccountOrder` | `{}` | `code:0「您已经支付开户手续费，请勿重复操作」` `data:null` |
| P082 钱包信息 | 同上页 | `POST /api/user/getWalletInfo` | `{}` | `code:0「尚未开户」` `data:null` |

- **页面定位**：引导中转页。`walletList` 仅 1 项「汇付天下」（`key:huifu` / `authKey:huifu_account`）；卡片上的「汇付天下 / 免费开通 / 无需开户费+超低手续费」**烘焙在 `wallet-card-bg.png` 图片内**，模板不渲染这三段文字（本项目同样不渲染，避免多余 DOM 文本节点）。
- **状态机（chunk f410）**：`onShow → initInfo() → getUserInfo() → walletList.map(w => w.openstatus = !!res[w.authKey])`；`activateButtonText = openstatus ? "进入钱包" : "开通此钱包"`。
- **交互链（chunk f410）**：卡片图 / 加号图 / 底部按钮**三个区域同走 `action(w)`** → 已开户 `toWalletPage()` 调 `getHuiFuInfo` 取 `redirect_url` → `toWeb()` 即 `window.location.href` 跳汇付第三方 H5；未开户 `openHuiFuWallet()` 调 `openAccount`，有 `mer_cust_id` 则跳，否则弹服务费确认；其他 key toast「敬请期待对接中」。
- **图片契约（原站实测 URL）**：`/static/huotu/wallet-card-bg.png`（852×432，显示 284×144 aspectFill）、`/static/huotu/wallet-add-btn.png`（120×300，显示 40×100 aspectFit）、`/static/cw/back_btn.png`（26×46，显示 7×13）。三张均已原站下载本地化。
- **布局 Token**：容器 `#f7f9fc`；navbar 占流 44px、标题 17px/700 纯黑；内容区 `padding 24rpx 40rpx 180rpx`；卡片 568rpx×288rpx，行内 gap 16rpx；底部 fixed 栏 `padding 20rpx 40rpx`，按钮 88rpx/44px、圆角 24rpx/12px、`#212121`、文字 32rpx/16px `Alibaba-PuHuiTi-Medium` 白色。
- **第三方边界**：`redirect_url` 指向汇付天下 H5（`hfpay.cloudpnr.com`），不属于 113 页清单且依赖外部会话。本项目**不自动跳转外站**（独立部署、运行时零原站/第三方依赖），改为展示本地接口返回的真实快照结果卡 + 可选新窗口打开链接。

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据源 | 说明 |
|--------|------|--------|--------|------|
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（批次1 已有，**零代码改动**） | `ht_user.huifu_account`（增量 SQL 加列） | `auth()` 取整行，新增列自动带出 `huifu_account=1` |
| POST | `/api/wallet/huifu/wallet` | `Wallet::walletInfo` | `ht_wallet_huifu` | 返回原站同构 `openstatus + info` |
| POST | `/api/wallet/huifu/openAccount` | `Wallet::openAccount` | `ht_user.huifu_account` | 已开户如实返回「您已经开户了」，不臆造成功路径 |
| POST | `/api/order/pay/CreateHuifuAccountOrder` | `Wallet::createAccountOrder` | — | 如实返回「您已经支付开户手续费，请勿重复操作」 |
| POST | `/api/user/getWalletInfo` | `Wallet::getWalletInfo` | — | 如实返回「尚未开户」 |

### 前端数据契约
- `pages/wallet/demo.js`：`IMG`（3 张本地图）/ `routeQuery()` / `isUnopenDemo()` / `WALLET_LIST`（chunk 原样）/ `TXT`（chunk 硬编码文案）。
- `?unopen=1` 为**本地分支验收开关**：只强制 `openstatus=false` 以展示 computed 文案「开通此钱包」，接口仍走真实本地数据，不构造假记录。
- 卡片图 / 加号图 / 底部按钮三处均可点击（回归实测 `panel=true` ×3）；返回箭头 `router.back()`。


---

## 批次15 签到（P083）

> 侦查来源：原站 chunk `pages-sign-index.07b21d92.js`（页面模块 `9b1b` / 接口层模块 `5f3a` 位于 `index.8db961e4.js`）；
> 直连原站 `https://api.hnyiqin.asia`，header 带 `token` + `version: 1.0.1`。
> **路径特殊**：接口层 `needBaseUrl:false`，签到三接口均**不带 `/api` 前缀**（发布域已实测转发，见文末）。

### 原站契约

| 页面 | 路由 | 原站接口 | 请求 | 原站实测响应 |
|------|------|----------|------|--------------|
| P083 签到配置 | `/pages/sign/index` | `POST /addons/signin/api.index/index` | `{}` | `code:1, msg:"", time:1794588752`，`data{signinscore{s1..s7,sn},isfillup:1,fillupscore:100,fillupdays:3,ranklist(10 条),successions:0,is_signin:false,self_rank:0,score:3,msg:"今天签到可获得 1 积分,请点击签到领取积分"}` |
| P083 月份记录 | 同上页 | `POST /addons/signin/api.index/monthSign` | `{}` | `code:1, msg:"", data:{"04":"04","05":"05"}`（4/5 号已签） |
| P083 月份切换 | 同上页 | `POST /addons/signin/api.index/monthSign` | `{"month":"2026-10"}` | 返回**同一份** `data:{"04":"04","05":"05"}`（原站实测行为；页面另弹「获取签到记录失败」瞬时 toast，非页面结构） |
| P083 用户信息 | 同上页 | `POST /api/user/getUserInfo` | `{}` | `data.id=173634`, `nickname="175****1293"`, `avatar="/defautAvatar.png"` |
| P083 立即签到 | 同上页 | `POST /addons/signin/api.index/dosign` | `{}` | **未实测**（见下方边界说明）；chunk 仅判断 `code==1` |

- **页面定位**：签到日历页。视觉由 1 张全屏背景图 + 2 张箭头 + 返回箭头 + 默认头像 + 弹窗关闭图 + 纯 CSS 卡片/日历格构成；「签到有礼」是 40px/700 `#E76264` 文字（非图片）。
- **状态机（chunk 9b1b）**：
  - `successions = config.successions ?? userInfo.successions`（原站 config=0，用户信息=5，页面显示 **0**）；
  - `hasSignedToday = config.is_signin`（`true|1|"1"`）；
  - 右侧文案 `已签到 / 未签到` + `请继续保持~ / 快去签到吧!`；
  - 按钮文案 `今日已签到 / 立即签到`，已签到时背景 `#C2C5CC`、无阴影且点击直接 return；
  - 月份标题 `calendarYear年calendarMonth月`；`canGoNextMonth` 不允许超过服务器当前月。
- **日历生成（`rebuildCalendarItems`）**：前置补上月尾（`当月1号星期几`）、当月 1..月末、后置补下月头至 7 的倍数；`isToday` 显示「今」；`isFuture` 无状态文字；`isSigned` →「已签到」；过去未签 →「未签」；其他月只显示数字。
- **格子样式（`dayInnerStyle` / `dayNumStyle`）**：已签到 `#D93437` 白字；未来/其他月/空位 `rgba(255,255,255,0.6)`（未来数字 `#9AA0AB`、其他月 `#C2C5CC`）；过去未签 `#C2C5CC` 白字。格子 40×50 圆角 8px，横向 gap 2px；**星期表头列间距 40px（无 gap），与日历格 42px 刻意不对齐**——原站实测，已如实复刻。
- **交互链**：「活动规则」→ `ruleText` 为空 → toast「暂无活动规则」；左/右箭头 → `monthSign({month})` 重建日历；「立即签到」→ `sign()`，`code==1` 弹「签到成功」白卡（标题 20px Bold / 红色「我知道了」按钮 / 下方 X 关闭图）并 `loadPageData()`。
- **图片契约（原站实测 URL，均已下载本地化）**：`/static/huotu/checkin-bg.png`（1125×2685，显示 375×812 aspectFill）、`checkin-arrow-left.png` / `checkin-arrow-right.png`（60×60，显示 20×20）、`mine-default-avatar.png`（44×44）、`/static/cw/back_btn.png`（26×46，显示 7×13）、`common-modal-close.png`（72×72，显示 24×24）。
- **布局 Token**：用户卡 (20,124) 335×114 圆角 20px `rgba(255,255,255,0.48)`；日历卡 (20,250) 335×445 圆角 20px + `0 4px 16px rgba(120,127,141,0.08)`；底部按钮 (20,719) 335×48 圆角 14px `linear-gradient(90deg,#D93437,#F06A6C)` + 红色投影；连续签到数字 `Bebas Neue` 24px `#D93437`。
- **快照基准**：原站按 GMT+8 判定「今」，顶层 `time=1794588752` → 2026-11-14 00:52:32。本项目把基准锁在 `demo.js SNAPSHOT`（2026/11/14）与 DB `snapshot_time`，**不用浏览器当前时间**，避免沙箱 UTC 漂移到 13 号。

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据源 | 说明 |
|--------|------|--------|--------|------|
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（批次1 已有，**零代码改动**） | `ht_user`（含新增 `origin_uid`） | `auth()` 取整行，新列自动带出原站真实 UID 173634 |
| POST | `/addons/signin/api.index/index` | `Sign::indexConfig` | `ht_sign_config.config_json` | 原站 data 原样 JSON 存库、`json_decode` 后原样返回，顶层 `msg:""` / `time` 用快照值 |
| POST | `/addons/signin/api.index/monthSign` | `Sign::monthSign` | `ht_sign_month.days_json` | 忽略 `month` 差异（如实复刻原站实测），返回 `{"04":"04","05":"05"}` |
| POST | `/addons/signin/api.index/dosign` | `Sign::doSign` | 不读表 | 返回 `code:1, msg:"签到成功"`；前端内存态更新，DB 快照永不改写 |

### 边界说明（如实登记，不臆造）

1. **dosign 未对原站账号执行真实签到**：一旦调用会让原站「今日已签到」，用户逐批验收时打开原站会与 Clone 默认态（未签到）不一致。故按 chunk 契约返回等价 `code:1`，页面状态在前端内存更新（弹窗 + 按钮变灰 + 今天标已签），**刷新后回到侦查快照态**。与批次14「本地结果卡替代第三方跳转」同一处理模式。
2. **发布域 `/addons` 路径**：`vite.config.js` 已有 `/addons` 代理；发布域页面内 `fetch('/addons/signin/api.index/index')`（不带 sharecode、body 带 token）实测正常转发，4 个接口全部返回本地后端数据。
3. **原站切月 toast**：点击左箭头后原站偶发弹「获取签到记录失败」，属瞬时错误提示、非页面结构；采集脚本已延长等待至其消失，对比时按系统文本 SKIP。

### 前端数据契约
- `pages/sign/demo.js`：`IMG`（6 张本地图）/ `SNAPSHOT`（2026,11,14,1794588752）/ `WEEKS` / `TXT`（chunk 硬编码文案）/ `buildCalendarItems()` / `cellClass()` / `statusText()` / `shiftMonth()` / `canGoNextMonth()` / `parseSignedDays()`。
- 交互验收：活动规则 toast、左/右箭头切月、立即签到成功弹窗与状态联动、我知道了关闭，本地与发布域均实测通过。


---

## 批次16 邀请域（P084-P085）

> 侦查来源：原站 chunk `pages-invitation-invitation.243bf971.js`（页面模块 `4d99`）/
> `pages-invitation-invitationrecord.2d8b2c65.js`；接口层模块 `5f3a`（位于 `index.8db961e4.js`）。
> 直连原站 `https://api.hnyiqin.asia`，header 带 `token` + `version: 1.0.1`。

### 原站契约

| 页面 | 路由 | 原站接口 | 请求 | 原站实测响应 |
|------|------|----------|------|--------------|
| P084/P085 用户信息 | 两页 | `POST /api/user/getUserInfo` | `{}` | `code:1`，`data.inviter_code = "HT173634"`、`id = 173634` |
| P084/P085 好友列表 | 两页 | `POST /api/user/ranking` | `{"types":2}` | `code:1, msg:"查询成功", time:1794619570`，`data{data{my_list:[],vip_time_num:0,real_name_num:0,yx_num:0,total_num:0,buy_num:0},my:"",activityRule:""}` |
| P084 二维码 | 邀请活动 | `GET /addons/qrcode/index/build?text_type=h5&text=<inviter_code>&t=<ts>&url=<当前页 fullPath>` | — | 返回 `image/png` 二进制（330×330），显示 36×36 |
| P084 助力 | 邀请活动 | `POST /api/user/invite` | `{"inviter_code":"<码>"}` | 无效码安全探测：`code:0, msg:"活动未开启", data:null`（未建立真实邀请关系） |
| P084/P085 分享配置 | 两页 | `POST /api/index/getConfig` | `{"key":"sharebg,copyright,sharebtn1,sharerulebtn,sharerulebg,share_bac_iamge,invitation_bg_color"}` | `code:1`，`data{sharerulebtn,sharebg,share_bac_iamge,sharerulebg,invitation_bg_color:"#03135b",sharebtn1,copyright}`（海报绘制用，默认态不渲染） |

- **页面定位**：P084 邀请活动 = 引导/分享页（邀请码卡 + 二维码 + 助力好友 + 好友列表空态）；P085 邀请记录 = 统计页（0 总人数 + 好友列表空态）。两页均 `#f7f9fc` 底色 + 白色圆角卡片 + `empty-data.png` 粉色空箱插画。
- **P084 状态机（chunk 4d99）**：
  - `inviteTab: "code" | "link"`（默认 code）；Tab 选中 `#212121` 白字 / 未选 `transparent` `#787f8d`，代码切 `v-show`。
  - `displayInviterCode = userInfo.inviter_code || "--"`（BebasNeue 48rpx）；`invitaion_code` = 二维码动态 URL。
  - `h5_invite_url = config.info.h5_invite_url + "&content=" + inviter_code` → **原站实测该配置未下发，真实渲染 `undefined&content=HT173634`**（本项目如实复刻，不臆造补全）。
  - `inviteTotalCount = data.total_num ?? 0`（ranking 空态为 0）；好友列表标题 `好友列表（0人）`。
  - `goAssist()`：空码 → toast「请输入邀请码」；有码 → `inviteByCode({inviter_code})` → `toast(res.msg || "助力成功")` → 清空输入 + `getList()`。按钮 `isAssistFilled ? #d93437 : #c2c5cc`。
  - `copyInviterCode()` / `copyInviteUrl()` → `uni.setClipboardData`；搜索框 `friendKeyword` 本地过滤 `my_list`（空态时始终为空）。
  - `goInviteRecord()` 在 chunk 中定义但**模板未绑定任何点击**（死方法）；navbar 仅 `title:"邀请活动"` + `bgColor`，**无右侧入口**。故 P084 到 P085 无页面内跳转，两页各自独立验收。
- **P085 状态机**：`displayTotalNum = total_num`（BebasNeue 80rpx「0」）+「总人数」；`list = my_list` 空 → `empty-data.png`；列表项模板（昵称/Cell 脱敏手机/时间/已实名·未实名/已购买·未购买）由 chunk 提供，本项目同结构实现但侦查态不渲染。
- **图片契约（原站实测 URL，均已下载本地化）**：`/static/cw/back_btn.png`（26×46，显示 7×13）、`/static/huotu/icon-copy.png`（30×30，显示 16×16）、`/static/huotu/market-search.png`（60×60，显示 16×16）、`/static/huotu/empty-data.png`（426×426，显示 142×142）、二维码 PNG（330×330，显示 36×36，存为 `b16-invite-qrcode.png`）。
- **布局 Token**：内容区 `padding 24rpx 40rpx 80rpx`=12px 20px 40px；白卡圆角 24rpx=12px、`padding 24rpx`=12px；Tab 条 72rpx=36px 圆角 16rpx=8px `#f4f5f5`；内容盒 132rpx=66px 圆角 8px；助力输入行 96rpx=48px `#f7f8fa`；搜索框 80rpx=40px；空态图 284rpx=142px，容器 `min-height 480rpx`=240px、上间距 `gap-pf-y-16`=8px；统计卡 `padding 32rpx 24rpx`=16px 12px。

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据源 | 说明 |
|--------|------|--------|--------|------|
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（批次1 已有，**零代码改动**） | `ht_user`（已有 `inviter_code` / `origin_uid` 列） | 主账号 `inviter_code = HT173634` 自动带出 |
| POST | `/api/user/ranking` | `Invitation::ranking` | `ht_invitation_ranking.data_json` | 原站 data 原样 JSON 存库、`json_decode` 后原样返回，顶层 `msg:"查询成功"` / `time` 用快照值 |
| POST | `/api/user/invite` | `Invitation::invite` | 不读表 | 空码「请输入邀请码」/ 有码「活动未开启」（原站无效码实测），不臆造成功路径 |
| GET | `/addons/qrcode/index/build` | — | 本地静态 `b16-invite-qrcode.png` | 二维码为动态生成 PNG，已按硬规则「图片原站下载」本地化为静态快照，运行时零原站请求 |
| POST | `/api/index/getConfig` | `Index::getConfig`（批次1 已有） | — | 分享海报配置默认态不渲染，本批不改动已完成控制器 |

### 边界说明（如实登记，不臆造）

1. **`invite` 未对原站账号建立真实邀请关系**：该接口是写操作，一旦用有效邀请码调用会污染原站账号邀请状态、破坏用户逐批验收时的原站/Clone 对照。故仅用无效码 `INVALID_PROBE_CODE_XYZ` 安全探测，拿到原站真实「活动未开启」响应；本地如实复刻该响应。与批次15 `dosign` 同一处理模式。
2. **邀请链接 Tab 的 `undefined&content=HT173634`**：原站 `mapState('config').info.h5_invite_url` 未下发导致拼串出现 `undefined`，这是原站真实渲染结果，本项目按「不臆造」如实复刻（未擅自补全为完整 URL）。
3. **`getConfig` 分享配置**：原站页面会请求两次 `getConfig`（一次全局 key、一次 share key），但默认态不渲染海报（`showPoster` 为 false）；本项目由批次1 既有 `Index::getConfig` 统一处理，按硬规则第 6 条不改动已完成控制器。
4. **P084→P085 无页面内入口**：chunk 的 `goInviteRecord()` 未绑定模板，navbar 无右侧 slot。两页通过 `m.html` 导航页与直接 URL 独立验收，不强行新增入口（避免臆造）。

### 前端数据契约
- `pages/invitation/demo.js`：`IMG`（5 张本地图）/ `routeQuery()` / `TXT`（chunk 硬编码文案）/ `inviteLinkText(code)`（复刻 `undefined&content=` 串）/ `DEFAULT_CODE`。
- `?tab=link` 为**本地分支验收开关**：初始直接切到「邀请链接」Tab，接口仍走真实本地数据，不构造假记录。
- 交互验收：双 Tab 切换、复制邀请码/链接、去助力（空码/有码两态 toast + 按钮变红 + 清空）、搜索框占位文本节点、二维码点击反馈、P085 空态，本地与发布域均实测通过。


---

## 批次17 返佣域 + 批次3 占位落地（P086-P088 / P023 / P092）

> 侦查口径：Puppeteer 注入原站裸 token（`localStorage.setItem('token', t)`），
> 在页面上下文用 `fetch` 带 `token` / `version: 1.0.1` header 探测。
> 原站 API 域名是 `https://api.hnyiqin.asia`；直接 curl `https://h5.hnyiqin.asia/api/...` 返回 404。

### 原站契约（2026-11-15 实测）

| 接口 | 真实响应 / 行为 |
|------|----------------|
| `POST /api/user/commison_rank` | `{"code":404,"msg":"An error occurred","time":1794681284,"data":null}`（HTTP 404） |
| `POST /api/user/commision_info` | 同上（HTTP 404）。chunk 中 `data.loading=true`，`get_commision_info()` 的 `.catch` 静默，loading 遮罩恒不消失 |
| `POST /api/user/bei_yao_list` | 同上（HTTP 404） |
| `POST /api/lottery/info`（`{"id":"2"}`） | `code:0, msg:"暂无抽奖活动", data:null` |
| `POST /api/lottery/statistics`（`{"lottery_id":"2"}`） | `code:1, msg:"successfully !!!", data:[]` |
| `POST /api/lottery/record`（`{"lottery_id":"2","type":10,"page":1,"list_rows":15}`） | `data.total:0, per_page:15, current_page:1, last_page:0, data:[]` |

### 本项目等价接口（零原站依赖）

| Method | Path | 控制器 | 数据来源 |
|--------|------|--------|----------|
| POST | `/api/user/commison_rank` | `Rebate::commisonRank` | 静态快照体（不读表） |
| POST | `/api/user/commision_info` | `Rebate::commisionInfo` | 静态快照体（不读表） |
| POST | `/api/user/bei_yao_list` | `Rebate::beiYaoList` | 静态快照体（不读表） |
| POST | `/api/lottery/info` | `Lottery::info` | 静态快照体（不读表） |
| POST | `/api/lottery/statistics` | `Lottery::statistics` | 静态空数组（不读表） |
| POST | `/api/lottery/record` | `Lottery::record` | 静态空分页（不读表） |
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（批次1 已有，**零代码改动**） | `ht_user` 既有列 |

> 路由集中在 `backend/route/app.php` 追加 6 条；`php -l` 校验通过。
> 发布代理会剥离自定义 header，故沿用既有 `request.js` 的 header `token` + body `token` 兜底，
> `BaseController::auth()` 同时读 header / body token。

### 边界说明（如实登记，不臆造）

1. **返佣 3 接口原站为 HTTP 404 异常态**：本地如实返回同一 JSON body（HTTP 200，前端 `http.post` 不抛错），
   页面不臆造榜单 / 返佣数据，三 Tab、5 列表头均保持空态。
2. **P087 真实态是纯白屏**：原站 `commision_info` 404 → `.catch` 静默 → `loading` 恒 true →
   整页被 loading 遮罩盖住。本地按「不臆造」复刻白屏，不自行补全返佣数据页。
3. **`?demo=1` 是 chunk 模板注入态**：逐字还原 chunk 真实文案（默认邀请链接 / 邀请码 / 邀请海报 /
   数据详情 / 总返佣金额 / 本期返佣金额 / 名下用户总交易金额 / 名下用户总佣金 / 被邀请人 /
   邀请人数 / 有效邀请人数 / 节点排行榜）与 `c_info` 字段，仅作本地验收展示，不作为整图 MAE 对比基准。
4. **邀请链接拼串沿用批次16 结论**：`undefined&content=HT173634`（原站 `config.info.h5_invite_url` 缺失），
   chunk 掩码规则为前 10 位 + `******` + 后 5 位，本地 `maskedInviteLink()` 如实复刻。
5. **抽奖页自动返回行为不模拟**：原站从「我的」点击进入 `#/pages/lottery/lottery?id=2` 后停留约 1s
   显示「暂无抽奖活动」再自动返回 my，直接访问 URL 会重定向首页。本地按批次14 钱包同款保留稳定展示态
   便于逐批验收（避免「点了没反应」），几何如实对齐原站 `uni-toast`（118×39 @128,387）。
6. **「抽 5 次」按钮原站该态不渲染**：`uni-image` 的 `<img>` 是隐藏预加载元素（`opacity:0`），
   视觉由父层 `background-image` 决定，该态下父层未设置。本地 `.lt-btn--five { opacity:0 }` 如实隐藏，
   保留 DOM 占位供几何对照，不臆造出原站没有的红色按钮。
7. **设置 / 海报复用既有用户数据**：`getUserInfo` 零改动带出 `nickname` / `inviter_code` / `origin_uid`，
   未新增接口或表。
8. **图片资源**：统一 `b17-` 前缀本地化（`b17-back-btn.png` / `b17-empty-data.png` /
   `b17-lottery-bg.png` / `b17-lottery-draw-*.png` / `b17-mine-invite-modal-bg.png` /
   `b17-poster-qrcode.png` / `b17-setting-*.png`），规避批次16 同名覆盖坑；
   `b17-yao-image.png` / `b17-box-left.png` 原站响应为空已删除，demo 态不渲染这两张图。

### 前端数据契约

- `pages/rebate/demo.js`：`IMG` / `LB`（三 Tab 文案、表头、空态）/ `RD`（5 列表头、空态）/
  `POSTER`（邀请码、复制 / 保存 toast）/ `inviteLinkText(code)` / `maskedInviteLink(url)` / `routeQuery()`。
- `pages/rebate/common.css`：P086 / P088 公共样式，几何取自原站实测 DOM（375×812）。
- `components/InvitePoster.vue`：「邀请海报」为 my 页内浮层，不跳转；复制走 `navigator.clipboard`，
  H5 无相册写权限故「保存海报」按批次14 同款给出明确结果反馈，不臆造下载能力。
- 交互验收：leaderboard 三 Tab 切换（重请求 `commison_rank`，不臆造数据）、rebate demo 内跳转、
  设置行 toast / 退出登录、抽奖按钮 toast、海报复制 / 保存 / 关闭，本地与发布域均实测通过。

### 发布域实测（`?sharecode=C2APU7DXA7`，body token 兜底）

```
/api/user/commison_rank   {"code":404,"msg":"An error occurred","time":1794681284,"data":null}
/api/user/commision_info  {"code":404,"msg":"An error occurred","time":1794681284,"data":null}
/api/user/bei_yao_list    {"code":404,"msg":"An error occurred","time":1794681284,"data":null}
/api/lottery/info         {"code":0,"msg":"暂无抽奖活动","time":"...","data":null}
/api/lottery/statistics   {"code":1,"msg":"successfully !!!","time":"...","data":[]}
/api/lottery/record       {"code":1,"msg":"successfully !!!","data":{"total":0,"per_page":15,"current_page":1,"last_page":0,"data":[]}}
```


## 批次18 排行域（P089-P091）

### 原站契约（2026-11-15 实测，页面内 fetch，header token + version:1.0.1）

```
POST /api/user/newrank1               {}        -> {"code":0,"msg":"名人堂未开启","time":"1794749567","data":null}
POST /api/market/market/marketrebang  {}        -> {"code":1,"msg":"ok","data":[6 条真实榜单]}
（P089 masterRank 无专用接口，仅 getConfig / getUserInfo 全局请求，list 恒空 → 纯空态页）
```

`marketrebang` 6 条快照（逐字）：熊小七(3000/1500/951/¥150)、2026青花瓷-龙王(2888/2700/887/¥380)、
大吉大利(5000/1000/668/¥110)、天官赐福(1500/800/315/¥150)、今晚吃鸡(1000/500/115/¥2000)、
弥勒-达观祥和(44350/44350/107/¥36)。字段 `id / name / total_num / flux / listimg /
reprice_limit / hotnum / is_like / min_price`。

### 本项目等价接口（零原站依赖）

> **兼容性决策**：原站同名的 `newrank1` / `marketrebang` 在项目早期批次已实现，且被**已交付首页
> `index.vue`（两接口都调）与市场页 `market.vue`（调 newrank1）** 使用，返回彼时演示数据。
> 改写会破坏已完成页面（硬规则第 6 条），故本批新增 3 个专用端点承载原站**当前**真实态快照，
> 仅供批次18 三页调用，已交付端点与页面零改动。

| Method | Path | 控制器 | 响应（原站同构） |
|--------|------|--------|------------------|
| POST | `/api/rank/masterList` | `Rank::masterList` | `code:1 msg:"ok" data:[]`（大师动态空列表） |
| POST | `/api/rank/hallOfFame` | `Rank::hallOfFame` | `code:0 msg:"名人堂未开启" data:null` |
| POST | `/api/rank/hotList` | `Rank::hotList` | `code:1 msg:"ok" data:[6 条真实榜单]` |

3 端点均 `auth()` 后返回静态快照体；`time` 为当前时间戳（字符串），其余字段逐字对齐原站。

### 发布域实测（`?sharecode=C2APU7DXA7`，body token 兜底）

```
/api/rank/masterList  {"code":1,"msg":"ok","time":"1794753657","data":[]}
/api/rank/hallOfFame  {"code":0,"msg":"名人堂未开启","time":"1794753657","data":null}
/api/rank/hotList     {"code":1,"msg":"ok","time":"1794753657","data":[{"id":23,"name":"熊小七",...}]}
```

### 边界说明（如实登记，不臆造）

- **破图**：`/static/tangpai/*` 共 17 个资源原站全部 HTTP 404（`rank-bg-0*.png` 前三名卡片背景 /
  `hall-of-fame-*.png` 领奖台 / `hotlist-*.png` 前三名奖牌 / `icon-deal.png` 成交胶囊图标 /
  `icon-search.png` / `icon-unfavorited.png`）。uni-image 元素不渲染但仍占位；Clone 不本地化、
  不臆造替代图片，仅保留布局占位偏移。前三名卡片为透明底、后三名白卡，靠文字/成交胶囊色区分。
- **瞬时 toast**：名人堂 `newrank1` 失败触发 `uni.showToast("名人堂加载失败")`（1.2–2.2s 可见、
  3.5s 消失）。按批次13/15/17 惯例 SKIP；稳定态快照统一等 4.5s 采集，Clone 不渲染该 toast。
- **商品图 CDN**：原站商品图走 `img.hnyiqin.asia` + `listimg` 相对路径，已下载本地化为
  `b18-hot1..6`（jpg/png），前端 `pages/rank/demo.js` 的 `localHotImg(index)` 按序映射，零原站运行时依赖。
- **状态机**：名人堂 Segment 购入榜/售出榜切换仅改选中态，两榜数据恒为空（名人堂未开启）；
  顶部 4 Tab 点击跳转：经典 → `/`（首页）、关注 → `/pages/market/attentionList`（P025 未交付，
  沿用 TabBar 既有提示）、热榜 → `/pages/rank/userBuy`、名人堂 → `/pages/rank/rankList`。
- **底部 TabBar**：rank 域固定「首页」激活态（与 route.path 无关），本批专用 `RankTabBar.vue`
  固定该态，不改动已交付共享 `components/TabBar.vue`。

### 前端数据契约

- `pages/rank/demo.js`：`IMG` / `NAV`（顶部 Tab 文案与 pendingToast）/ `HALL`（Segment、占位 `--`/`¥0`、
  三列色值）/ `HOT_SNAPSHOT`（6 条逐字快照）/ `HOT_STYLE`（按 rank 的标题/发行流通/价格/成交胶囊配色，
  取自原站 chunk `getTitleStyle`/`getStatsStyle`/`getPriceColor`/`getTransactionButtonStyle`）/
  `localHotImg(i)` / `formatPrice()`。
- `pages/rank/common.css`：三页共享 `--rk-*` Token 色值与几何（顶部 Tab、Segment、领奖台、
  热榜卡片 340×75、底部 TabBar）；`pages/rank/RankTabs.vue` / `RankTabBar.vue` 为本批专用组件。
- 交互验收：顶部 Tab 跳转 3/3、Segment 购入/售出切换、底部 TabBar 5/5、热榜 6 卡片名称/价格/
  名次/图片全部正确，本地与发布域均实测通过。


## 批次19 抽奖域：中奖记录 / 活动规则（P093-P094）

### 原站契约（2026-11-16 实测，页面内 fetch，header token + version:1.0.1）

```
P093 /pages/lottery/record：onLoad -> this.id = A.id||A.lottery_id||''
  POST /api/lottery/statistics  {"lottery_id":""}  -> {"code":1,"msg":"successfully !!!","data":[]}
  （chunk 内虽定义 lotteryRecord=/lottery/record，但页面实际调 lotteryStatistics，不调 /lottery/record）
  全局：POST /api/index/getConfig / POST /api/user/getUserInfo（App 启动请求，非页面专用）
P094 /pages/lottery/rule：无页面专用接口
  正文 lotteryRule = uni.getStorageSync("lotteryRule")（本账号恒为 null）
  getConfig 规则相关 key 全部为空字符串：draw_lots_rule / draw_lots_notice /
    draw_code_rankrule / draw_share_assrule / helpdraw_rule / share_rule / draw_share_assimage
  全局：POST /api/index/getConfig / POST /api/user/getUserInfo
```

### 本项目等价接口（零新增端点，零原站依赖）

> **决策**：本批两页均为静态真实态（record 空态 / rule 空白卡），P093 消费批次17 已实现的
> `POST /api/lottery/statistics`（返回 `data:[]`，与原站逐字一致）；P094 正文为本地存储驱动的
> 静态富文本卡，原站无专用接口。按「无产物不设席位」不臆造端点，仅复用既有端点、零新增。

| Method | Path | 控制器 | 消费页面 | 响应（原站同构） |
|--------|------|--------|----------|------------------|
| POST | `/api/lottery/statistics`（批次17 已有，未改动） | `Lottery::statistics` | P093 record | `code:1 msg:"successfully !!!" data:[]`（空态） |
| （无） | — | — | P094 rule | 静态空白规则卡，无页面专用接口 |

P092 抽奖活动页（批次17 已交付）的 3 个既有端点 `/api/lottery/info` `/statistics` `/record` 本批零改动。

### 发布域实测（`?sharecode=C2APU7DXA7`，body token 兜底）

```
/api/lottery/statistics  {"code":1,"msg":"successfully !!!","time":"1794759379","data":[]}
/api/lottery/info        {"code":0,"msg":"暂无抽奖活动","time":"1794759379","data":null}
/api/lottery/record      {"code":1,"msg":"successfully !!!","data":{"total":0,"per_page":15,"current_page":1,"last_page":0,"data":[]}}
```

### 边界说明（如实登记，不臆造）

- **空白规则卡**：原站 rule 页规则正文来自 `uni.getStorageSync('lotteryRule')`，本账号实测恒为
  null，且 getConfig 全部规则配置 key 为空字符串；从「我的→抽奖活动」链路 P092 从不写入该 storage。
  故 `.content` 卡 @(16,59) 343×650 min-height 80vh br12 rgb(244,246,250) 内 u-parse 无节点 →
  空白卡是原站当前真实态。Clone 如实复刻，不臆造规则正文、不新增规则接口。
- **背景色差异**：P093 整页 + navbar 均 rgb(247,249,252)（navbar 非白）；P094 整页 + navbar 白
  rgb(255,255,255)，仅规则卡为 rgb(244,246,250)。两页分别复刻，不与批次17 P092 的
  rgb(244,246,250) 整页背景混淆。
- **破图**：原站 `cj-page-bg.png`（page-bg-colorfull 背景层）HTTP 404，实际渲染为纯色；不本地化、
  不臆造替代背景图。
- **隐藏 TabBar**：两页 DOM 中 5 个 tab 图标均为 0×0 隐藏预加载层（op=1 但无尺寸），底部纯色空白，
  Clone 不渲染 TabBar。
- **入口联动**：P092 右上「我的中奖」→ `#/pages/lottery/record?id=2`（原站实测）。本批把
  `lottery.vue` 该 toast 占位改为真实跳转，视觉零改动；P094 rule 页原站无当前可达入口
  （未侦查到任何跳 `#/pages/lottery/rule` 的按钮），按路由直接访问验收，不臆造入口。

### 前端数据契约

- `pages/lottery/common.css`：两页共享 `--l19-*` Token（record 背景 / rule 白底 / 卡片色 / 圆角 / 标题色）
  与 navbar 几何（44px、返回箭头 7×13 @19,16、标题 17px/700 居中）；record 空盒图 142×142、
  rule 卡 343×650 br12。
- `pages/lottery/record.vue`：`onMounted` 调 `/api/lottery/statistics`，空态如实保持；图片
  `b19-back-btn.png` / `b19-empty-data.png`（与批次17 资源 md5 一致）。
- `pages/lottery/rule.vue`：纯静态空白卡，无接口调用；top 注释完整记录 lotteryRule 为空的侦查依据。
- 交互验收：P093/P094 结构/几何/背景校验 OK、返回按钮、P092「我的中奖」联动跳 record?id=2，
  本地与发布域均实测通过。

## 批次20 抽奖活动域（P095-P099）

### 原站契约（2026-11-16 实测，GET + header token）

| 端点 | 说明 |
|------|------|
| `GET /api/score/get_my_share_details?type=1\|2` | **唯一接口**。原站实测 HTTP/2 404、content-type: text/html、content-length: 0（空响应体）。P099 chunk fe090c11 的 onLoad 调用之，catch 后积分默认 0，页面静态渲染 |

其余四页零端点：P095/P097/P098 计数与列表来自页面参数/localStorage（直接访问恒 0 + 空态）；
P096 onLoad 缺页面参数 → `JSON.parse(undefined)` 抛异常 → loading 恒 true（永久加载态，无接口调用）。

### 本项目等价接口

| Method | 路径 | 控制器 | 服务页 | 行为 |
|--------|------|--------|--------|------|
| GET | `/api/score/get_my_share_details` | `Draw::getMyShareDetails` | P099 | HTTP 404 + 空响应体（`http_response_code(404)` + `Content-Length: 0` + exit，绕过框架 debug trace 附加），与原站逐帧一致 |

前端 `pages/draw/shareCommunityGroup.vue` `onMounted` 调用之（axios error 分支 catch 后静态渲染），
与原站 chunk 行为一致。

### 发布域实测（`?sharecode=C2APU7DXA7`）

```
GET /api/score/get_my_share_details?type=1  HTTP 404 len:0（type=2 同）
```

### 边界说明（如实登记，不臆造）

- **永久加载态是真实态**：P096 原站 chunk `onLoad` 解析页面参数 result 时 `JSON.parse(undefined)`
  直接抛异常，`loading` 恒 true → spinner +「加载中...」永驻。Clone 如实复刻，不臆造中签结果、
  不补页面参数兜底逻辑。
- **404 空体是真实态**：P099 原站接口为服务端 404（非业务空态），响应体 0 字节；与批次17 返佣接口
  （HTTP 200 + body code 404 JSON）形态不同，本项目按各自实测形态分别复刻。
- **破图**：上传框加号 `static/img/icon/IconAdd.png`、规则卡图标 `icon_cp.png` 原站 404
  （实测 `background-image: none`），渲染为空白占位；Clone 如实复刻空白，不臆造替代图。
- **iconfont 本地化**：空态图标 `\ue62f` 与系统导航栏返回箭头 `\ue601` 均来自原站 uview uicon 字体
  （at.alicdn.com font_2225171），本地化 `public/static/fonts/d20-uicon.woff`（同源 woff），
  五页统一引用，零 emoji。
- **入口可达性**：本批五页在原站均无固定入口按钮可达（依赖活动参数/分享链路深链），
  与 P093/P094 同属「按路由直接访问验收」，不臆造入口。

### 前端数据契约

- `pages/draw/common.css`：五页共享 `--d20-*` Token（黑卡 #302920 / 深按钮 #313131 / jb 按钮色
  #141416 / theme #F4F6FA / 空态 #C0C4CC / 加载 #C8C8C8 / 上传框 #E5E5E5 等）与系统导航栏样式
  （白底 44px、\ue601 27px @(5,6)、标题 16px/700 top9）、空态组件（\ue62f 90px + 文字 mt15）、
  d20-spin 旋转动画。
- 五个页面组件均带原站 chunk hash 注释（communityShare.6ffa8f31 / drawResult.36af198a /
  InvitationFriendAssist.10a01bda / Leaderboard.726db9b5 / shareCommunityGroup.fe090c11），
  侦查结论逐字记录在文件头注释。
- 校准记录：字体 metrics 差（原站 range 高 ≈ 本地 -5px）通过 margin/padding 链式补偿，
  全部文字 dy ≤ 2（详见 `docs/compare/batch20/README.md`）。

## 批次21 活动域（P100-P101）

### 原站契约（2026-11-16 实测，GET/POST + header token）

| 端点 | 说明 |
|------|------|
| `GET /api/draw/get` | 抽签活动详情。原站无活动参数直访返回 `{"code":0,"msg":"活动不存在","data":null}`，P100 组件渲染抛异常 → 整页纯白（真实态） |
| `POST /api/draw/drawLog` | 中签记录列表。原站返回 `{"code":1,"msg":"获取成功","data":[]}`（当前账号无中签记录），P101 渲染开箱空态图 + 提示文字 |
| （P100 并行）`POST /api/user/ranking` `{"page":1,"types":2}` | 原站并行请求但不产生可见内容（组件已崩溃），无消费者 |

P100 另有 `POST /api/draw/drawLog`（data:[]）、P101 另有全局 `/api/index/getConfig` + `/api/user/getUserInfo`，
均为已交付的全局端点，本批不改动。

### 本项目等价接口

| Method | 路径 | 控制器 | 服务页 | 行为 |
|--------|------|--------|--------|------|
| GET | `/api/draw/get` | `Draw::get` | P100 | `fail('活动不存在', 0)` → `{"code":0,"msg":"活动不存在","data":null}`，前端据此保持纯白 |
| POST | `/api/draw/drawLog` | `Draw::drawLog` | P101 | 返回 `{"code":1,"msg":"获取成功","time":...,"data":[]}`，前端据此渲染空态 |

两者均为静态真实态快照，不读 DB；新增路由不碰 `marketrebang`/`newrank1`/`lottery/*`/`get_my_share_details` 等已交付端点。

### 发布域实测（`?sharecode=C2APU7DXA7`）

```
GET  /api/draw/get       -> {"code":0,"msg":"活动不存在","data":null}
POST /api/draw/drawLog   -> {"code":1,"msg":"获取成功","data":[]}
P100 发布域纯白、P101「中签记录 目前没有任何数据」，无 pageerror / 破图 / API 异常
```

### 边界说明（如实登记，不臆造）

- **纯白是真实态**：P100 不补活动参数兜底、不臆造抽奖界面；P101 不臆造中签数据。
- **透明空条带**：P101 navbar 后 `.br-12.pa-16` @(16,44) 343×32 为 draw_notice 空残留，无可见内容。
- **图片复用**：返回箭头与空态图与批次19 原站同源逐像素一致，复用 b19 资源，零重复下载。
- **背景色**：两页纯白 rgb(255,255,255)，区别于批次20 #F5F5F5。

## 批次22 活动精选域（P102）

### 原站契约（2026-11-16 实测，POST + header token）

| 端点 | 说明 |
|------|------|
| `POST /api/Activity/category` | 活动分类列表。原站返回 `{"code":1,"msg":"分类列表","data":[]}`（无自定义分类） |
| `POST /api/activity/index` | 活动列表（按分类分页）。原站返回 `{"code":1,"msg":"请求成功","data":{"total":0,"per_page":10,"current_page":1,"last_page":0,"data":[]}}`（无活动） |

两者共同驱动 P102 空态：无分类、无活动 → 开箱空态图 +「目前没有任何数据」。
另有全局 `/api/index/getConfig`、`/api/user/getUserInfo`（已交付，本批不改动）。

### 本项目等价接口

| Method | 路径 | 控制器 | 服务页 | 行为 |
|--------|------|--------|--------|------|
| POST | `/api/Activity/category` | `Activity::category` | P102 | `{"code":1,"msg":"分类列表","time":...,"data":[]}` |
| POST | `/api/activity/index` | `Activity::index` | P102 | `{"code":1,"msg":"请求成功","time":...,"data":{total:0,per_page:10,current_page:1,last_page:0,data:[]}}` |

均为静态真实态快照，不读 DB；新增 controller/Activity.php 与路由不碰已交付端点。

### 发布域实测（`?sharecode=C2APU7DXA7`）

```
POST /api/Activity/category  -> {"code":1,"msg":"分类列表","data":[]}
POST /api/activity/index     -> {"code":1,"msg":"请求成功","data":{"total":0,...,"data":[]}}
P102「活动中心 最新活动 抽奖中心 目前没有任何数据」，无 pageerror / 破图 / API 异常
```

### 边界说明（如实登记，不臆造）

- **背景图全 404 是真实态**：分类入口背景图 `active-banner-bg1/bg2.png`（OBS 跨域失败）、
  `cj-page-bg.png`/`active-new.png`/`active-lottey.png` 均 404 破图；实测卡片背景 = 页面色
  rgb(244,246,250)（隐形卡），仅白字浮于底上。不臆造图片、不落地 404 资源。
- **背景色**：整页 + navbar 均 rgb(244,246,250)=#F4F6FA，与批次19 同色系，区别于批次20 #F5F5F5、批次21 纯白。
- **图片复用**：返回箭头与空态图与批次19 原站逐像素同图，复用 b19 资源。

## 批次23 实名认证域（P103-P104）—— 零新增端点

两页仅调用全局接口，无页面专用 API；后端 `Index::getConfig` + `User::getUserInfo` 已存在且零改动：

| Method | Path | 实现 | 页面 | 说明 |
|--------|------|------|------|------|
| POST | `/api/index/getConfig` | `Index::getConfig`（复用） | P103/P104 | 全局配置，已交付端点零改动 |
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（复用） | P103/P104 | `real_name_authentication=1`/`real_name`/`id_card` 字段来源，已交付端点零改动 |

### 原站 getUserInfo 实名相关字段（侦查快照）

```
real_name_authentication = 1          # 已完成实名
real_name = 王*绵                     # P103 直接显示
id_card = 520******838                # 页面二次脱敏显示为 520************838
```

静态真实态快照，前端不依赖本地 DB 认证数据渲染；两页均无新增端点、无 controller 改动。

### 边界说明（如实登记，不臆造）

- **P103 已完成态真实**：本账号 `real_name_authentication=1`，直访显示已完成实名态，不臆造未认证表单。
- **P104 失败态直访真实**：无认证流程参数直访 `certificationStatus` 显示失败结果页，如实复刻不臆造成功态。
- **原生 nav 双标题**：`uni-page-head__title` 16px 框架层壳与自定义 17px 标题重叠，对比层 SKIP 框架层。
- **背景图**：P103 `cert-verified.png` 本地化；P104 `cj-page-bg.png` 404 破图如实不落地。

## 批次24 行情K线域（P105-P106）—— 零新增端点

两页仅调用全局接口，无页面专用 API；后端已存在且零改动：

| Method | Path | 实现 | 页面 | 说明 |
|--------|------|------|------|------|
| POST | `/api/index/getConfig` | `Index::getConfig`（复用） | P105/P106 | 全局配置 |
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（复用） | P105/P106 | 个人信息 |
| POST | `/api/banner/show/getBanner` | `Index::banner`（复用） | P105/P106 | banner 轮播，返回 data.total=2 两张图，与原站逐字一致 |

### 原站 getBanner 响应（侦查快照）

```
{"code":1,"msg":"banner列表","data":{"total":2,"per_page":15,"current_page":1,"last_page":1,
  "data":[
    {"id":9,"image":"/uploads/20261103/98f80a96656573090ad81cd42c5f10b6.jpg",...},
    {"id":8,"image":"/uploads/20261103/ddc6e2ffb38d8bce4b2fd1f5b90cd83.jpg",...}
  ]}}
```

静态真实态快照，前端用本地化 banner 图渲染（本地 getBanner 返回 URL 与原站一致）；无 K线数据接口。

### 边界说明（如实登记，不臆造）

- **深色主题低对比文字**：价格/涨跌幅 `rgb(51,51,51)` 在深色底上对比度极低，为原站真实设计，不提亮。
- **banner 轮播**：两页各两张 JPG，次图位于屏外如实复刻；指示器暗黄点 + 亮绿条。
- **行情数据固定快照**：`112.34CNY`/`-0.53%`/`30000` 为当前账号固定值，无实时接口。

## 批次3 我的域二级页（P009-P024）—— 2 个静态端点

14 页中 12 页仅调用全局接口（后端已存在、零改动）：

| Method | Path | 实现 | 页面 | 说明 |
|--------|------|------|------|------|
| POST | `/api/index/getConfig` | `Index::getConfig`（复用） | 全部 14 页 | 全局配置，已交付端点零改动 |
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（复用） | 全部 14 页 | 个人信息，已交付端点零改动 |

本批新增 2 个静态真实态端点（用户域）：

| Method | Path | 实现 | 原站实测响应 | 说明 |
|--------|------|------|--------------|------|
| POST | `/api/user/address_list` | `User::addressList` | `{"code":1,"msg":"地址列表","data":[]}` | 当前账号无收货地址，恒空数组，不建地址表 |
| POST | `/api/user_collection/user_collection/getRecord` | `User::getRecord` | `{"code":0,"msg":"参数错误","data":null}` | 无参数直访真实态，页面据此显示空态+toast |

### 边界说明（如实登记，不臆造）

- 前端 14 页为静态快照复刻，不依赖接口数据渲染；新端点仅补齐 API 契约，返回与原站逐字一致。
- P014 正文原站回退 Times New Roman 渲染中文，已在 CSS 显式锁定字体栈对齐像素。
- 无 schema change：`address_list` 空态不建表，符合「无产物不设席位」原则。

---

## 批次4 市场交易（P025/P026/P027/P028/P030-P036，2026-11-17）

本批 11 页全部为无参数直访真实态快照，**零新增端点、零 schema change**。

### 复用端点（均已由早期批次实现，零改动）

| Method | Path | 实现 | 页面 | 原站实测响应要点 |
|--------|------|------|------|------------------|
| POST | `/api/market/market/getMarketList` | `Market::getMarketList`（复用） | P025 关注列表 / P031 市场搜索 | `{"code":1,"data":[8 条真实藏品]}`（弥勒-达观祥和/今晚吃鸡等） |
| POST | `/api/banner/show/getBanner` | `Index::banner`（复用） | P027/P032/P033 fallback 首页 | 2 张 banner JPG |
| POST | `/api/index/afficheList` | `Index::afficheList`（复用） | P027/P032/P033 fallback 首页 | 30 条公告，动态轮播 |
| POST | `/api/index/getSeries` | `Index::getSeries`（复用） | P027/P032/P033 fallback 首页 | 系列列表 |
| POST | `/api/box/blind_box/list` | `BlindBox::list`（复用） | P027/P032/P033 fallback 首页 | 发售日历 |
| POST | `/api/market/market/marketrebang` | `Index::marketrebang`（复用） | fallback 首页热榜 | 热榜数据 |
| POST | `/api/user/newrank1` | 复用 | fallback 首页排行 | 名人堂未开启 |
| POST | `/api/index/getConfig` / `/api/user/getUserInfo` | 复用 | 全部 11 页 | 全局配置/个人信息 |

### 静态真实态端点（原站返回，本项目前端快照复刻，未新增后端端点）

| Path | 原站实测响应 | 页面 | 复刻方式 |
|------|--------------|------|----------|
| `/api/market/market/MarketDetail/ids/undefined` | `{"code":0,"msg":"已售出","data":null}` | P027 | fallback 首页，前端快照 |
| `/api/market/trade/getSellCollectionInfo` | `{"code":0,"msg":"藏品不存在","data":null}` | P032 | fallback 首页 + 瞬时 toast（对比 SKIP） |
| `/api/market/market/marketGoodsDetail` | HTTP 500 `{"code":500,"msg":"An error occurred"}` | P030/P033 | 空壳静态快照 |
| `/api/market/trade/tradingInformation` | `{"code":1,"data":{"list":[],...}}` | P028 | 空态快照 |
| `/api/market/trade/collectionDetail` | `{"code":0,"msg":"藏品ID不能为空","data":null}` | P034/P035 | 表单+toast 快照 |
| `/api/market/Prepayment/collectionDetail` | `{"code":0,"msg":"藏品ID不能为空","data":null}` | P035 | 表单快照 |
| `/api/index/customSeriesList` | `{"code":1,"data":{"custom_series":[],"platform_series":[]}}` | P026 | 静态分类快照 |
| P036 tradeUnion | 无专用接口（仅全局 getConfig/getUserInfo） | P036 | 静态权益页快照 |

### 边界说明（如实登记，不臆造）

- P025/P031 商品数据为原站 2.2s 稳定态 8 条真实藏品快照，价格数字沿用 BebasNeue 字体。
- P027/P032/P033 无参数直访在原站 fallback 到首页（body class=`pages-index-index`），本项目复用已交付首页组件，
  动态公告/发售日历数据按采集时点不同 SKIP 并登记，静态骨架几何层 0 fail。
- 无 schema change：`increment_v18_batch4.sql` 仅占位记录，不建无消费者空表。

## 批次25–29 收官 7 页（2026-11-18）

### 复用端点（零新增）

| Method | Path | 控制器 | 页面 | 说明 |
|--------|------|--------|------|------|
| POST | `/api/index/getConfig` | `Index::getConfig`（复用） | 全部 7 页 | 全局配置（draw_lots_rule 空串等） |
| POST | `/api/user/getUserInfo` | `User::getUserInfo`（复用） | 全部 7 页 | 个人信息 |
| POST | `/api/user/newrank1` | 复用（原站 code0） | P107 | 名人堂未开启 → 空态快照 |
| POST | `/api/user/ranking` | 复用（原站 data:[]） | P108 | 市值排行空态快照 |

### 静态真实态端点（原站返回，本项目前端快照复刻，未新增后端端点）

| 页面 | 原站实测 | 复刻方式 |
|------|----------|----------|
| P109 规则 | getConfig.draw_lots_rule = 空串 | 浅灰内容卡空态快照 |
| P110 下载 APP | 无专用接口（仅全局 getConfig/getUserInfo）；appBigLogo jpg 404 | 静态引导页；logo 灰块占位 |
| P111 启动页 | 无专用接口；start-bg.png 静态资源 | 静态启动图快照 |
| P112/P113 webview | 无 url 参数 → iframe/wv-page 空白 | 白底空页快照 |

### 边界说明（如实登记）

- 本批 7 页全部为零新增端点、零 schema change；`increment_v18_batch25.sql` 仅占位记录。
- P107/P108 原站接口分别返回「名人堂未开启」「空 data」，忠实复刻为空态，不臆造排行榜数据。
- P110 appBigLogo jpg 原站 404，复刻为灰块占位（与原站破图态一致）。
- **全量收官**：113/113 页面全部完成，API 文档覆盖 29 个批次全部端点。

## 缺陷修复：首页「藏品发售」点击进不去（2026-11-19）

**现象**：首页发售日历商品卡片点击无反应/跳详情页后自动回退首页。

**根因（前后端双 bug）**：
1. 前端 `index.vue` 发售卡片 `goods-card` 漏绑点击事件——原站首页 chunk 的 `goRecommendDetail(a)` 未复刻。
2. 后端 `Index::blindBoxList()` 将 `batch_id` 硬编码为 `1`；而详情页 `Product::productDetail()` 按 `ht_product.batch_id` 校验（id=20→3、id=33→4），批次不符返回 `code:0 找不到是哪个批次`，详情页随即 `back()` 回退首页。

**修复**：
- 前端补 `goRecommendDetail(g)`：对齐原站逻辑，`goods_type==1` → `/pages/product/box?id=`，否则 → `/pages/product/collection?id=&batch_id=`；卡片加 `@click`。
- 后端 `blindBoxList()` 改为按商品 id 从 `ht_product` 权威读取 `batch_id`（与详情接口同源），查不到回退 1。零 schema change。

**验证**：有效登录态下 Puppeteer 实测——点击卡片 → `#/pages/product/collection?id=20&batch_id=3` → 详情接口 `code:1` → 页面渲染真实商品内容，0 pageerror。`php -l` 通过、`vite build` 通过、P0 扫描 0 违规。
