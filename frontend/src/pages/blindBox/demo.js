/**
 * 批次11 盲盒 —— 数据契约层
 *
 * 侦查结论（原站 2026-11-12）：
 *  - index / result / result2 三页由 uni-app H5 storage 驱动，不发业务 API：
 *      blindBoxInfo        -> {num, listimg, blindBoxId, id}      我的盲盒
 *      blindBoxResultList  -> [{name, collection_code, listimg, rarity_icon}]  一键开启结果
 *      blindBoxResult      -> {name, collection_code, main_image, rarity_icon} 逐个拆开结果
 *  - openRecord 调 /api/box/blind_box/openlog（原站该账号 total=0 恒空态）
 *  - blindBoxDetails 调 /api/user_collection/user_collection/detail/ids/<id>，无 id 白屏
 *  - openbox / openBoxs 原站对空盲盒返回 code:0，本项目自建后端如实同构返回
 *
 * 默认访问 = 原站空态（storage 无值 / 分页壳空）；?demo=1 仅做前端结构预览，不入库。
 */

/** hash 路由 + search 双通道取查询参数（兼容发布链接 ?sharecode=） */
export function routeQuery() {
  const hash = window.location.hash || ''
  const hq = hash.includes('?') ? hash.split('?').slice(1).join('&') : ''
  const sq = (window.location.search || '').replace(/^\?/, '')
  return new URLSearchParams([sq, hq].filter(Boolean).join('&'))
}
export const isDemo = () => routeQuery().get('demo') === '1'

/** uni-app H5 storage 契约：值为 {"type":"object","data":<obj>} 包装 */
export function readStore(key, fallback) {
  try {
    const raw = localStorage.getItem(key)
    if (!raw) return fallback
    const o = JSON.parse(raw)
    const v = o && typeof o === 'object' && 'data' in o ? o.data : o
    return v === null || v === undefined ? fallback : v
  } catch (e) { return fallback }
}
export function writeStore(key, val) {
  try { localStorage.setItem(key, JSON.stringify({ type: 'object', data: val })) } catch (e) { /* ignore */ }
}

/* 本地化图片（全部取自原站，打包进 assets 保证零原站运行时依赖） */
const IMG_PAOPAO = new URL('../../assets/images/bb-paopao.jpg', import.meta.url).href
const IMG_DENG = new URL('../../assets/images/bb-dengjipai.jpg', import.meta.url).href
const IMG_RARITY = new URL('../../assets/images/bb-rarity-icon.png', import.meta.url).href
const IMG_TUER = new URL('../../assets/images/coll-tuer.jpg', import.meta.url).href

/** 把原站 /uploads/... 路径映射到本地 assets 图 */
export function localImg(path) {
  if (!path) return ''
  if (path.includes('b15c345d56b5c3af601ae03834625014')) return IMG_PAOPAO
  if (path.includes('88748beb27fa6100fa3346938cb8edd0')) return IMG_DENG
  if (path.includes('4ba378fee1beb93954cd5401ee9385b6')) return IMG_TUER
  if (path.includes('rank_icon')) return IMG_RARITY
  if (path.startsWith('/static/')) return IMG_RARITY
  return path
}
export const RARITY_ICON = IMG_RARITY

/* ---- 结构预览快照（值来自原站真实快照，非虚构业务数据） ---- */

/* 盲盒商品 /api/box/blind_box/list 真实 2 条 */
export const DEMO_BOXES = [
  { id: 33, name: '2026泡泡探险', price: '11.11', total_num: 200000, listimg: IMG_PAOPAO, release_date_time_text: '2026-11-08 20:18' },
  { id: 20, name: '2026登机牌', price: '3.98', total_num: 688888, listimg: IMG_DENG, release_date_time_text: '2026-11-05 20:18' },
]

/* 我的盲盒 storage 结构（原站由上游购买流程写入，本项目仅结构预览） */
export const DEMO_BOX_INFO = { num: 3, listimg: IMG_PAOPAO, blindBoxId: 33, id: 33 }

/* 结果页 storage 结构：编号来自原站真实持仓实例（批次9 快照） */
export const DEMO_RESULT_LIST = [
  { name: '2026兔耳', collection_code: 61247, listimg: IMG_TUER, rarity_icon: IMG_RARITY },
  { name: '2026登机牌', collection_code: 377825, listimg: IMG_DENG, rarity_icon: IMG_RARITY },
]
export const DEMO_RESULT = { name: '2026兔耳', collection_code: 61247, main_image: IMG_TUER, rarity_icon: IMG_RARITY }

/* 开盒记录：原站 total=0，仅列表项结构预览（昵称/藏品名取真实值） */
export const DEMO_OPEN_LOG = [
  { id: 1, userss: { nickname: '175****1293' }, createtime: 1794045823, collections: { name: '2026兔耳' } },
  { id: 2, userss: { nickname: '175****1293' }, createtime: 1794045823, collections: { name: '2026登机牌' } },
]

/** 原站 formatDate 输出（yyyy-mm-dd） */
export function fmtDate(ts) {
  const d = new Date(Number(ts || 0) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`
}
