#!/bin/bash
# 火兔复刻项目 —— 单端口发布启动脚本（全异步，前端立即就绪）
ROOT="$(cd "$(dirname "$0")" && pwd)"

# 1. 数据库（幂等，后台启动，不阻塞）
if ! mysqladmin status >/dev/null 2>&1; then
  service mariadb start >/dev/null 2>&1 || service mysql start >/dev/null 2>&1 || mysqld_safe >/dev/null 2>&1 &
fi

# 2. ThinkPHP 后端（8000，幂等，后台启动，不阻塞）
if ! curl -s -o /dev/null --max-time 1 http://127.0.0.1:8000/; then
  (cd "$ROOT/backend" && nohup php think run --host 0.0.0.0 --port 8000 >/tmp/think.log 2>&1 &)
fi

# 3. 前端 Vite dev server（前台运行，监听注入的 PORT）
cd "$ROOT/frontend"
exec npx vite --host 0.0.0.0 --port "${PORT:-5173}" --strictPort
