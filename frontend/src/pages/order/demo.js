/**
 * 批次8 预览态同构占位数据（仅用于 ?demo=1 结构预览与像素对比）
 *
 * 与批次7 不同：订单域「常规订单列表/常规订单详情」在原站有真实数据，
 * 已把原站 4 条订单 + 订单详情按原字段种入本项目 MySQL（increment_v6_batch8.sql）。
 * 默认访问 list / defaultDetail 一律读自建后端的真实数据。
 *
 * 本文件仅为「原站当前无业务数据」的页面（marketDetail/regiftDetail/
 * begInfo/entrustInfo/orderSubmit/firstSaleConfirm/begList/entrustList）
 * 提供 ?demo=1 时的同构占位值，用于验证「有数据时」的 UI 骨架；
 * 字段与数值沿用原站真实订单（id=1493003）快照，视觉与文案 100% 来自原站模板。
 */

export const isDemo = () => {
  const hash = window.location.hash || ''
  const hq = hash.includes('?') ? hash.split('?').slice(1).join('&') : ''
  const sq = (window.location.search || '').replace(/^\?/, '')
  const q = new URLSearchParams([sq, hq].filter(Boolean).join('&'))
  return q.get('demo') === '1'
}

const IMG = new URL('../../assets/images/order-goods-tuer.jpg', import.meta.url).href

/** 原站真实订单 1493003 快照（字段名对齐 orderList/orderDetail 返回） */
export const REAL_ORDER = {
  id: 1493003, order_sn: '2026110718034310252571878789', goods_name: '2026兔耳',
  goods_cover_image: '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg',
  local_image: IMG, num: 1, price: '0.00', total_price: '0.00',
  order_type: 3, order_status: 2, pay_type: 1, series_name: '火兔2026',
  collection_code: ['61247/85268'], createtime: 1794045823, updatetime: 1794045823,
  createtime_text: '2026-11-07 18:03:43', pay_sn: '--',
}

/** 4 条真实订单（列表 demo 态复用，默认态由后端接口提供） */
export const REAL_ORDERS = [
  { ...REAL_ORDER, id: 1493003, order_sn: '2026110718034310252571878789', goods_name: '2026兔耳', local_image: IMG, createtime_text: '2026-11-07 18:03:43' },
  { ...REAL_ORDER, id: 1484414, order_sn: '2026110717580710249554132268', goods_name: '2026兔耳', local_image: IMG, collection_code: ['16044/85268'], createtime_text: '2026-11-07 17:58:07' },
  { ...REAL_ORDER, id: 1476919, order_sn: '2026110521024553564952958189', goods_name: '2026登机牌', local_image: new URL('../../assets/images/order-goods-dengjipai.jpg', import.meta.url).href, collection_code: ['377825/688888'], createtime_text: '2026-11-05 21:02:45' },
  { ...REAL_ORDER, id: 169114, order_sn: '2026110315393353555053848100', goods_name: '2026潮酷兔空投卡二', local_image: new URL('../../assets/images/order-goods-chaoku.jpg', import.meta.url).href, collection_code: ['93904/300000'], createtime_text: '2026-11-03 15:39:33' },
]

/** 求购/委托列表 demo 占位（原站为空数据） */
export const DEMO_BEG_LIST = [
  { id: 1, goods_name: '2026兔耳', goods_cover_image: '/uploads/20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', local_image: IMG, num: 2, price: '100.00', status: 1, createtime_text: '2026-11-07 18:03:43' },
]

/** 详情/确认页通用商品上下文 */
export const CONFIRM_PRODUCT = {
  id: 28, name: '2026兔耳', main_image: IMG, listimg: IMG, price: '0.00',
  goods_type: 2, total_num: 85268, purchasing_num: 1, is_like: 0,
  batch: { name: '第一期' }, publisher: { name: '火兔' },
}

/** 区块链地址（原站 userInfo.ntf_url 实测脱敏值） */
export const CHAIN_ADDR = '0x8a2****b423d'
