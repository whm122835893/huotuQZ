// 批次16 邀请域数据契约层（P084 邀请活动 / P085 邀请记录）
// 图片全部为原站 https://h5.hnyiqin.asia/static/ 与二维码接口下载后本地化，零运行时原站依赖。
import iconCopy from '../../assets/images/b16-icon-copy.png'
import searchIcon from '../../assets/images/b16-market-search.png'
import emptyData from '../../assets/images/b16-empty-data.png'
import qrcode from '../../assets/images/b16-invite-qrcode.png'
import backBtn from '../../assets/images/b16-back-btn.png'

export const IMG = {
  copy: iconCopy,
  search: searchIcon,
  empty: emptyData,
  qrcode: qrcode,
  back: backBtn
}

/** 从 hash query 读参数（如 ?tab=link / ?demo=1） */
export function routeQuery() {
  const hash = window.location.hash || ''
  const i = hash.indexOf('?')
  return new URLSearchParams(i >= 0 ? hash.slice(i + 1) : '')
}

/** 原站 chunk 硬编码文案（如实保留，不改写、不臆造） */
export const TXT = {
  navInvite: '邀请活动',
  navRecord: '邀请记录',
  tabCode: '我的邀请码',
  tabLink: '邀请链接',
  qrLabel: '二维码',
  assistTitle: '助力好友',
  assistPlaceholder: '填邀请码，助好友',
  assistBtn: '去助力',
  friendTitlePrefix: '好友列表（',
  friendTitleSuffix: '人）',
  searchPlaceholder: '搜索昵称或手机号',
  totalLabel: '总人数',
  toastEmptyCode: '请输入邀请码',
  toastAssistOk: '助力成功',
  toastCopy: '已复制'
}

/**
 * 原站 computed：h5_invite_url = config.info.h5_invite_url + "&content=" + userInfo.inviter_code
 * 侦查实测原站 config.info.h5_invite_url 为 undefined（该 key 未下发），
 * 故页面真实渲染 "undefined&content=HT173634"。按硬规则「不臆造」，如实复刻此串。
 */
export function inviteLinkText(code) {
  return 'undefined&content=' + code
}

/** 邀请码：原站 getUserInfo.inviter_code = HT173634（本地 ht_user 已有该列，零改动） */
export const DEFAULT_CODE = 'HT173634'
