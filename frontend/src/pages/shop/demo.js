/**
 * 批次7 预览态同构占位数据（仅用于 ?demo=1 结构预览与像素对比）
 *
 * 重要：这不是原站真实业务数据。原站龙晶商城当前未开放，
 * list / crystalDetail / exchangeRecord 均返回 total=0，info 返回「商品不存在」。
 * 本文件仅按原站接口契约的字段名构造占位值，用于验证「有数据时」的 UI 骨架；
 * 默认访问（不带 demo 参数）一律以自建后端的真实空数据态呈现，与原站一致。
 */
export const isDemo = () => {
  // hash 模式下预览参数在 # 之后；发布链接的 ?sharecode= 在 search 中，两者都需兼容
  const hash = window.location.hash || ''
  const hq = hash.includes('?') ? hash.split('?').slice(1).join('&') : ''
  const sq = (window.location.search || '').replace(/^\?/, '')
  const q = new URLSearchParams([sq, hq].filter(Boolean).join('&'))
  return q.get('demo') === '1'
}

const IMG1 = new URL('../../assets/images/shop-goods-demo.png', import.meta.url).href
const IMG2 = new URL('../../assets/images/shop-goods-demo2.png', import.meta.url).href

export const DEMO_GOODS = [
  {
    id: 1, goods_id: 1, type: 10, goods_type: 10, goods_name: '龙晶兑换藏品',
    list_image: IMG1, main_image: IMG1, crystal_type: 'yellow', crystal_number: 100,
    limited: 500, stock: 88, exchange_status: 2, status: 1
  },
  {
    id: 2, goods_id: 2, type: 20, goods_type: 20, goods_name: '龙晶兑换盲盒',
    list_image: IMG2, main_image: IMG2, crystal_type: 'purple', crystal_number: 260,
    limited: 500, stock: 88, exchange_status: 2, status: 1
  }
]

export const DEMO_INFO = {
  ...DEMO_GOODS[0],
  type: 30, goods_type: 30,
  description: '数字藏品描述',
  content: '<p>数字藏品描述</p>',
  product: {
    id: 1, name: '龙晶兑换藏品', listimg: IMG1, main_image: IMG1, goods_type: 30,
    total_num: 500, crystal: { crystal_type: 'yellow', crystal_price: 100, crystal_number: 100, stock: 88 }
  }
}

export const DEMO_DETAIL = [
  { id: 1, title: '签到奖励', time: '2026.11.09 00:06:40', type: 1 },
  { id: 2, title: '兑换商品', time: '2026.11.08 23:00:00', type: 2 }
]

export const DEMO_RECORD = [
  { id: 1, goods_name: '龙晶兑换藏品', list_image: IMG1, crystal_number: 100, num: 1, create_time: '2026.11.08 12:00:00' }
]

export const CRYSTAL_LABEL = { yellow: '黄水晶', purple: '紫水晶', blue: '蓝水晶' }
