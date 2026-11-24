/* 批次18 排行三页：数据契约层
   P089 /pages/rank/masterRank  大师动态
   P090 /pages/rank/rankList    名人堂
   P091 /pages/rank/userBuy     热榜

   侦查口径（原站实测，2026-11-15）：
   - masterRank 无专用接口、无 TabBar，navbar「大师动态」+ 空盒图，背景 rgb(247,249,252)。
   - rankList 调 newrank1 → code:0「名人堂未开启」data:null；chunk .catch 触发
     uni.showToast("名人堂加载失败")（瞬时 1.2-2.2s 可见，3.5s 消失）+ loading=empty。
     本项目以专用端点 /api/rank/hallOfFame 返回同一真实态快照，不动已交付的
     /api/user/newrank1（首页/市场页在用，返回有数据版本）。
   - userBuy 调 marketrebang → 6 条真实榜单。本项目以 /api/rank/hotList 返回快照，
     不动已交付的 /api/market/market/marketrebang（首页在用，返回 1 条演示数据）。
   - /static/tangpai/* 共 17 个资源原站全部 HTTP 404 破图（rank-bg-0*.png /
     hall-of-fame-*.png / hotlist-*.png / icon-*.png），不渲染、不本地化、不臆造替代；
     前三名卡片因此为透明底（露出页面 rgb(244,245,245)），后三名为白卡。
   - 商品图原站走 img.hnyiqin.asia CDN，已下载本地化为 b18-hot1..6。
*/
import backBtn from '../../assets/images/b18-back-btn.png'
import emptyData from '../../assets/images/b18-empty-data.png'
import logo from '../../assets/images/b18-logo.png'
import hot1 from '../../assets/images/b18-hot1.jpg'
import hot2 from '../../assets/images/b18-hot2.jpg'
import hot3 from '../../assets/images/b18-hot3.jpg'
import hot4 from '../../assets/images/b18-hot4.jpg'
import hot5 from '../../assets/images/b18-hot5.png'
import hot6 from '../../assets/images/b18-hot6.jpg'

export const IMG = { back: backBtn, empty: emptyData, logo }

/* 原站 listimg → 本地资源（按 marketrebang 返回顺序 1..6） */
const LOCAL_HOT = [hot1, hot2, hot3, hot4, hot5, hot6]
export function localHotImg(index) {
  return LOCAL_HOT[index] || emptyData
}

export const NAV = {
  masterTitle: '大师动态',
  tabs: ['经典', '关注', '热榜', '名人堂'],
  hotTabs: ['经典', '关注', '热榜'],
  hallActive: '名人堂',
  hotActive: '热榜',
  pendingToast: '该页面将在后续批次交付'
}

export const HALL = {
  segments: ['购入榜', '售出榜'],
  placeholder: '--',
  wealth: '¥0',
  empty: '目前没有任何数据',
  loadFailToast: '名人堂加载失败',
  /* 三列占位色（原站 @60/179/299,292 实测） */
  placeColors: ['rgb(163, 79, 184)', 'rgb(132, 119, 101)', 'rgb(209, 154, 12)'],
  wealthColor: 'rgb(131, 131, 131)'
}

/* marketrebang 原站真实 6 条（code:1 msg:"ok"），逐字快照 */
export const HOT_SNAPSHOT = [
  { id: 23, name: '熊小七',          total_num: 3000,  flux: 1500,  listimg: '/uploads/20261105/76c4b4a3d1285e5cd5f085bf511aa3ef.jpg', reprice_limit: '1000.00',  hotnum: 951, is_like: 0, min_price: 150 },
  { id: 22, name: '2026青花瓷-龙王', total_num: 2888,  flux: 2700,  listimg: '/uploads/20261105/22f503c727362cf58a1cf76b2846c99a.jpg', reprice_limit: '10000.00', hotnum: 887, is_like: 0, min_price: 380 },
  { id: 25, name: '大吉大利',        total_num: 5000,  flux: 1000,  listimg: '/uploads/20261106/3009215916b46e7fadf16520c1bd7a2c.jpg', reprice_limit: '300.00',   hotnum: 668, is_like: 0, min_price: 110 },
  { id: 24, name: '天官赐福',        total_num: 1500,  flux: 800,   listimg: '/uploads/20261106/e1712a4bf6e126a37880fca8a70f7ed4.jpg', reprice_limit: '2000.00',  hotnum: 315, is_like: 0, min_price: 150 },
  { id: 32, name: '今晚吃鸡',        total_num: 1000,  flux: 500,   listimg: '/uploads/20261108/c125ef4fa80e2c6e6a3fe923af9d72b8.png', reprice_limit: '2000.00',  hotnum: 115, is_like: 0, min_price: 2000 },
  { id: 34, name: '弥勒-达观祥和',   total_num: 44350, flux: 44350, listimg: '/uploads/20261109/6d82a28e5130ee9dbddbe9a5c0c31c1e.jpg', reprice_limit: '50.00',    hotnum: 107, is_like: 0, min_price: 36 }
]

/* chunk getTitleStyle / getStatsStyle / getPriceColor / getTransactionButtonStyle
   按 rank（1..6）上色，逐字对齐压缩源码 */
export const HOT_STYLE = {
  title: {
    1: { fs: '12px', color: 'var(--rk-title-1)' },
    2: { fs: '12px', color: 'var(--rk-title-1)' },
    3: { fs: '12px', color: 'var(--rk-title-3)' },
    d: { fs: '14px', color: 'var(--rk-title-d)' }
  },
  stats: {
    1: 'var(--rk-stats-1)', 2: 'var(--rk-stats-1)',
    3: 'var(--rk-stats-3)', d: 'var(--rk-stats-d)'
  },
  price: {
    1: 'var(--rk-title-1)', 2: 'var(--rk-price-2)',
    3: 'var(--rk-title-3)', d: 'var(--rk-title-d)'
  },
  dealBg: { top: 'var(--rk-deal-top)', d: 'var(--rk-deal-d)' },
  dealText: 'var(--rk-deal-text)',
  rankNo: 'var(--rk-rank-no)'
}

export function routeQuery() {
  return new URLSearchParams(window.location.hash.split('?')[1] || '')
}

export function formatPrice(v) {
  const n = parseFloat(v)
  return Number.isFinite(n) ? String(Math.round(n)) : '0'
}
