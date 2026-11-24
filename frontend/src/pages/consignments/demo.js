/**
 * 批次10 寄售大厅 —— 结构预览兜底 + 图片本地映射
 *
 * 说明：寄售表单页（P069）原站需带 ?id=用户藏品实例id，无 id 时整页白屏；
 * P070/P071 原站该账号寄售订单 total=0 恒为空态。本项目默认读自建后端
 * 如实还原，本文件仅在 ?demo=1 或后端不可用时提供同构占位，
 * 占位值全部来自原站 2026-11-11 真实快照，非虚构业务数据。
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

/* 3 张藏品真实图（与批次9/订单域同一批资源），打包进 assets 保证零原站依赖 */
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

/* P069 寄售表单：detail/ids/{实例id} 真实快照（3 个藏品实例） */
export const DEMO_DETAIL = {
  2643699: {
    id: 2643699, collection_code: 61247, from_price: null,
    product: { id: 28, name: '2026兔耳', main_image: IMG_TUER,
      reprice_limit: '5.00', reprice_min_limit: '2.00', platform_resale_rate: 6 },
  },
  2243825: {
    id: 2243825, collection_code: 377825, from_price: null,
    product: { id: 20, name: '2026登机牌', main_image: IMG_DENG,
      reprice_limit: '5.00', reprice_min_limit: '1.00', platform_resale_rate: 6 },
  },
  104904: {
    id: 104904, collection_code: 93904, from_price: null,
    product: { id: 3, name: '2026潮酷兔空投卡二', main_image: IMG_CHAO,
      reprice_limit: '0.00', reprice_min_limit: '0.00', platform_resale_rate: 5 },
  },
}

/* show_resale_money 配置真实快照（price=100 基准） */
export const DEMO_RATE = {
  pay_platform_service_rate: '0.06', share_proportion_rate: '0.00',
  platform_service_price: '6.00', share_proportion_price: '0.00', real_price: '94.00',
  low_price: 5, low_price_text: '五元',
  tips: '为保护寄售市场整体用户权益，寄售出价下限为昨日平均成交价90%，若低于最低限价或高于最高限价藏品将无法寄售，敬请知悉！',
}

/* P070/P071 有数据态结构预览（字段来自原站 myConsignmentsItem 组件引用） */
export const DEMO_CONSIGNMENT = [
  {
    id: 1, goods_name: '2026兔耳', goods_cover_image: IMG_TUER, goods_type: 2,
    price: '100.00', status: 1,
    user_collection: { collection_code: 61247, total_num: 85268 },
  },
]
