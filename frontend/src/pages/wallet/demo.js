// 批次14 钱包数据契约层（P082）
// 图片全部为原站 https://h5.hnyiqin.asia/static/ 下载后本地化，零运行时原站依赖。
import cardBg from '../../assets/images/wallet-card-bg.png'
import addBtn from '../../assets/images/wallet-add-btn.png'
import backBtn from '../../assets/images/back_btn.png'

export const IMG = { card: cardBg, add: addBtn, back: backBtn }

export function routeQuery() {
  const hash = window.location.hash || ''
  const i = hash.indexOf('?')
  return new URLSearchParams(i >= 0 ? hash.slice(i + 1) : '')
}

/** 本地验收开关：模拟 unopen 分支（仅切换 computed 文案，数据仍走真实接口，不臆造态） */
export function isUnopenDemo() {
  return routeQuery().get('unopen') === '1'
}

/**
 * 原站 chunk `pages-wallet-wallet.35f56741.js`（模块 f410）data.walletList 原样锁定。
 * 注意：title / line1 / line2 是 chunk 内硬编码数据，但模板并未渲染它们——
 * 卡片上的「汇付天下 / 免费开通 / 无需开户费+超低手续费」是烘焙在 wallet-card-bg.png 里的，
 * 故本项目同样不渲染这三段文字，避免与原站 DOM 产生多余文本节点。
 */
export const WALLET_LIST = [
  {
    title: '汇付天下',
    line1: '免费开通',
    line2: '无需开户费+超低手续费',
    key: 'huifu',
    openstatus: false,
    authKey: 'huifu_account'
  }
]

// 原站 chunk 内硬编码文案（如实保留，不改写）
export const TXT = {
  navTitle: '我的钱包',
  btnEnter: '进入钱包',
  btnOpen: '开通此钱包',
  loading: '请稍后...',
  opening: '开户提交中',
  docking: '对接中',
  comingSoon: '敬请期待对接中'
}
