<template>
  <div class="mine-page">
    <!-- 顶部横幅：h180，2026 FIRE BUNNY 背景 -->
    <div class="mine-banner">
      <img class="settings-ic" :src="mineSettings" alt="" @click="go('/pages/my/setting')" />
    </div>

    <!-- 用户信息区 -->
    <div class="user-zone">
      <img class="avatar" :src="avatarSrc" alt="" />
      <div class="nick-row">
        <span class="nickname">{{ userinfo?.nickname || '未登录' }}</span>
        <img v-if="verified" class="verified" :src="mineVerified" alt="已实名" />
      </div>
      <div class="stat-row">
        <div class="stat-item">
          <span class="num">{{ stats.invited_count }}</span><span class="lab">邀请</span>
        </div>
        <div class="stat-item">
          <span class="num">{{ stats.buy_count }}</span><span class="lab">购入</span>
        </div>
        <div class="stat-item">
          <span class="num">{{ stats.sale_count }}</span><span class="lab">出售</span>
        </div>
        <div class="stat-item">
          <span class="num">{{ stats.join_day }}</span><span class="lab">加入天数</span>
        </div>
      </div>
      <div class="cell-row">
        <span class="cell-text">Cell: {{ maskMobile }}</span>
        <img class="checkin" :src="mineCheckin" alt="打卡活动" @click="go('/pages/sign/index')" />
      </div>
      <div class="btn-row">
        <div class="m-btn" @click="go('/pages/my/setting')">
          <img class="m-btn-ic" :src="mineEdit" alt="" />
          <span>编辑个人信息</span>
        </div>
        <div class="m-btn" @click="showPoster = true">
          <img class="m-btn-ic" :src="mineInvite" alt="" />
          <span>邀请海报</span>
        </div>
        <div class="m-btn" @click="go('/pages/lottery/lottery?id=2')">
          <img class="m-btn-ic" :src="mineLottery" alt="" />
          <span>抽奖活动</span>
        </div>
      </div>
    </div>

    <!-- 订单 / 钱包 双卡片 -->
    <div class="card-row">
      <div class="half-card" @click="go('/pages/order/list')">
        <div class="card-head">
          <img class="card-ic" :src="mineOrders" alt="" />
          <span class="card-title">我的订单</span>
        </div>
        <div class="card-sub">
          查看所有历史交易
          <img class="card-arrow" :src="iconRight" alt="" />
        </div>
      </div>
      <div class="half-card" @click="openWallet()">
        <div class="card-head">
          <img class="card-ic" :src="mineWallet" alt="" />
          <span class="card-title">我的钱包</span>
        </div>
        <div class="card-sub">
          开通第三方支付
          <img class="card-arrow" :src="iconRight" alt="" />
        </div>
      </div>
    </div>

    <!-- 我的资产卡 -->
    <div class="asset-card">
      <div class="asset-head" @click="go('/pages/myCollection/myCollection')">
        <span class="asset-title">我的资产</span>
        <img class="asset-arrow" :src="iconRight" alt="" />
      </div>
      <div class="asset-body">
        <div class="asset-item" @click="go('/pages/myCollection/myCollection')">
          <img class="asset-ic" :src="mineCollection" alt="" />
          <span class="asset-lab">藏品</span>
          <span class="asset-num">{{ stats.collection_count }}</span>
        </div>
        <div class="asset-divider"></div>
        <div class="asset-item" @click="go('/pages/myCollection/myCollection')">
          <img class="asset-ic" :src="mineBlindbox" alt="" />
          <span class="asset-lab">盲盒</span>
          <span class="asset-num">{{ stats.blindbox_count }}</span>
        </div>
      </div>
    </div>

    <!-- 寄售中 / 关注 -->
    <div class="seg-row">
      <div class="seg-pill" :class="{ off: seg !== 'sell' }" @click="seg = 'sell'">
        寄售中 {{ consTotal }}
      </div>
      <div class="seg-pill" :class="{ off: seg !== 'follow' }" @click="seg = 'follow'">
        关注 {{ followTotal }}
      </div>
    </div>

    <!-- 关注 Tab：市场列表表头 -->
    <div v-if="seg === 'follow'" class="follow-head">
      <div class="fh-cell">名称<span class="sort"></span></div>
      <div class="fh-cell">成交量<span class="sort"></span></div>
      <div class="fh-spacer"></div>
      <div class="fh-cell">地板价<span class="sort"></span></div>
      <div class="fh-cell">涨跌幅<span class="sort"></span></div>
    </div>

    <!-- 空态（寄售中 Tab） -->
    <div v-else class="empty-wrap">
      <img class="empty-img" :src="emptyData" alt="" />
    </div>

    <TabBar />

    <!-- 「邀请海报」原站为页内浮层（不跳转），批次3 占位补做 -->
    <InvitePoster v-if="showPoster" @close="showPoster = false" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { useUserStore } from '../../stores/user'
import TabBar from '../../components/TabBar.vue'
import InvitePoster from '../../components/InvitePoster.vue'

/* 静态资源（均已从原站下载进项目） */
const mineSettings = new URL('../../assets/images/mine-settings.png', import.meta.url).href
const mineVerified = new URL('../../assets/images/mine-verified.png', import.meta.url).href
const mineCheckin = new URL('../../assets/images/mine-checkin-activity.png', import.meta.url).href
const mineEdit = new URL('../../assets/images/mine-edit.png', import.meta.url).href
const mineInvite = new URL('../../assets/images/mine-invite.png', import.meta.url).href
const mineLottery = new URL('../../assets/images/mine-lottery-activity.png', import.meta.url).href
const mineOrders = new URL('../../assets/images/mine-orders.png', import.meta.url).href
const mineWallet = new URL('../../assets/images/mine-wallet.png', import.meta.url).href
const mineCollection = new URL('../../assets/images/mine-collection.png', import.meta.url).href
const mineBlindbox = new URL('../../assets/images/mine-blindbox.png', import.meta.url).href
const iconRight = new URL('../../assets/images/icon-right.png', import.meta.url).href
const emptyData = new URL('../../assets/images/empty-data.png', import.meta.url).href
const defaultAvatar = new URL('../../assets/images/mine-default-avatar.png', import.meta.url).href

const API_BASE = import.meta.env.VITE_API_BASE || ''
function imgUrl(p) {
  if (!p) return ''
  return /^https?:/.test(p) ? p : API_BASE + p
}

const user = useUserStore()
const router = useRouter()
const userinfo = ref(user.userinfo)
const stats = ref({ invited_count: 0, buy_count: 0, sale_count: 0, join_day: 0, collection_count: 0, blindbox_count: 0 })
const consTotal = ref(0)
const followTotal = ref(0)
const seg = ref('sell')
const showPoster = ref(false)

const verified = computed(() => (userinfo.value?.real_name_authentication || 0) === 1)
const maskMobile = computed(() => {
  const m = userinfo.value?.mobile || ''
  return m.length === 11 ? m.slice(0, 3) + '****' + m.slice(7) : m
})
/* 原站行为：默认头像固定显示白色圆角兔标 */
const avatarSrc = computed(() => {
  const a = userinfo.value?.avatar
  if (!a || a.includes('defautAvatar')) return defaultAvatar
  return imgUrl(a)
})

/* 批次3 占位清理（用户「把批次三的占位做掉」授权）：
   9 个 todo() 占位按原站实测真实跳转目标全部接为路由，仅「邀请海报」为页内浮层。
   目标：设置图标/编辑个人信息 -> my/setting；打卡 -> sign/index；
   抽奖 -> lottery/lottery?id=2；我的订单 -> order/list；
   我的资产/藏品/盲盒 -> myCollection/myCollection。 */
function go(path) {
  router.push(path)
}
/* 原站实测：「我的钱包」入口跳转 /pages/wallet/wallet（批次14 交付，用户授权联动） */
function openWallet() {
  router.push('/pages/wallet/wallet')
}

onMounted(async () => {
  try {
    const r = await http.post('/api/user/getUserInfo', {})
    if (r.code === 1 && r.data) {
      userinfo.value = r.data
      if (r.data.statistics) stats.value = r.data.statistics
    }
  } catch (e) { /* 401 由拦截器跳登录 */ }

  /* 寄售中 / 关注 两个 Tab 的计数 */
  try {
    const c = await http.post('/api/order/order/myConsignment', { status: 1, types: 2, goods_type: 2, page: 1 })
    if (c.code === 1 && c.data) consTotal.value = c.data.total || 0
  } catch (e) { /* 静默 */ }
  try {
    const f = await http.post('/api/market/market/getMarketList', { goods_type: 2, page: 1, list_rows: 10, series_id: -2 })
    if (f.code === 1) followTotal.value = (f.data && f.data.total) || (Array.isArray(f.data) ? f.data.length : 0)
  } catch (e) { /* 静默 */ }
})
</script>

<style scoped>
.mine-page {
  min-height: 100vh;
  background: #f4f5f5;
  padding-bottom: 76px;
}

/* ===== 顶部横幅：375x180 ===== */
.mine-banner {
  position: relative;
  height: 180px;
  background: url('../../assets/images/mine-bg.png') no-repeat center top / 100% 180px;
}
.settings-ic {
  position: absolute;
  right: 22px;
  top: 120px;
  width: 20px;
  height: 20px;
  cursor: pointer;
}

/* ===== 用户信息区（头像上叠横幅 40px） ===== */
.user-zone {
  position: relative;
  padding: 0 20px;
}
.avatar {
  display: block;
  width: 72px;
  height: 72px;
  border-radius: 16px;
  margin: -40px 0 0 4px; /* 头像 x24 = 容器 padding20 + 4 */
}
.nick-row {
  margin-top: 13px;
  display: flex;
  align-items: center;
}
.nickname {
  font-size: 20px;
  line-height: 27px;
  font-weight: 700;
  color: #212121;
  letter-spacing: -1px; /* 原站 CKTKingKong-Bold 字宽略窄于 PuHuiTi Bold，收紧字距对齐 */
}
.verified {
  width: 74px;
  height: 26px;
  margin-left: 6px;
}
.stat-row {
  margin-top: 10px;
  display: flex;
  align-items: baseline;
  gap: 14px;
}
.stat-item {
  display: flex;
  align-items: baseline;
  gap: 6px;
}
.stat-item .num {
  font-family: var(--ht-font-num);
  font-size: 20px;
  line-height: 22px;
  color: #212121;
}
.stat-item .lab {
  font-size: 12px;
  line-height: 16px;
  color: #787f8d;
}
.cell-row {
  position: relative;
  margin-top: 20px;
  height: 20px;
  display: flex;
  align-items: center;
}
.cell-text {
  font-size: 14px;
  line-height: 20px;
  color: #787f8d;
}
.checkin {
  position: absolute;
  right: 0;
  top: -10px;
  width: 43px;
  height: 40px;
  cursor: pointer;
}
.btn-row {
  margin-top: 26px;
  display: flex;
  gap: 8px;
}
.m-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  height: 32px;
  padding: 5px 6px;
  background: #fff;
  border: 1px solid #e8ebf0;
  border-radius: 6px;
  cursor: pointer;
}
.m-btn-ic {
  width: 20px;
  height: 20px;
}
.m-btn span {
  font-size: 11px;
  line-height: 16px;
  color: #000;
  white-space: nowrap;
}

/* ===== 订单 / 钱包 双卡片 ===== */
.card-row {
  margin-top: 16px;
  padding: 0 20px;
  display: flex;
  gap: 8px;
}
.half-card {
  flex: 1;
  height: 88px;
  background: #fff;
  border-radius: 12px;
  padding: 14px 12px;
  cursor: pointer;
}
.card-head {
  display: flex;
  align-items: center;
  gap: 6px;
}
.card-ic {
  width: 30px;
  height: 30px;
}
.card-title {
  font-size: 14px;
  line-height: 20px;
  font-weight: 700;
  color: #212121;
}
.card-sub {
  margin-top: 9px;
  display: flex;
  align-items: center;
  font-size: 12px;
  line-height: 17px;
  color: #787f8d;
}
.card-arrow {
  width: 10px;
  height: 10px;
  margin-left: 2px;
}

/* ===== 我的资产卡 ===== */
.asset-card {
  margin: 16px 20px 0;
  height: 105px;
  background: linear-gradient(225deg, #fdecee 0%, #ffffff 55%);
  border-radius: 12px;
  padding: 14px 12px;
}
.asset-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(33, 33, 33, 0.06);
  cursor: pointer;
}
.asset-title {
  font-size: 14px;
  line-height: 20px;
  font-weight: 500;
  color: #212121;
}
.asset-arrow {
  width: 10px;
  height: 10px;
}
.asset-body {
  margin-top: 16px;
  display: flex;
  align-items: center;
}
.asset-item {
  flex: 1;
  display: flex;
  align-items: center;
  cursor: pointer;
}
.asset-ic {
  width: 20px;
  height: 20px;
}
.asset-lab {
  margin-left: 8px;
  font-size: 12px;
  line-height: 17px;
  color: #787f8d;
}
.asset-num {
  margin-left: 8px;
  font-family: var(--ht-font-num);
  font-size: 20px;
  line-height: 24px;
  color: #212121;
}
.asset-divider {
  width: 1px;
  height: 24px;
  background: rgba(33, 33, 33, 0.08);
}

/* ===== 寄售中 / 关注 ===== */
.seg-row {
  margin-top: 16px;
  padding: 0 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.seg-pill {
  height: 28px;
  padding: 0 12px;
  display: flex;
  align-items: center;
  border: 1px solid #c5cad3;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  color: #212121;
  background: transparent;
  cursor: pointer;
}
.seg-pill.off {
  border-color: transparent;
  font-weight: 400;
  color: #9aa0ab;
}

/* ===== 关注 Tab 表头 ===== */
.follow-head {
  margin-top: 16px;
  padding: 0 20px;
  display: flex;
  align-items: center;
}
.fh-cell {
  display: flex;
  align-items: center;
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
  white-space: nowrap;
}
.fh-cell + .fh-cell {
  margin-left: 30px;
}
.fh-spacer {
  flex: 1;
}
.sort {
  display: inline-block;
  width: 0;
  height: 0;
  margin-left: 4px;
  border-left: 3px solid transparent;
  border-right: 3px solid transparent;
  border-bottom: 4px solid #c5cad3;
  position: relative;
  top: -3px;
}
.sort::after {
  content: '';
  position: absolute;
  left: -3px;
  top: 5px;
  width: 0;
  height: 0;
  border-left: 3px solid transparent;
  border-right: 3px solid transparent;
  border-top: 4px solid #c5cad3;
}

/* ===== 空态 ===== */
.empty-wrap {
  margin-top: 65px;
  display: flex;
  justify-content: center;
}
.empty-img {
  width: 142px;
  height: 142px;
}
</style>
