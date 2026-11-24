// 批次17 返佣域数据契约层（P086 排行榜 / P087 返佣主页 / P088 被邀请人清单）
// 图片全部为原站 https://h5.hnyiqin.asia/static/ 下载后本地化，零运行时原站依赖。
// 文案全部来自原站 chunk pages-rebate-rebate.90a45f76.js 及页面实测 DOM，逐字如实复刻。
import backBtn from '../../assets/images/b17-back-btn.png'
import emptyData from '../../assets/images/b17-empty-data.png'
import posterBg from '../../assets/images/b17-mine-invite-modal-bg.png'
import qrcode from '../../assets/images/b17-poster-qrcode.png'
import modalClose from '../../assets/images/common-modal-close.png'
import defaultAvatar from '../../assets/images/mine-default-avatar.png'

export const IMG = {
  back: backBtn,
  empty: emptyData,
  posterBg: posterBg,
  qrcode: qrcode,
  close: modalClose,
  avatar: defaultAvatar
}

/** 从 hash query 读参数（如 ?demo=1 / ?tab=week） */
export function routeQuery() {
  const hash = window.location.hash || ''
  const i = hash.indexOf('?')
  return new URLSearchParams(i >= 0 ? hash.slice(i + 1) : '')
}

/** P086 排行榜：原站实测三 Tab 文案（u-tabs__wrapper__nav__item__text） */
export const LB = {
  tabs: ['日排行', '周排行', '月排行'],
  heads: ['排名', '用户', '返佣金额'],
  empty: '目前没有任何数据'
}

/** P088 被邀请人清单：原站实测 navbar 标题 + 5 列表头 */
export const RD = {
  title: '被邀请人清单',
  heads: ['昵称', '加入时间', '首次交易日期', '总交易金额', '本自然月交易金额'],
  empty: '目前没有任何数据'
}

/** P087 返佣主页：原站 chunk 真实模板文案（逐字，不改写不臆造） */
export const RB = {
  defaultLinkTitle: '默认邀请链接',
  inviteCode: '邀请码',
  invitePoster: '邀请海报',
  inviteLinkPrefix: '邀请链接: ',
  dataTitle: '数据详情',
  allCommision: '总返佣金额',
  currentCommision: '本期返佣金额',
  userTransaction: '名下用户总交易金额',
  userCommision: '名下用户总佣金',
  invited: '被邀请人',
  inviteCount: '邀请人数',
  validInviteCount: '有效邀请人数',
  nodeRank: '节点排行榜',
  tipCurrent: '指前一天的返金额',
  tipValid: '有交易记录的邀请人数',
  know: '我知道了'
}

/**
 * P087 demo 注入态快照：
 * 原站 onLoad -> commision_info() 实测返回 404（An error occurred），
 * loading 始终为 true -> w-container 整页被 loading 遮罩盖住 -> 白屏，真实态不做臆造渲染。
 * 本快照仅字段名来自 chunk 模板 c_info.*，值取主账号 getUserInfo 真实值/空账户 0 值，
 * 供 ?demo=1 注入态还原 chunk 真实模板的完整 UI（批次12 BoxDetail 双态同款做法）。
 */
export const RB_SNAPSHOT = {
  avatar: '',
  nickname: '175****1293',
  inviter_code: 'HT173634',
  all_commision: 0,
  yesterday_commision: 0,
  user_transaction: 0,
  mingxia_commision: 0,
  yao_counts: 0,
  you_yao_counts: 0
}

/** 原站 computed：h5_invite_url = config.info.h5_invite_url + "&content=" + inviter_code
 *  批次16 已实测 config.info.h5_invite_url 未下发（undefined），如实复刻，不臆造补全。 */
export function inviteLinkText(code) {
  return 'undefined&content=' + code
}

/** chunk copyInviteUrl 掩码规则：前 10 位 + "******" + 后 5 位 */
export function maskedInviteLink(url) {
  return String(url).slice(0, 10) + '******' + String(url).slice(-5)
}

/** 「我的」页邀请海报浮层（原站实测 DOM，页内弹层非跳转） */
export const POSTER = {
  codeLabel: '邀请码',
  copyBtn: '复制链接',
  saveBtn: '保存海报',
  scanTip: '扫码加入我们吧~',
  code: 'HT173634',
  toastCopy: '已复制',
  toastSave: '海报已保存到相册'
}
