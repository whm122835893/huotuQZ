import { createRouter, createWebHashHistory } from 'vue-router'

/**
 * 路由结构与原站 hash 路由保持一致（#/pages/...）
 * 逐页复刻、逐页验收：已实现 login 域 7 页 + 首页。
 */
const routes = [
  {
    path: '/',
    name: 'index',
    component: () => import('../pages/index/index.vue'),
    meta: { title: '火兔', isTab: true }
  },
  {
    path: '/pages/index/index',
    name: 'indexAlt',
    component: () => import('../pages/index/index.vue'),
    meta: { title: '火兔', isTab: true }
  },
  {
    path: '/pages/login/login',
    name: 'login',
    component: () => import('../pages/login/login.vue'),
    meta: { title: '登录' }
  },
  {
    path: '/pages/login/register',
    name: 'register',
    component: () => import('../pages/login/register.vue'),
    meta: { title: '注册' }
  },
  {
    path: '/pages/login/captchaLogin',
    name: 'captchaLogin',
    component: () => import('../pages/login/captchaLogin.vue'),
    meta: { title: '验证码登录' }
  },
  {
    path: '/pages/login/captchaConfirm',
    name: 'captchaConfirm',
    component: () => import('../pages/login/captchaConfirm.vue'),
    meta: { title: '输入短信验证码' }
  },
  {
    path: '/pages/login/verification',
    name: 'verification',
    component: () => import('../pages/login/verification.vue'),
    meta: { title: '安全验证' }
  },
  {
    path: '/pages/login/forgetPwd',
    name: 'forgetPwd',
    component: () => import('../pages/login/forgetPwd.vue'),
    meta: { title: '忘记密码' }
  },
  {
    path: '/pages/login/setPwd',
    name: 'setPwd',
    component: () => import('../pages/login/setPwd.vue'),
    meta: { title: '设置新密码' }
  },
  {
    path: '/pages/my/my',
    name: 'my',
    component: () => import('../pages/my/my.vue'),
    meta: { title: '我的', isTab: true }
  },
  {
    path: '/pages/market/market',
    name: 'market',
    component: () => import('../pages/market/market.vue'),
    meta: { title: '市场', isTab: true }
  },
  {
    path: '/pages/notice/notice',
    name: 'notice',
    component: () => import('../pages/notice/notice.vue'),
    meta: { title: '发现', isTab: true }
  },
  {
    path: '/pages/notice/noticeDetails',
    name: 'noticeDetails',
    component: () => import('../pages/notice/noticeDetails.vue'),
    meta: { title: '官方公告' }
  },
  {
    path: '/pages/notice/newsDetails',
    name: 'newsDetails',
    component: () => import('../pages/notice/newsDetails.vue'),
    meta: { title: '新闻发声' }
  },
  {
    path: '/pages/notice/search',
    name: 'noticeSearch',
    component: () => import('../pages/notice/search.vue'),
    meta: { title: '搜索' }
  },
  {
    path: '/pages/notice/sendRumor',
    name: 'sendRumor',
    component: () => import('../pages/notice/sendRumor.vue'),
    meta: { title: '发表' }
  },
  {
    path: '/pages/notice/wallDetails',
    name: 'wallDetails',
    component: () => import('../pages/notice/wallDetails.vue'),
    meta: { title: '辟谣详情' }
  },
  {
    path: '/pages/compound/compound',
    name: 'compound',
    component: () => import('../pages/compound/compound.vue'),
    meta: { title: '合成', isTab: true }
  },
  {
    path: '/pages/compound/record',
    name: 'compoundRecord',
    component: () => import('../pages/compound/record.vue'),
    meta: { title: '活动记录' }
  },
  {
    path: '/pages/compound/result',
    name: 'compoundResult',
    component: () => import('../pages/compound/result.vue'),
    meta: { title: '参与结果' }
  },
  {
    path: '/pages/compound/schemeDetails',
    name: 'schemeDetails',
    component: () => import('../pages/compound/schemeDetails.vue'),
    meta: { title: '合成详情' }
  },
  // 批次7 水晶（龙晶）商城域 P047-P053
  {
    path: '/pages/shop/shop',
    name: 'shop',
    component: () => import('../pages/shop/shop.vue'),
    meta: { title: '龙晶商城' }
  },
  {
    path: '/pages/shop/detailList',
    name: 'shopDetailList',
    component: () => import('../pages/shop/detailList.vue'),
    meta: { title: '龙晶明细' }
  },
  {
    path: '/pages/shop/record',
    name: 'shopRecord',
    component: () => import('../pages/shop/record.vue'),
    meta: { title: '兑换记录' }
  },
  {
    path: '/pages/shop/exchange',
    name: 'shopExchange',
    component: () => import('../pages/shop/exchange.vue'),
    meta: { title: '确认订单' }
  },
  {
    path: '/pages/shop/detail/goods',
    name: 'shopDetailGoods',
    component: () => import('../pages/shop/detailGoods.vue'),
    meta: { title: '商品详情' }
  },
  {
    path: '/pages/shop/detail/collection',
    name: 'shopDetailCollection',
    component: () => import('../pages/shop/detailCollection.vue'),
    meta: { title: '藏品详情' }
  },
  {
    path: '/pages/shop/detail/box',
    name: 'shopDetailBox',
    component: () => import('../pages/shop/detailBox.vue'),
    meta: { title: '盲盒详情' }
  },
  // 订单域（批次8：P054-P064）
  {
    path: '/pages/order/list',
    name: 'orderList',
    component: () => import('../pages/order/list.vue'),
    meta: { title: '订单列表' }
  },
  {
    path: '/pages/order/search',
    name: 'orderSearch',
    component: () => import('../pages/order/search.vue'),
    meta: { title: '订单搜索' }
  },
  {
    path: '/pages/order/defaultDetail',
    name: 'orderDefaultDetail',
    component: () => import('../pages/order/defaultDetail.vue'),
    meta: { title: '订单详情' }
  },
  {
    path: '/pages/order/marketDetail',
    name: 'orderMarketDetail',
    component: () => import('../pages/order/marketDetail.vue'),
    meta: { title: '订单详情' }
  },
  {
    path: '/pages/order/regiftDetail',
    name: 'orderRegiftDetail',
    component: () => import('../pages/order/regiftDetail.vue'),
    meta: { title: '赠送订单详情' }
  },
  {
    path: '/pages/order/orderSubmit',
    name: 'orderSubmit',
    component: () => import('../pages/order/orderSubmit.vue'),
    meta: { title: '确认订单' }
  },
  {
    path: '/pages/order/firstSaleConfirm',
    name: 'orderFirstSaleConfirm',
    component: () => import('../pages/order/firstSaleConfirm.vue'),
    meta: { title: '订单确认' }
  },
  {
    path: '/pages/order/beg/list',
    name: 'orderBegList',
    component: () => import('../pages/order/begList.vue'),
    meta: { title: '我的求购' }
  },
  {
    path: '/pages/order/beg/info',
    name: 'orderBegInfo',
    component: () => import('../pages/order/begInfo.vue'),
    meta: { title: '求购详情' }
  },
  {
    path: '/pages/order/entrust/list',
    name: 'orderEntrustList',
    component: () => import('../pages/order/entrustList.vue'),
    meta: { title: '我的委托' }
  },
  {
    path: '/pages/order/entrust/info',
    name: 'orderEntrustInfo',
    component: () => import('../pages/order/entrustInfo.vue'),
    meta: { title: '委托详情' }
  },
  // 我的资产域（批次9：P065-P068）
  {
    path: '/pages/myCollection/myCollection',
    name: 'myCollection',
    component: () => import('../pages/myCollection/myCollection.vue'),
    meta: { title: '我的藏品' }
  },
  {
    path: '/pages/myCollection/holdingsDetail',
    name: 'mcHoldingsDetail',
    component: () => import('../pages/myCollection/holdingsDetail.vue'),
    meta: { title: '持仓详情' }
  },
  {
    path: '/pages/myCollection/collectionDetails',
    name: 'mcCollectionDetails',
    component: () => import('../pages/myCollection/collectionDetails.vue'),
    meta: { title: '藏品详情' }
  },
  {
    path: '/pages/myCollection/search',
    name: 'mcSearch',
    component: () => import('../pages/myCollection/search.vue'),
    meta: { title: '搜索' }
  },
  // 寄售大厅域（批次10：P069-P071）
  {
    path: '/pages/consignments/consignments',
    name: 'consignments',
    component: () => import('../pages/consignments/consignments.vue'),
    meta: { title: '寄售' }
  },
  {
    path: '/pages/consignments/myConsignment',
    name: 'myConsignment',
    component: () => import('../pages/consignments/myConsignment.vue'),
    meta: { title: '我的寄售' }
  },
  {
    path: '/pages/consignments/search',
    name: 'csSearch',
    component: () => import('../pages/consignments/search.vue'),
    meta: { title: '搜索' }
  },
  // 盲盒域（批次11：P072-P076）
  {
    path: '/pages/blindBox/index',
    name: 'bbIndex',
    component: () => import('../pages/blindBox/index.vue'),
    meta: { title: '我的盲盒' }
  },
  {
    path: '/pages/blindBox/blindBoxDetails',
    name: 'bbDetails',
    component: () => import('../pages/blindBox/blindBoxDetails.vue'),
    meta: { title: '盲盒详情' }
  },
  {
    path: '/pages/blindBox/openRecord',
    name: 'bbOpenRecord',
    component: () => import('../pages/blindBox/openRecord.vue'),
    meta: { title: '开盒记录' }
  },
  {
    path: '/pages/blindBox/result',
    name: 'bbResult',
    component: () => import('../pages/blindBox/result.vue'),
    meta: { title: '盲盒结果' }
  },
  {
    path: '/pages/blindBox/result2',
    name: 'bbResult2',
    component: () => import('../pages/blindBox/result2.vue'),
    meta: { title: '盲盒结果' }
  },
  // 商品详情域（批次12：P077-P079）
  {
    path: '/pages/product/box',
    name: 'productBox',
    component: () => import('../pages/product/box.vue'),
    meta: { title: '盲盒详情' }
  },
  {
    path: '/pages/product/collection',
    name: 'productCollection',
    component: () => import('../pages/product/collection.vue'),
    meta: { title: '藏品详情' }
  },
  {
    path: '/pages/product/notice',
    name: 'productNotice',
    component: () => import('../pages/product/notice.vue'),
    meta: { title: '相关公告' }
  },
  {
    path: '/pages/regift/record',
    name: 'regiftRecord',
    component: () => import('../pages/regift/record.vue'),
    meta: { title: '转赠记录' }
  },
  {
    path: '/pages/regift/regift',
    name: 'regift',
    component: () => import('../pages/regift/regift.vue'),
    meta: { title: '转赠' }
  },
  {
    path: '/pages/wallet/wallet',
    name: 'wallet',
    component: () => import('../pages/wallet/wallet.vue'),
    meta: { title: '我的钱包' }
  },
  {
    path: '/pages/sign/index',
    name: 'sign',
    component: () => import('../pages/sign/index.vue'),
    meta: { title: '签到有礼' }
  },
  {
    path: '/pages/invitation/invitation',
    name: 'invitation',
    component: () => import('../pages/invitation/invitation.vue'),
    meta: { title: '邀请活动' }
  },
  {
    path: '/pages/invitation/invitationrecord',
    name: 'invitationrecord',
    component: () => import('../pages/invitation/invitationrecord.vue'),
    meta: { title: '邀请记录' }
  },
  // 批次17 返佣域（P086-P088）
  {
    path: '/pages/rebate/leaderboard',
    name: 'rebateLeaderboard',
    component: () => import('../pages/rebate/leaderboard.vue'),
    meta: { title: '返佣排行榜' }
  },
  {
    path: '/pages/rebate/rebate',
    name: 'rebate',
    component: () => import('../pages/rebate/rebate.vue'),
    meta: { title: '返佣' }
  },
  {
    path: '/pages/rebate/rebateInviteDetail',
    name: 'rebateInviteDetail',
    component: () => import('../pages/rebate/rebateInviteDetail.vue'),
    meta: { title: '被邀请人清单' }
  },
  // 批次3 占位落地页：设置(P023) / 抽奖活动(P092)
  {
    path: '/pages/my/setting',
    name: 'mySetting',
    component: () => import('../pages/my/setting.vue'),
    meta: { title: '设置' }
  },
  {
    path: '/pages/lottery/lottery',
    name: 'lottery',
    component: () => import('../pages/lottery/lottery.vue'),
    meta: { title: '抽奖活动' }
  },
  // 批次18 排行三页 P089-P091
  {
    path: '/pages/rank/masterRank',
    name: 'masterRank',
    component: () => import('../pages/rank/masterRank.vue'),
    meta: { title: '大师动态' }
  },
  {
    path: '/pages/rank/rankList',
    name: 'rankList',
    component: () => import('../pages/rank/rankList.vue'),
    meta: { title: '名人堂' }
  },
  {
    path: '/pages/rank/userBuy',
    name: 'userBuy',
    component: () => import('../pages/rank/userBuy.vue'),
    meta: { title: '热榜' }
  },
  {
    path: '/pages/lottery/record',
    name: 'lotteryRecord',
    component: () => import('../pages/lottery/record.vue'),
    meta: { title: '中奖记录' }
  },
  {
    path: '/pages/lottery/rule',
    name: 'lotteryRule',
    component: () => import('../pages/lottery/rule.vue'),
    meta: { title: '抽奖活动规则' }
  },
  // 批次20 抽奖活动五页 P095-P099
  {
    path: '/pages/draw/communityShare/communityShare',
    name: 'drawCommunityShare',
    component: () => import('../pages/draw/communityShare.vue'),
    meta: { title: '分享' }
  },
  {
    path: '/pages/draw/drawResult/drawResult',
    name: 'drawResult',
    component: () => import('../pages/draw/drawResult.vue'),
    meta: { title: '中签结果' }
  },
  {
    path: '/pages/draw/InvitationFriendAssist/InvitationFriendAssist',
    name: 'drawInvitationFriendAssist',
    component: () => import('../pages/draw/InvitationFriendAssist.vue'),
    meta: { title: '邀请好友助力' }
  },
  {
    path: '/pages/draw/Leaderboard/Leaderboard',
    name: 'drawLeaderboard',
    component: () => import('../pages/draw/Leaderboard.vue'),
    meta: { title: '参与人数' }
  },
  {
    path: '/pages/draw/shareCommunityGroup/shareCommunityGroup',
    name: 'drawShareCommunityGroup',
    component: () => import('../pages/draw/shareCommunityGroup.vue'),
    meta: { title: '分享' }
  },
  {
    path: '/pages/activity/draw',
    name: 'activityDraw',
    component: () => import('../pages/activity/draw.vue'),
    meta: { title: '抽签' }
  },
  {
    path: '/pages/activity/record',
    name: 'activityRecord',
    component: () => import('../pages/activity/record.vue'),
    meta: { title: '中签记录' }
  },
  {
    path: '/pages/activeSelected/activeSelected',
    name: 'activeSelected',
    component: () => import('../pages/activity/activeSelected.vue'),
    meta: { title: '活动中心' }
  },
  {
    path: '/pages/certification/certification',
    name: 'certification',
    component: () => import('../pages/certification/certification.vue'),
    meta: { title: '实名认证' }
  },
  {
    path: '/pages/certification/certificationStatus',
    name: 'certificationStatus',
    component: () => import('../pages/certification/certificationStatus.vue'),
    meta: { title: '认证结果' }
  },
  {
    path: '/pages/kline/buy',
    name: 'klineBuy',
    component: () => import('../pages/kline/buy.vue'),
    meta: { title: '购买' }
  },
  {
    path: '/pages/kline/info',
    name: 'klineInfo',
    component: () => import('../pages/kline/info.vue'),
    meta: { title: '详情' }
  },
  // ===== 批次3 我的域 14 页（P009-P019/P021/P022/P024）=====
  { path: '/pages/my/about', name: 'about', component: () => import('../pages/my/about.vue'), meta: { title: '关于我们' } },
  { path: '/pages/my/actionPwd/getCode', name: 'actionPwdGetCode', component: () => import('../pages/my/getCode.vue'), meta: { title: '修改操作密码' } },
  { path: '/pages/my/actionPwd/setPwd', name: 'actionPwdSetPwd', component: () => import('../pages/my/setPwd.vue'), meta: { title: '设置操作密码' } },
  { path: '/pages/my/address/edit', name: 'addressEdit', component: () => import('../pages/my/addressEdit.vue'), meta: { title: '添加收货地址' } },
  { path: '/pages/my/address/list', name: 'addressList', component: () => import('../pages/my/addressList.vue'), meta: { title: '收货地址' } },
  { path: '/pages/my/cancellation', name: 'cancellation', component: () => import('../pages/my/cancellation.vue'), meta: { title: '注销账号申请' } },
  { path: '/pages/my/depositinquiry', name: 'depositinquiry', component: () => import('../pages/my/depositinquiry.vue'), meta: { title: '存证查询' } },
  { path: '/pages/my/enablingcenter', name: 'enablingcenter', component: () => import('../pages/my/enablingcenter.vue'), meta: { title: '赋能中心' } },
  { path: '/pages/my/flowDoc', name: 'flowDoc', component: () => import('../pages/my/flowDoc.vue'), meta: { title: '流转记录' } },
  { path: '/pages/my/info', name: 'userInfo', component: () => import('../pages/my/info.vue'), meta: { title: '个人信息' } },
  { path: '/pages/my/join', name: 'join', component: () => import('../pages/my/join.vue'), meta: { title: '加入社群' } },
  { path: '/pages/my/privacy', name: 'privacySetting', component: () => import('../pages/my/privacy.vue'), meta: { title: '隐私设置' } },
  { path: '/pages/my/service', name: 'service', component: () => import('../pages/my/service.vue'), meta: { title: '客服' } },
  { path: '/pages/my/vip', name: 'vip', component: () => import('../pages/my/vip.vue'), meta: { title: 'VIP权益中心' } },

  // 批次4 市场交易（11 页）—— P027/P032/P033 无参数直访 fallback 到首页，复用 index.vue
  { path: '/pages/market/attentionList', name: 'attentionList', component: () => import('../pages/market/attentionList.vue'), meta: { title: '关注' } },
  { path: '/pages/market/categoryManager', name: 'categoryManager', component: () => import('../pages/market/categoryManager.vue'), meta: { title: '分类' } },
  { path: '/pages/market/detail', name: 'marketDetail', component: () => import('../pages/index/index.vue'), meta: { title: '火兔', isTab: true } },
  { path: '/pages/market/giftRecord', name: 'giftRecord', component: () => import('../pages/market/giftRecord.vue'), meta: { title: '赠送记录' } },
  { path: '/pages/market/marketGoodsList', name: 'marketGoodsList', component: () => import('../pages/market/marketGoodsList.vue'), meta: { title: '商品详情' } },
  { path: '/pages/market/search', name: 'marketSearch', component: () => import('../pages/market/search.vue'), meta: { title: '搜索' } },
  { path: '/pages/market/sellBegBuy', name: 'sellBegBuy', component: () => import('../pages/index/index.vue'), meta: { title: '火兔', isTab: true } },
  { path: '/pages/market/sellInfo', name: 'sellInfo', component: () => import('../pages/index/index.vue'), meta: { title: '火兔', isTab: true } },
  { path: '/pages/market/submitBegBuy', name: 'submitBegBuy', component: () => import('../pages/market/submitBegBuy.vue'), meta: { title: '发起求购' } },
  { path: '/pages/market/submitEntrust', name: 'submitEntrust', component: () => import('../pages/market/submitEntrust.vue'), meta: { title: '委托购买' } },
  { path: '/pages/market/tradeUnion', name: 'tradeUnion', component: () => import('../pages/market/tradeUnion.vue'), meta: { title: 'GM工会' } },

  // 批次25-29 收官（7 页）—— 均为无参数直访真实态快照，零新增 API
  // 批次25 排行榜
  { path: '/pages/leaderboard/leaderboard', name: 'leaderboard', component: () => import('../pages/leaderboard/leaderboard.vue'), meta: { title: '排行榜' } },
  // 批次26 市值排行
  { path: '/pages/marketCapRanking/marketCapRanking', name: 'marketCapRanking', component: () => import('../pages/marketCapRanking/marketCapRanking.vue'), meta: { title: '市值排行' } },
  // 批次27 规则
  { path: '/pages/rules/rules', name: 'rulesPage', component: () => import('../pages/rules/rules.vue'), meta: { title: '活动规则' } },
  // 批次28 启动/下载
  { path: '/pages/index/downloadApp', name: 'downloadApp', component: () => import('../pages/downloadApp/downloadApp.vue'), meta: { title: '下载 APP' } },
  { path: '/pages/index/start', name: 'startPage', component: () => import('../pages/index/start.vue'), meta: { title: '启动页' } },
  // 批次29 内嵌页
  { path: '/pages/webview/webview', name: 'webview', component: () => import('../pages/webview/webview.vue'), meta: { title: '' } },
  { path: '/pages/webview/webview-storage', name: 'webviewStorage', component: () => import('../pages/webview/webviewStorage.vue'), meta: { title: '' } }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 })
})

router.afterEach((to) => {
  document.title = to.meta.title || '火兔'
})

export default router
