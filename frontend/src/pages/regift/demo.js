// 批次13 转赠数据契约层：hash query、图片本地映射、须知文案、时间格式化
import tuer from '../../assets/images/coll-tuer.jpg'
import emptyImg from '../../assets/images/empty-data.png'
import backBtn from '../../assets/images/back_btn.png'
import defAvatar from '../../assets/images/defaut-avatar.jpg'

export const IMG = { tuer, empty: emptyImg, back: backBtn, defAvatar }

export function routeQuery() {
  const hash = window.location.hash || ''
  const i = hash.indexOf('?')
  return new URLSearchParams(i >= 0 ? hash.slice(i + 1) : '')
}

// 原站图片路径 -> 本项目本地资源；零运行时原站依赖
const IMG_MAP = [
  ['20261107/4ba378fee1beb93954cd5401ee9385b6.jpg', tuer],
  ['empty-data.png', emptyImg],
  ['back_btn.png', backBtn],
  ['defautAvatar.png', defAvatar]
]

export function localImg(u) {
  const s = String(u || '')
  if (!s) return ''
  for (const [k, v] of IMG_MAP) if (s.includes(k)) return v
  return ''
}

// 原站 chunk 内硬编码文案（注意原站用「转增」，如实保留，不改写为「转赠」）
export const GIFT_TITLE = '温馨提示，一旦发起数字藏品转增，即代表你同意以下条件：'
export const GIFT_NOTICE = [
  '1. 转增成功后不可撤销，在使用场景（如付款码皮肤页面）将不再限时本数字藏品。',
  '2. 该数字藏品在受赠满2年后，可再次发起转赠。',
  '3. 我们不支持任何形式的有偿转赠，请警惕欺诈风险。一旦发现异常行为，我们会对相关账户采取限制措施。'
]
export const GIFT_TIP = '24小时内对方未领取，将退还给你'
export const SEARCH_PH = '输入用户手机号或钱包地址'

// 固定 UTC+8，避免浏览器时区影响
export function fmtDate(ts) {
  const t = Number(ts || 0)
  if (!t) return ''
  const d = new Date((t + 8 * 3600) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getUTCFullYear()}-${p(d.getUTCMonth() + 1)}-${p(d.getUTCDate())} ${p(d.getUTCHours())}:${p(d.getUTCMinutes())}:${p(d.getUTCSeconds())}`
}
