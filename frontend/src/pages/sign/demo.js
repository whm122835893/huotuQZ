// 批次15 签到页数据契约层（P083）
// 图片全部为原站 https://h5.hnyiqin.asia/static/ 下载后本地化，零运行时原站依赖。
// 原站 chunk：pages-sign-index.07b21d92.js（页面模块 9b1b / 接口层模块 5f3a）
import bg from '../../assets/images/checkin-bg.png'
import backBtn from '../../assets/images/back_btn.png'
import avatar from '../../assets/images/mine-default-avatar.png'
import arrowL from '../../assets/images/checkin-arrow-left.png'
import arrowR from '../../assets/images/checkin-arrow-right.png'
import close from '../../assets/images/common-modal-close.png'

export const IMG = { bg, back: backBtn, avatar, arrowL, arrowR, close }

/**
 * 侦查时刻快照基准（原站响应顶层 time=1794588752 → GMT+8 2026-11-14 00:52:32）。
 * 固定基准而非浏览器当前时间：原站按 GMT+8 判定「今」，沙箱为 UTC，
 * 若用 new Date() 会漂移到 13 号，日历与像素对比无法复现。
 */
export const SNAPSHOT = { year: 2026, month: 11, day: 14, time: 1794588752 }

/** 原站 chunk data.weeks 与硬编码文案，逐字保留 */
export const WEEKS = ['日', '一', '二', '三', '四', '五', '六']
export const TXT = {
  title: '签到有礼',
  subtitle: '连续签到获取官方奖励！',
  rule: '活动规则',
  ruleEmpty: '暂无活动规则',
  recordsEmpty: '暂无奖励记录',
  streakPrefix: '您已连续签到',
  streakSuffix: '天',
  signedTitle: '已签到',
  signedSub: '请继续保持~',
  unsignedTitle: '未签到',
  unsignedSub: '快去签到吧!',
  uidPrefix: 'UID ',
  cellSigned: '已签到',
  cellUnsigned: '未签',
  today: '今',
  btnSign: '立即签到',
  btnSigned: '今日已签到',
  loading: '签到中...',
  signFail: '签到失败',
  recordFail: '获取签到记录失败',
  successTitle: '签到成功',
  successBtn: '我知道了'
}

/**
 * 原站 rebuildCalendarItems() 等价实现：
 * 前置补上月尾（当月 1 号星期几），当月 1..月末，后置补下月头至 7 的倍数。
 * 全部用 Date.UTC 计算，规避时区漂移。
 */
export function buildCalendarItems(year, month, signedDays) {
  const firstWeekday = new Date(Date.UTC(year, month - 1, 1)).getUTCDay() // 0=周日
  const daysInMonth = new Date(Date.UTC(year, month, 0)).getUTCDate()
  const daysInPrev = new Date(Date.UTC(year, month - 1, 0)).getUTCDate()
  const todayTs = Date.UTC(SNAPSHOT.year, SNAPSHOT.month - 1, SNAPSHOT.day)
  const items = []

  for (let b = 0; b < firstWeekday; b++) {
    items.push({ day: daysInPrev - firstWeekday + b + 1, isOtherMonth: true, isToday: false, isFuture: false, isSigned: false })
  }
  for (let c = 1; c <= daysInMonth; c++) {
    const isToday = year === SNAPSHOT.year && month === SNAPSHOT.month && c === SNAPSHOT.day
    const ts = Date.UTC(year, month - 1, c)
    items.push({
      day: c,
      isOtherMonth: false,
      isToday,
      isSigned: signedDays.includes(c),
      isFuture: !isToday && ts > todayTs
    })
  }
  const rest = (7 - (items.length % 7)) % 7
  for (let u = 1; u <= rest; u++) {
    items.push({ day: u, isOtherMonth: true, isToday: false, isFuture: false, isSigned: false })
  }
  return items
}

/** 原站 dayInnerStyle / dayNumStyle 等价：用语义 class 映射，颜色全部由 CSS Token 提供 */
export function cellClass(it) {
  if (!it || it.day <= 0) return 'sg-cell--empty'
  if (it.isOtherMonth) return 'sg-cell--other'
  if (it.isSigned) return 'sg-cell--signed'
  if (it.isFuture) return 'sg-cell--future'
  return 'sg-cell--past'
}

/** 原站日历格状态文字渲染条件 */
export function statusText(it) {
  if (!it || it.isOtherMonth || it.day <= 0) return ''
  if (it.isSigned) return TXT.cellSigned
  if (it.isFuture) return ''
  return TXT.cellUnsigned
}

/** 月份加减，返回 YYYY-MM */
export function shiftMonth(year, month, delta) {
  const d = new Date(Date.UTC(year, month - 1 + delta, 1))
  const y = d.getUTCFullYear()
  const m = d.getUTCMonth() + 1
  return { year: y, month: m, text: `${y}-${String(m).padStart(2, '0')}` }
}

/** 原站 canGoNextMonth：不能超过快照所在月 */
export function canGoNextMonth(year, month) {
  return year < SNAPSHOT.year || (year === SNAPSHOT.year && month < SNAPSHOT.month)
}

/** 从 monthSign 的 {"04":"04","05":"05"} 解析出已签日期数组 */
export function parseSignedDays(data) {
  const out = []
  if (data && typeof data === 'object') {
    Object.values(data).forEach(v => {
      const n = parseInt(v, 10)
      if (n >= 1 && n <= 31) out.push(n)
    })
  }
  return out
}
