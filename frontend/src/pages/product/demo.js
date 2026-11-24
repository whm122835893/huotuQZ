// 批次12 商品详情数据契约层：hash query、demo 态、图片本地映射、格式化
import paopao from '../../assets/images/bb-paopao.jpg'
import dengjipai from '../../assets/images/bb-dengjipai.jpg'
import emptyImg from '../../assets/images/empty-data.png'
import avatar from '../../assets/images/mine-default-avatar.png'
import backBtn from '../../assets/images/back_btn.png'
import icoFollow from '../../assets/images/detail-unfollow.png'
import icoNotice from '../../assets/images/detail-notice.png'
import icoMarket from '../../assets/images/detail-market.png'

export const IMG = { empty: emptyImg, avatar, back: backBtn, icoFollow, icoNotice, icoMarket }

export function routeQuery() {
  const hash = window.location.hash || ''
  const i = hash.indexOf('?')
  return new URLSearchParams(i >= 0 ? hash.slice(i + 1) : '')
}

export function isDemo() {
  return routeQuery().get('demo') === '1'
}

// 原站图片路径 -> 本项目本地资源；零运行时原站依赖
const IMG_MAP = [
  ['20261108/b15c345d56b5c3af601ae03834625014.jpg', paopao],
  ['20261103/88748beb27fa6100fa3346938cb8edd0.jpg', dengjipai],
  ['empty-data.png', emptyImg],
  ['mine-default-avatar.png', avatar],
  ['back_btn.png', backBtn],
  ['detail-unfollow.png', icoFollow],
  ['detail-notice.png', icoNotice],
  ['detail-market.png', icoMarket]
]

export function localImg(u) {
  const s = String(u || '')
  if (!s) return ''
  for (const [k, v] of IMG_MAP) if (s.includes(k)) return v
  return ''
}

export function formatNum(n) {
  const v = Number(n || 0)
  if (!Number.isFinite(v)) return '--'
  return String(v).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

// 固定 UTC+8，避免浏览器时区影响与原站日期不一致
export function fmtDate(ts) {
  const t = Number(ts || 0)
  if (!t) return ''
  const d = new Date((t + 8 * 3600) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getUTCFullYear()}-${p(d.getUTCMonth() + 1)}-${p(d.getUTCDate())} ${p(d.getUTCHours())}:${p(d.getUTCMinutes())}:${p(d.getUTCSeconds())}`
}

// 原站 chunk 内硬编码的购买须知正文（首屏下方，滚动可见）
export const BUY_NOTICE = '为更好服务平台的各位用户，特此公告数字藏品的二次交易细则。鉴于不同属性的数字艺术的二次交易细则各不相同，请各位用户务必仔细阅读并同意相关规则后进行相应操作，以免造成不必要的损失1.依照我国法律要求，特定条件下的数字藏品的二次交易不支持任何形式的变相炒作，我们坚决反对数字藏品炒作、场外交易、欺诈，或以任何其他非法、侵权方式使用的违规行为，请大家注意甄别上述涉嫌网络欺诈的行为及其相关风险。2.数字藏品为虚拟数字商品，仅限18周岁以上，60周岁以下且实名认证的中国大陆用户购买。3.本商品一经售出不支持退货。'
