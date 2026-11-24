/**
 * 批次9 我的资产 —— 结构预览兜底 + 图片本地映射
 *
 * 说明：资产域原站有真实数据，本项目已按原字段全部种入自建 MySQL
 * （increment_v7_batch9.sql），默认访问读自建后端。本文件仅在 ?demo=1
 * 或后端不可用时提供同构占位，占位值来自原站 2026-11-11 真实快照，非虚构业务数据。
 */
export const isDemo = () => {
  const hash = window.location.hash || ''
  const hq = hash.includes('?') ? hash.split('?').slice(1).join('&') : ''
  const sq = (window.location.search || '').replace(/^\?/, '')
  const q = new URLSearchParams([sq, hq].filter(Boolean).join('&'))
  return q.get('demo') === '1'
}

/** 读取 hash 路由里的查询参数（兼容发布链接 ?sharecode= 在 search 中） */
export function routeQuery() {
  const hash = window.location.hash || ''
  const hq = hash.includes('?') ? hash.split('?').slice(1).join('&') : ''
  const sq = (window.location.search || '').replace(/^\?/, '')
  return new URLSearchParams([sq, hq].filter(Boolean).join('&'))
}

/* 3 张藏品真实图（md5 与订单域 order-goods-*.jpg 一致），打包进 assets 保证零原站依赖 */
const IMG_TUER = new URL('../../assets/images/coll-tuer.jpg', import.meta.url).href
const IMG_DENG = new URL('../../assets/images/coll-dengjipai.jpg', import.meta.url).href
const IMG_CHAO = new URL('../../assets/images/coll-chaoku.jpg', import.meta.url).href

/** 把 /uploads/... 原站路径映射到本项目本地 assets 图 */
export function localImg(path) {
  if (!path) return ''
  if (path.includes('4ba378fee1beb93954cd5401ee9385b6')) return IMG_TUER
  if (path.includes('88748beb27fa6100fa3346938cb8edd0')) return IMG_DENG
  if (path.includes('27a8d4c90fbc3dc59b26092839f0a680')) return IMG_CHAO
  return path
}

/* 藏品定义（原站真实） */
export const DEMO_COLLECTIONS = [
  { id: 28, name: '2026兔耳', main_image: IMG_TUER, total_num: 85268, publisher_name: '火兔', series_name: '火兔2026' },
  { id: 20, name: '2026登机牌', main_image: IMG_DENG, total_num: 688888, publisher_name: '火兔', series_name: '火兔2026' },
  { id: 3, name: '2026潮酷兔空投卡二', main_image: IMG_CHAO, total_num: 300000, publisher_name: '火兔', series_name: '火兔2026' },
]

/* 主列表 3 项（原站 antMycollection{types:2} 快照） */
export const DEMO_LIST = [
  { collection_id: 28, num: 2, sell_num: '0', collection: DEMO_COLLECTIONS[0] },
  { collection_id: 20, num: 1, sell_num: '0', collection: DEMO_COLLECTIONS[1] },
  { collection_id: 3, num: 1, sell_num: '0', collection: DEMO_COLLECTIONS[2] },
]

/* 各藏品的持仓实例（原站 getDetail 快照） */
export const DEMO_ITEMS = {
  28: [
    { id: 2643699, collection_id: 28, collection_code: 61247, from_price: null, sale_price: null },
    { id: 2598496, collection_id: 28, collection_code: 16044, from_price: null, sale_price: null },
  ],
  20: [{ id: 2243825, collection_id: 20, collection_code: 377825, from_price: null, sale_price: null }],
  3: [{ id: 104904, collection_id: 3, collection_code: 93904, from_price: null, sale_price: null }],
}

/* 搜索联想（原站 likeCollection{keyword:"2026"} 快照） */
export const DEMO_SEARCH = [
  { collection_id: 3, name: '2026潮酷兔空投卡二' },
  { collection_id: 20, name: '2026登机牌' },
  { collection_id: 28, name: '2026兔耳' },
]
