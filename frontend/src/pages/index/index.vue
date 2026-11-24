<template>
  <div class="home-page">
    <!-- 轮播图：y0 h244 顶部圆角11 -->
    <div class="banner-wrap">
      <div class="swiper">
        <div
          class="swiper-track"
          :style="{ transform: `translateX(-${current * 375}px)` }"
        >
          <div v-for="b in banners" :key="b.id" class="swiper-slide">
            <img :src="imgUrl(b.image)" class="banner-img" alt="" />
          </div>
        </div>
      </div>
      <img class="banner-corner" :src="bannerCorner" alt="" />
      <div class="swiper-dots">
        <span
          v-for="(b, i) in banners"
          :key="i"
          class="dot"
          :class="{ on: i === current }"
        ></span>
      </div>
    </div>

    <!-- 公告栏：x20 y228 w335 h40 r12 悬浮于轮播下缘 -->
    <div class="notice-bar">
      <img class="notice-ic" :src="noticeIc" alt="" />
      <div class="notice-viewport">
        <div
          class="notice-track"
          :style="{ transform: `translateY(-${noticeIdx * 26}px)` }"
        >
          <div v-for="n in notices" :key="n.id" class="notice-line">{{ n.title }}</div>
        </div>
      </div>
      <img class="notice-go" :src="noticeGo" alt="" />
    </div>

    <!-- 活动入口卡片：y284 两张 160x184 r16 -->
    <div class="activity-row">
      <div class="act-card" @click="todo('邀请好友')">
        <div class="act-head">
          <div class="act-titles">
            <div class="act-title">邀请好友</div>
            <div class="act-sub">一起加入</div>
          </div>
          <img class="act-arrow" :src="homeEnter" alt="" />
          <div class="act-en">Join<br />together</div>
        </div>
        <img class="act-img" :src="inviteImg" alt="" />
      </div>
      <div class="act-card" @click="todo('合成活动')">
        <div class="act-head">
          <div class="act-titles">
            <div class="act-title">合成活动</div>
            <div class="act-sub">新图惊喜</div>
          </div>
          <img class="act-arrow" :src="homeEnter" alt="" />
          <div class="act-en">Official<br />Gift</div>
        </div>
        <img class="act-img" :src="hcImg" alt="" />
      </div>
    </div>

    <!-- 标签页：推荐/热榜/排行 -->
    <div class="seg-row">
      <div
        v-for="t in segs"
        :key="t"
        class="seg"
        :class="{ on: seg === t }"
        @click="switchSeg(t)"
      >{{ t }}</div>
    </div>

    <!-- ===== 推荐 ===== -->
    <template v-if="seg === '推荐'">
      <div class="filter-row">
        <div
          class="pill"
          :class="{ on: saleFilter === 1 }"
          @click="saleFilter = 1"
        >即将开售</div>
        <div
          class="pill"
          :class="{ on: saleFilter === 2 }"
          @click="saleFilter = 2"
        >已开售</div>
        <div class="type-wrap">
          <div class="type-btn" @click="typeOpen = !typeOpen">
            {{ typeName }} <span class="caret"></span>
          </div>
          <div v-if="typeOpen" class="type-panel">
            <div
              v-for="s in series"
              :key="s.series_id"
              class="type-item"
              :class="{ on: s.series_id === seriesId }"
              @click="pickSeries(s)"
            >{{ s.series_name }}</div>
          </div>
        </div>
      </div>

      <div class="goods-list">
        <template v-for="g in filteredGoods" :key="g.id">
          <div class="goods-card" @click="goRecommendDetail(g)">
            <div class="goods-main">
              <img class="goods-img" :src="imgUrl(g.listimg)" alt="" />
              <div class="goods-info">
                <div class="goods-name">{{ g.name }}</div>
                <div class="goods-meta">
                  <img class="meta-ic" :src="sharesIc" alt="" />
                  <span>{{ g.total_num }}份</span>
                </div>
                <div class="goods-meta">
                  <img class="meta-ic" :src="dateIc" alt="" />
                  <span>{{ g.release_date_time_text }}</span>
                </div>
              </div>
              <div class="goods-price">
                <span class="p-cur">¥</span><span class="p-num">{{ g.price }}</span><span class="p-qi">起</span>
              </div>
            </div>
          </div>
          <!-- 开售倒计时条：白卡下方独立条（原站 y702 h40） -->
          <div v-if="countdownOf(g)" class="sale-bar">
            <img class="sale-ic" :src="todayIc" alt="" />
            <span class="sale-text">{{ saleLabel(g) }} {{ g.release_date_time_text.slice(11) }} 开售</span>
            <span class="sale-cd">{{ countdownOf(g) }}</span>
          </div>
        </template>
        <div v-if="!filteredGoods.length" class="empty-tip">暂无数据</div>
      </div>
    </template>

    <!-- ===== 热榜 ===== -->
    <template v-if="seg === '热榜'">
      <div class="rank-list">
        <div v-for="(r, i) in hotList" :key="r.id" class="hot-card">
          <img v-if="i === 0" class="hot-medal" :src="rankTop1" alt="" />
          <span v-else class="hot-no">{{ i + 1 }}</span>
          <img class="hot-img" :src="imgUrl(r.listimg)" alt="" />
          <div class="hot-info">
            <div class="hot-name">{{ r.name }}</div>
            <div class="hot-line">发行 {{ r.total_num }} | 流通 {{ r.flux }}</div>
            <div class="hot-vol">成交量：{{ formatNum(r.hotnum) }}</div>
          </div>
          <div class="hot-right">
            <img
              class="follow-btn"
              :src="r.is_like ? followedIc : unfollowIc"
              alt=""
              @click="todo('关注')"
            />
            <div class="hot-price">
              <span class="hp-cur">¥</span><span class="hp-num">{{ r.min_price.toFixed(2) }}</span>
            </div>
          </div>
        </div>
        <div v-if="!hotList.length" class="empty-tip">暂无数据</div>
      </div>
    </template>

    <!-- ===== 排行（名人堂） ===== -->
    <template v-if="seg === '排行'">
      <div class="rank1-wrap">
        <div class="my-rank-card">
          <div class="mrc-top">
            <span class="mrc-name">{{ nickname }}</span>
            <span class="mrc-rule"><img class="rule-ic" :src="rankDateIc" alt="" />每日晚24时结算</span>
          </div>
          <div class="mrc-body">
            <img class="mrc-avatar" :src="avatarUrl" alt="" />
            <div class="mrc-val">
              <div class="mrc-label">当前购入值</div>
              <div class="mrc-num">{{ myRankValue }}</div>
            </div>
            <div class="mrc-order"><span>第</span><b>{{ myRankNo }}</b><span>名</span></div>
          </div>
        </div>

        <div class="podium">
          <div class="pod pod2">
            <img class="pod-badge" :src="pod2" alt="" />
            <img class="pod-avatar sm" :src="podAvatar" alt="" />
            <div class="pod-name">-</div>
            <div class="pod-label">购入值</div>
            <div class="pod-val">-</div>
          </div>
          <div class="pod pod1">
            <img class="pod-badge" :src="pod1" alt="" />
            <img class="pod-avatar" :src="podAvatar" alt="" />
            <div class="pod-name">-</div>
            <div class="pod-label">购入值</div>
            <div class="pod-val">-</div>
          </div>
          <div class="pod pod3">
            <img class="pod-badge" :src="pod3" alt="" />
            <img class="pod-avatar sm" :src="podAvatar" alt="" />
            <div class="pod-name">-</div>
            <div class="pod-label">购入值</div>
            <div class="pod-val">-</div>
          </div>
        </div>

        <div class="hall-empty">
          <img class="empty-img" :src="emptyData" alt="" />
          <div class="empty-text">名人堂未开启</div>
        </div>
      </div>
    </template>

    <div class="bottom-space"></div>
    <TabBar />
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { useUserStore } from '../../stores/user'
import TabBar from '../../components/TabBar.vue'
const router = useRouter()

/* 静态资源（均已从原站下载进项目） */
const bannerCorner = new URL('../../assets/images/banner_right.png', import.meta.url).href
const noticeIc = new URL('../../assets/images/home-notice.png', import.meta.url).href
const noticeGo = new URL('../../assets/images/home-notice-go.png', import.meta.url).href
const homeEnter = new URL('../../assets/images/home-enter.png', import.meta.url).href
const inviteImg = new URL('../../assets/images/home-invite-friends.png', import.meta.url).href
const hcImg = new URL('../../assets/images/home-invite-hc.png', import.meta.url).href
const sharesIc = new URL('../../assets/images/home-shares.png', import.meta.url).href
const dateIc = new URL('../../assets/images/home-date.png', import.meta.url).href
const todayIc = new URL('../../assets/images/home-today.png', import.meta.url).href
const rankTop1 = new URL('../../assets/images/home-rank-top1.png', import.meta.url).href
const followedIc = new URL('../../assets/images/home-followed.png', import.meta.url).href
const unfollowIc = new URL('../../assets/images/home-unfollow.png', import.meta.url).href
const rankDateIc = new URL('../../assets/images/home-rank-date.png', import.meta.url).href
const pod1 = new URL('../../assets/images/home-rank-user-top1.png', import.meta.url).href
const pod2 = new URL('../../assets/images/home-rank-user-top2.png', import.meta.url).href
const pod3 = new URL('../../assets/images/home-rank-user-top3.png', import.meta.url).href
const podAvatar = new URL('../../assets/images/logo.png', import.meta.url).href
const emptyData = new URL('../../assets/images/empty-data.png', import.meta.url).href

/* 图片走自建后端 /uploads */
const API_BASE = import.meta.env.VITE_API_BASE || ''
function imgUrl(p) {
  if (!p) return ''
  return /^https?:/.test(p) ? p : API_BASE + p
}

const user = useUserStore()
const nickname = computed(() => user.userinfo?.nickname || '未登录')
const avatarUrl = computed(() => {
  const a = user.userinfo?.avatar
  return a ? imgUrl(a) : podAvatar
})

/* 数据 */
const banners = ref([])
const notices = ref([])
const series = ref([])
const goods = ref([])
const hotList = ref([])

const current = ref(0)
const noticeIdx = ref(0)
const seg = ref('推荐')
const segs = ['推荐', '热榜', '排行']
const saleFilter = ref(1)
const typeOpen = ref(false)
const seriesId = ref(-1)
/* 未选择时显示"类型"（与原站默认一致），选择后显示所选系列名 */
const typeName = computed(() => {
  if (!typePicked.value) return '类型'
  return series.value.find(s => s.series_id === seriesId.value)?.series_name || '类型'
})
const typePicked = ref(false)
const myRankValue = ref('-')
const myRankNo = ref('-')

/* 原站行为：筛选胶囊为前端本地过滤 */
const filteredGoods = computed(() =>
  goods.value.filter(g => (saleFilter.value === 1 ? 1 : 2) === (g.sale_status || 1))
)

const timers = []
onMounted(async () => {
  try {
    const [b, a, s, g] = await Promise.all([
      http.post('/api/banner/show/getBanner', {}),
      http.post('/api/index/afficheList', {}),
      http.post('/api/index/getSeries', { type: 10 }),
      http.post('/api/box/blind_box/list', { list_rows: 10, page: 1 })
    ])
    banners.value = b.data?.data || []
    notices.value = a.data?.data || []
    series.value = s.data || []
    goods.value = (g.data || []).map(x => ({ ...x, sale_status: x.sale_status || 1 }))
  } catch (e) { /* 静默 */ }

  try {
    const r = await http.post('/api/market/market/marketrebang', {})
    if (r.code === 1) hotList.value = r.data || []
  } catch (e) { /* 静默 */ }

  try {
    const rk = await http.post('/api/user/newrank1', {})
    if (rk.code === 0) { myRankValue.value = '-'; myRankNo.value = '-' }
  } catch (e) { /* 静默 */ }

  /* 轮播 5s 自动切换（与原站一致） */
  timers.push(setInterval(() => {
    if (banners.value.length) current.value = (current.value + 1) % banners.value.length
  }, 5000))
  /* 公告 3s 纵向轮播 */
  timers.push(setInterval(() => {
    if (notices.value.length) noticeIdx.value = (noticeIdx.value + 1) % notices.value.length
  }, 3000))
  /* 倒计时每秒 */
  timers.push(setInterval(() => { nowTs.value = Math.floor(Date.now() / 1000) }, 1000))
})
onUnmounted(() => timers.forEach(clearInterval))

const nowTs = ref(Math.floor(Date.now() / 1000))

function pad(n) { return String(n).padStart(2, '0') }

/* 开售倒计时：未开售返回 HH:MM:SS，已开售返回空（原站开售后隐藏倒计时条） */
function countdownOf(g) {
  const diff = g.release_time - nowTs.value
  if (diff <= 0) return ''
  const h = Math.floor(diff / 3600)
  const m = Math.floor((diff % 3600) / 60)
  const s = diff % 60
  return `${pad(h)} : ${pad(m)} : ${pad(s)}`
}

/* 开售日期前缀：今天/明天/MM.DD */
function saleLabel(g) {
  const rt = g.release_time * 1000
  const d = new Date(rt)
  const today = new Date(); today.setHours(0, 0, 0, 0)
  const that = new Date(rt); that.setHours(0, 0, 0, 0)
  const days = Math.round((that - today) / 86400000)
  if (days === 0) return '今天'
  if (days === 1) return '明天'
  return `${pad(d.getMonth() + 1)}.${pad(d.getDate())}`
}

function formatNum(n) {
  return Number(n || 0).toLocaleString('en-US')
}

function switchSeg(t) {
  seg.value = t
}

function pickSeries(s) {
  seriesId.value = s.series_id
  typePicked.value = true
  typeOpen.value = false
}

/* 发售日历卡片点击：对齐原站首页 chunk 的 goRecommendDetail(a)
   goods_type==1 → 盲盒详情；否则 → 藏品详情（带 batch_id） */
function goRecommendDetail(g) {
  if (String(g.goods_type) === '1') {
    router.push({ path: '/pages/product/box', query: { id: g.id } })
  } else {
    router.push({ path: '/pages/product/collection', query: { id: g.id, batch_id: g.batch_id } })
  }
}

let toastTimer = null
function showToast(msg) {
  let el = document.querySelector('.ht-toast')
  if (!el) {
    el = document.createElement('div')
    el.className = 'ht-toast'
    document.body.appendChild(el)
  }
  el.textContent = msg
  el.style.display = 'block'
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (el.style.display = 'none'), 2000)
}

function todo(name) {
  showToast(`${name}页面将在后续批次交付`)
}
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  background: #f4f5f5;
  padding-bottom: 76px;
}

/* ===== 轮播 ===== */
.banner-wrap {
  position: relative;
  height: 244px;
}
.swiper {
  width: 100%;
  height: 244px;
  overflow: hidden;
  border-radius: 11px 11px 0 0;
}
.swiper-track {
  display: flex;
  height: 100%;
  transition: transform 0.5s ease;
}
.swiper-slide {
  width: 375px;
  flex-shrink: 0;
  height: 244px;
}
.banner-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.banner-corner {
  position: absolute;
  right: 0;
  top: 163px;
  width: 92px;
  height: 69px;
  pointer-events: none;
}
.swiper-dots {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 10px;
  display: flex;
  justify-content: center;
  gap: 4px;
}
.dot {
  width: 6px;
  height: 6px;
  border-radius: 3px;
  background: rgba(255, 255, 255, 0.9);
}
.dot.on {
  width: 15px;
  background: url('../../assets/images/home-indicator.png') no-repeat center / 15px 6px;
}

/* ===== 公告 ===== */
.notice-bar {
  position: relative;
  margin: -16px 20px 0;
  height: 40px;
  background: #f0f3f6;
  border-radius: 12px;
  display: flex;
  align-items: center;
  z-index: 2;
}
.notice-ic {
  width: 28px;
  height: 26px;
  margin-left: 14px;
  flex-shrink: 0;
}
.notice-viewport {
  width: 223px;
  height: 26px;
  overflow: hidden;
  margin-left: 6px;
}
.notice-track {
  transition: transform 0.4s ease;
}
.notice-line {
  height: 26px;
  line-height: 26px;
  font-size: 12px;
  color: #3c455a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.notice-go {
  width: 50px;
  height: 24px;
  margin-left: auto;
  margin-right: 10px;
  flex-shrink: 0;
  cursor: pointer;
}

/* ===== 活动卡片 ===== */
.activity-row {
  display: flex;
  justify-content: space-between;
  margin: 16px 20px 0;
}
.act-card {
  width: 160px;
  height: 184px;
  background: #fff;
  border-radius: 16px;
  padding: 12px 10px;
  box-sizing: border-box;
  cursor: pointer;
}
.act-head {
  position: relative;
  height: 56px;
}
.act-title {
  font-size: 16px;
  font-weight: 700;
  color: #212121;
  line-height: 22px;
}
.act-sub {
  margin-top: 4px;
  font-size: 14px;
  color: #787f8d;
  line-height: 16px;
}
.act-arrow {
  position: absolute;
  right: 0;
  top: 0;
  width: 33px;
  height: 24px;
}
.act-en {
  position: absolute;
  right: 0;
  bottom: 0;
  font-size: 12px;
  color: #787f8d;
  text-align: right;
  line-height: 14px;
}
.act-img {
  margin-top: 8px;
  width: 140px;
  height: 100px;
  border-radius: 12px;
  display: block;
}

/* ===== 标签页 ===== */
.seg-row {
  display: flex;
  align-items: flex-end;
  gap: 24px;
  margin: 16px 20px 0;
}
.seg {
  font-size: 15px;
  line-height: 20px;
  color: #4a4a4a;
  padding-bottom: 8px;
  position: relative;
  cursor: pointer;
}
.seg.on {
  font-size: 16px;
  font-weight: 700;
  color: #212121;
}
.seg.on::after {
  content: '';
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  bottom: 0;
  width: 20px;
  height: 3px;
  border-radius: 2px;
  background: #d93437;
}

/* ===== 筛选 ===== */
.filter-row {
  display: flex;
  align-items: center;
  margin: 12px 20px 0;
}
.pill {
  height: 28px;
  border-radius: 14px;
  background: #eceff3;
  color: #787f8d;
  font-size: 12px;
  line-height: 28px;
  padding: 0 14px;
  margin-right: 8px;
  cursor: pointer;
}
.pill.on {
  background: #2c2c2c;
  color: #fff;
}
.type-wrap {
  margin-left: auto;
  position: relative;
}
.type-btn {
  font-size: 12px;
  color: #4a4a4a;
  line-height: 28px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}
.caret {
  width: 0;
  height: 0;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 5px solid #4a4a4a;
}
.type-panel {
  position: absolute;
  right: 0;
  top: 32px;
  width: 96px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  z-index: 10;
  overflow: hidden;
}
.type-item {
  height: 36px;
  line-height: 36px;
  text-align: center;
  font-size: 12px;
  color: #4a4a4a;
  cursor: pointer;
}
.type-item.on {
  color: #d93437;
  font-weight: 700;
}

/* ===== 商品卡 ===== */
.goods-list {
  margin: 12px 20px 0;
}
.goods-card {
  background: #fff;
  border-radius: 16px;
  padding: 12px 12px 24px;
}
.goods-main {
  position: relative;
  display: flex;
}
.goods-img {
  width: 90px;
  height: 90px;
  border-radius: 8px;
  background: #f5f5f5;
  object-fit: cover;
  flex-shrink: 0;
}
.goods-info {
  margin-left: 6px;
  padding-top: 10px;
}
.goods-name {
  font-size: 14px;
  font-weight: 700;
  color: #212121;
  line-height: 20px;
}
.goods-meta {
  display: flex;
  align-items: center;
  margin-top: 12px;
  font-size: 11px;
  color: #9aa0ab;
  line-height: 14px;
}
.meta-ic {
  width: 14px;
  height: 14px;
  margin-right: 4px;
}
.goods-price {
  position: absolute;
  right: 0;
  bottom: -6px;
  color: #d93437;
  display: flex;
  align-items: baseline;
}
.p-cur, .p-qi {
  font-size: 11px;
  line-height: 16px;
}
.p-num {
  font-size: 24px;
  line-height: 29px;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
}
.sale-bar {
  margin-top: 12px;
  height: 40px;
  background: #fcecec;
  border-radius: 10px;
  display: flex;
  align-items: center;
  padding: 0 12px;
}
.sale-ic {
  width: 16px;
  height: 16px;
  margin-right: 5px;
}
.sale-text {
  font-size: 12px;
  font-weight: 600;
  color: #d93437;
}
.sale-cd {
  margin-left: auto;
  font-size: 13px;
  font-weight: 700;
  color: #d93437;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
  letter-spacing: 1px;
}

/* ===== 热榜 ===== */
.rank-list {
  margin: 12px 20px 0;
}
.hot-card {
  position: relative;
  height: 82px;
  background: #fff;
  border-radius: 12px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  padding: 0 12px 0 10px;
  box-sizing: border-box;
}
.hot-medal {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}
.hot-no {
  width: 24px;
  text-align: center;
  font-size: 20px;
  color: #d93437;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
  flex-shrink: 0;
}
.hot-img {
  width: 56px;
  height: 56px;
  border-radius: 8px;
  background: #f5f5f5;
  object-fit: cover;
  margin-left: 4px;
  flex-shrink: 0;
}
.hot-info {
  margin-left: 8px;
  width: 159px;
}
.hot-name {
  font-size: 14px;
  font-weight: 700;
  color: #212121;
  line-height: 20px;
}
.hot-line {
  margin-top: 3px;
  font-size: 11px;
  color: #9aa0ab;
  line-height: 16px;
}
.hot-vol {
  margin-top: 2px;
  font-size: 11px;
  color: #d93437;
  line-height: 16px;
}
.hot-right {
  position: absolute;
  right: 12px;
  top: 4px;
  width: 60px;
  height: 58px;
}
.follow-btn {
  position: absolute;
  right: -30px;
  top: 0;
  width: 90px;
  height: 28px;
  cursor: pointer;
}
.hot-price {
  position: absolute;
  right: 0;
  bottom: 0;
  color: #212121;
  display: flex;
  align-items: baseline;
}
.hp-cur {
  font-size: 11px;
}
.hp-num {
  font-size: 20px;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
}

/* ===== 排行（名人堂） ===== */
.rank1-wrap {
  margin: 12px 20px 0;
}
.my-rank-card {
  background: #fff;
  border-radius: 12px;
  padding: 12px;
}
.mrc-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.mrc-name {
  font-size: 13px;
  font-weight: 700;
  color: #212121;
}
.mrc-rule {
  display: flex;
  align-items: center;
  gap: 3px;
  font-size: 10px;
  color: #9aa0ab;
}
.rule-ic {
  width: 12px;
  height: 12px;
}
.mrc-body {
  display: flex;
  align-items: center;
  margin-top: 10px;
}
.mrc-avatar {
  width: 44px;
  height: 44px;
  border-radius: 22px;
  object-fit: cover;
  flex-shrink: 0;
}
.mrc-val {
  margin-left: 10px;
}
.mrc-label {
  font-size: 11px;
  color: #9aa0ab;
  line-height: 16px;
}
.mrc-num {
  margin-top: 2px;
  font-size: 24px;
  color: #d93437;
  line-height: 24px;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
}
.mrc-order {
  margin-left: auto;
  display: flex;
  align-items: baseline;
  gap: 6px;
  font-size: 12px;
  color: #212121;
}
.mrc-order b {
  font-size: 24px;
  color: #d93437;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
}
.podium {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-top: 12px;
}
.pod {
  background: #fff;
  border-radius: 10px;
  width: 105px;
  height: 146px;
  position: relative;
  padding-top: 32px;
  box-sizing: border-box;
  text-align: center;
}
.pod.pod1 {
  width: 113px;
  height: 158px;
}
.pod-badge {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 54px;
  height: 20px;
}
.pod-avatar {
  width: 56px;
  height: 56px;
  border-radius: 28px;
  object-fit: cover;
  margin: 0 auto;
}
.pod-avatar.sm {
  width: 48px;
  height: 48px;
  border-radius: 24px;
}
.pod-name {
  margin-top: 8px;
  font-size: 11px;
  color: #212121;
}
.pod-label {
  margin-top: 10px;
  font-size: 10px;
  color: #9aa0ab;
}
.pod-val {
  margin-top: 2px;
  font-size: 16px;
  color: #d93437;
  font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif;
}
.hall-empty {
  text-align: center;
  padding: 24px 0 40px;
}
.empty-img {
  width: 120px;
  display: block;
  margin: 0 auto;
}
.empty-text {
  margin-top: 8px;
  font-size: 12px;
  color: #9aa0ab;
}

.empty-tip {
  text-align: center;
  padding: 40px 0;
  font-size: 12px;
  color: #9aa0ab;
}
.bottom-space {
  height: 76px;
}
</style>
