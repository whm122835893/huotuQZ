# 火兔 H5 复刻项目（Vue3 + ThinkPHP + MySQL）

对 `https://h5.hnyiqin.asia` 的独立复刻项目。前端、后端、数据库完全自建，**不依赖原站任何服务**。

## 目录结构

```
huotu-clone/
├── docs/                     # 侦查与设计文档（逐批更新）
│   ├── 01-网站分析报告.md     # 技术栈/118条路由/视觉规格/交互逻辑
│   ├── 02-API文档.md          # 66+端点逆向清单与响应结构
│   └── 03-数据库设计文档.md   # MySQL 表设计（随页面验收持续校准）
├── frontend/                 # Vue 3 + Vite + Pinia + Vue Router（hash 路由与原站一致）
│   └── src/
│       ├── pages/login/login.vue   # 已交付：登录页（像素级）
│       ├── assets/images/          # 图片均取自原站（login-bg.png 等）
│       ├── assets/fonts/           # Alibaba PuHuiTi ×3 + Bebas Neue（原站同款）
│       ├── styles/global.css       # 设计 Token（取样锁定）
│       ├── api/request.js          # axios 封装（token头 + 401跳转）
│       └── stores/user.js          # Pinia 登录态
└── backend/                  # ThinkPHP 8 + MySQL（与原站 API 路径/响应结构同构）
    ├── app/controller/User.php     # login / getUserInfo（已实现）
    ├── route/app.php
    ├── sql/init_v1.sql             # 建表 + 配置 seed
    └── .env                        # 本地 DB 配置
```

## 本地运行

```bash
# 数据库（首次）
mysql -u root < backend/sql/init_v1.sql   # 或用 .env 中配置的账号

# 后端（端口 8000）
cd backend && php think run --host 0.0.0.0 --port 8000

# 前端（端口 5173，/api 已代理到 8000）
cd frontend && pnpm install && pnpm dev
```

预览：http://localhost:5173/#/pages/login/login
测试账号：请联系项目维护者获取（公开仓库不存放明文凭据）

## 像素对齐证据（375×812 视口，原站 vs 复刻）

| 元素 | 原站 | 复刻 |
|------|------|------|
| Logo 红色带 y | 238–300 | 237–300 |
| 协议勾选 y | 628–642 | 629–645 |
| 验证码登录 y | 759–766 | 759–766 |
| 输入胶囊 | x24 y439 w327 h48 r24 | 同 |
| 忘记密码 | x279 y515 w52 | 同 |
| 按钮 | y567 h48 r24 启用#D93437/禁用#E8D0D2 | 同 |
| 登录响应 | `{code:1,data:{userinfo:{token,...expires_in:2592000}}}` | 同构 |

## 复刻进度

- [x] 侦查分析 + 三份文档
- [x] 登录页（本批交付，待验收）
- [ ] 注册 / 验证码登录 / 忘记密码（下一批，按验收顺序）
- [ ] 首页 → 我的 → 市场 → 发现 → 活动(合成) → 商品详情 → …
