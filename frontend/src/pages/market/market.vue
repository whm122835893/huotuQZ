<template>
  <div class="market-page">
    <!-- 顶部标签：藏品/盲盒/关注/排行 + 搜索 -->
    <div class="top-tabs">
      <div
        v-for="t in topTabs"
        :key="t.key"
        class="top-tab"
        :class="{ on: tab === t.key }"
        @click="switchTab(t.key)"
      >
        {{ t.name }}
        <span v-if="tab === t.key" class="tab-underline"></span>
      </div>
      <img class="search-ic" :src="marketSearch" alt="" @click="todo('搜索')" />
    </div>

    <!-- ===== 藏品 ===== -->
    <template v-if="tab === 'collection'">
      <!-- 系列卡片：推荐 / 全部 -->
      <div class="series-row">
        <div class="series-card" :class="{ sel: collSeries === -1 }" @click="pickCollSeries(-1)">
          <img class="series-badge" :src="marketRecommend" alt="" />
          <div class="series-name">推荐</div>
          <div class="series-sub">较昨日 -</div>
        </div>
        <div class="series-card" :class="{ sel: collSeries === 0 }" @click="pickCollSeries(0)">
          <div class="series-name">全部</div>
          <div class="series-sub">较昨日 -</div>
        </div>
      </div>
    </template>

    <!-- ===== 盲盒 ===== -->
    <template v-if="tab === 'blindbox'">
      <div class="series-row">
        <div class="series-card sel">
          <img class="series-badge" :src="marketRecommend" alt="" />
          <div class="series-name">盲盒系列</div>
          <div class="series-sub">较昨日 -</div>
        </div>
      </div>
    </template>

    <!-- ===== 列表（藏品/盲盒/关注共用，排行不显示） ===== -->
    <template v-if="tab !== 'rank'">
      <div class="th-row">
        <div class="th-cell">名称<img class="th-sort" :src="marketSort" alt="" /></div>
        <div class="th-cell">成交量<img class="th-sort" :src="marketSort" alt="" /></div>
        <div class="th-spacer"></div>
        <div class="th-cell">地板价<img class="th-sort" :src="marketSort" alt="" /></div>
        <div class="th-cell th-last">涨跌幅<img class="th-sort" :src="marketSort" alt="" /></div>
      </div>

      <div v-if="currentList.length" class="goods-list">
        <div v-for="g in currentList" :key="g.id" class="goods-row" @click="todo(g.name)">
          <img class="g-thumb" :src="imgUrl(g.listimg)" alt="" />
          <div class="g-info">
            <div class="g-name">{{ g.name }}</div>
            <div class="g-deal">成交量: {{ fmtNum(g.deal_num) }}</div>
          </div>
          <div class="g-price">
            <span class="g-yen">¥</span>
            <span class="g-num">{{ fmtPrice(g.min_price) }}</span>
          </div>
          <div class="g-fluc" :class="{ down: Number(g.market_fluctuate_new) < 0 }">{{ fmtFluc(g.market_fluctuate_new) }}</div>
        </div>
      </div>
      <div v-else class="empty-wrap">
        <img class="empty-img" :src="emptyData" alt="" />
      </div>
    </template>

    <!-- ===== 排行 ===== -->
    <template v-if="tab === 'rank'">
      <!-- 我的排名卡 -->
      <div class="my-rank-card">
        <div class="mrc-top">
          <span class="mrc-name">{{ myName }}</span>
          <span class="mrc-rule"><img class="rule-ic" :src="rankDateIc" alt="" />每日晚24时结算</span>
        </div>
        <div class="mrc-body">
          <img class="mrc-avatar" :src="logoImg" alt="" />
          <div class="mrc-val">
            <div class="mrc-label">当前购入值</div>
            <div class="mrc-num">{{ myRankValue }}</div>
          </div>
          <div class="mrc-nous">{{ myRankNo }}</div>
        </div>
      </div>

      <!-- 前三领奖台 -->
      <div class="podium">
        <div class="pod pod-side" v-if="pod(1)">
          <img class="pod-badge" :src="pod2" alt="" />
          <img class="pod-avatar" :src="logoImg" alt="" />
          <div class="pod-name">{{ pod(1).nickname }}</div>
          <div class="pod-label">购入值</div>
          <div class="pod-val">{{ pod(1).price }}</div>
        </div>
        <div class="pod pod-center" v-if="pod(0)">
          <img class="pod-badge" :src="pod1" alt="" />
          <img class="pod-avatar big" :src="logoImg" alt="" />
          <div class="pod-name">{{ pod(0).nickname }}</div>
          <div class="pod-label">购入值</div>
          <div class="pod-val">{{ pod(0).price }}</div>
        </div>
        <div class="pod pod-side" v-if="pod(2)">
          <img class="pod-badge" :src="pod3" alt="" />
          <img class="pod-avatar" :src="logoImg" alt="" />
          <div class="pod-name">{{ pod(2).nickname }}</div>
          <div class="pod-label">购入值</div>
          <div class="pod-val">{{ pod(2).price }}</div>
        </div>
      </div>

      <!-- 第4名起列表 -->
      <div class="rank-list">
        <div v-for="(r, i) in rankRest" :key="i" class="rank-row">
          <span class="rank-no">{{ i + 4 }}</span>
          <img class="rank-avatar" :src="logoImg" alt="" />
          <div class="rank-info">
            <div class="rank-name">{{ r.nickname }}</div>
            <div class="rank-val">购入值:{{ r.price }}</div>
          </div>
        </div>
      </div>
    </template>

    <TabBar />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import http from '../../api/request'
import TabBar from '../../components/TabBar.vue'

/* 静态资源（均已从原站下载进项目） */
const marketSearch = new URL('../../assets/images/market-search.png', import.meta.url).href
const marketRecommend = new URL('../../assets/images/market-recommend.png', import.meta.url).href
const marketSort = new URL('../../assets/images/market-sort.png', import.meta.url).href
const rankDateIc = new URL('../../assets/images/home-rank-date.png', import.meta.url).href
const pod1 = new URL('../../assets/images/home-rank-user-top1.png', import.meta.url).href
const pod2 = new URL('../../assets/images/home-rank-user-top2.png', import.meta.url).href
const pod3 = new URL('../../assets/images/home-rank-user-top3.png', import.meta.url).href
const logoImg = new URL('../../assets/images/logo.png', import.meta.url).href
const emptyData = new URL('../../assets/images/empty-data.png', import.meta.url).href

const API_BASE = import.meta.env.VITE_API_BASE || ''
function imgUrl(p) {
  if (!p) return ''
  return /^https?:/.test(p) ? p : API_BASE + p
}

/* 数据 */
const topTabs = [
  { key: 'collection', name: '藏品' },
  { key: 'blindbox', name: '盲盒' },
  { key: 'follow', name: '关注' },
  { key: 'rank', name: '排行' }
]
const tab = ref('collection')
const collSeries = ref(0)
const collList = ref([])
const boxList = ref([])
const followList = ref([])
const rankList = ref([])
const myName = ref('-')
const myRankValue = ref('-')
const myRankNo = ref('未上榜')

const currentList = computed(() => {
  if (tab.value === 'collection') return collList.value
  if (tab.value === 'blindbox') return boxList.value
  if (tab.value === 'follow') return followList.value
  return []
})
const rankRest = computed(() => rankList.value.slice(3))
function pod(i) {
  return rankList.value[i] || null
}

const fmtNum = (v) => Number(v || 0).toLocaleString('en-US')
const fmtPrice = (v) => (v === null || v === undefined ? '--' : Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }))
const fmtFluc = (v) => {
  const n = Number(v || 0)
  return (n >= 0 ? '+ ' : '- ') + Math.abs(n).toFixed(2) + '%'
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

async function loadColl() {
  try {
    const r = await http.post('/api/market/market/getMarketList', {
      goods_type: 2, page: 1, list_rows: 10, series_id: collSeries.value,
      add_market_fluctuate: true, add_deal_num: 1
    })
    collList.value = Array.isArray(r.data) ? r.data : []
  } catch (e) { /* 静默 */ }
}
function pickCollSeries(id) {
  if (collSeries.value === id) return
  collSeries.value = id
  loadColl()
}

async function switchTab(key) {
  if (tab.value === key) return
  tab.value = key
  if (key === 'blindbox' && !boxList.value.length) {
    try {
      const r = await http.post('/api/market/market/marketList', { goods_type: 1, page: 1, list_rows: 10, series_id: 3 })
      boxList.value = r.data?.data || []
    } catch (e) { /* 静默 */ }
  }
  if (key === 'follow' && !followList.value.length) {
    try {
      const r = await http.post('/api/market/market/getMarketList', { goods_type: 2, page: 1, list_rows: 10, series_id: -2 })
      followList.value = Array.isArray(r.data) ? r.data : []
    } catch (e) { /* 静默 */ }
  }
  if (key === 'rank' && !rankList.value.length) loadRank()
}

async function loadRank() {
  try {
    const r = await http.post('/api/user/newrank1', {})
    if (r.code === 1 && r.data) {
      rankList.value = r.data.rank2 || []
      const my = r.data.my2 || {}
      myRankValue.value = my.price > 0 ? String(my.price) : '-'
      myRankNo.value = my.myrank || '未上榜'
    }
  } catch (e) { /* 静默 */ }
}

onMounted(async () => {
  loadColl()
  try {
    const u = await http.post('/api/user/getUserInfo', {})
    if (u.code === 1 && u.data) myName.value = u.data.nickname || '-'
  } catch (e) { /* 静默 */ }
})
</script>

<style scoped>
.market-page {
  min-height: 100vh;
  background: #f4f5f5;
  padding-bottom: 76px;
}

/* ===== 顶部标签 ===== */
.top-tabs {
  position: relative;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 11px 20px 0;
  background: #f4f5f5;
}
.top-tab {
  position: relative;
  font-size: 16px;
  line-height: 22px;
  color: #9aa0ab;
  padding-bottom: 11px;
  cursor: pointer;
}
.top-tab.on {
  font-weight: 700;
  color: #212121;
}
.tab-underline {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  bottom: 0;
  width: 24px;
  height: 3px;
  border-radius: 2px;
  background: #d93437;
}
.search-ic {
  position: absolute;
  right: 22px;
  top: 12px;
  width: 20px;
  height: 20px;
  cursor: pointer;
}

/* ===== 系列卡片 ===== */
.series-row {
  margin-top: 12px;
  padding: 0 20px;
  display: flex;
  gap: 8px;
}
.series-card {
  position: relative;
  width: 120px;
  height: 64px;
  background: #eef1f5;
  border: 1px solid transparent;
  border-radius: 10px;
  padding: 10px 10px 8px;
  cursor: pointer;
}
.series-card.sel {
  background: #fdecee;
  border-color: #d93437;
}
.series-badge {
  position: absolute;
  top: -1px;
  right: -1px;
  width: 33px;
  height: 14px;
}
.series-name {
  font-size: 14px;
  line-height: 20px;
  font-weight: 700;
  color: #212121;
}
.series-sub {
  margin-top: 5px;
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
}

/* ===== 表头 ===== */
.th-row {
  display: flex;
  align-items: center;
  padding: 12px 20px 8px;
}
.th-cell {
  display: flex;
  align-items: center;
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
  white-space: nowrap;
}
.th-cell + .th-cell {
  margin-left: 11px;
}
.th-spacer {
  flex: 1;
}
.th-last {
  margin-left: 42px;
}
.th-sort {
  width: 10px;
  height: 10px;
  margin-left: 3px;
}

/* ===== 商品行 ===== */
.goods-list {
  padding: 0 20px;
}
.goods-row {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 12px;
  padding: 10px;
  margin-bottom: 8px;
  cursor: pointer;
}
.g-thumb {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: block;
  object-fit: cover;
}
.g-info {
  margin-left: 8px;
  width: 100px;
}
.g-name {
  font-size: 14px;
  line-height: 20px;
  font-weight: 700;
  color: #212121;
}
.g-deal {
  margin-top: 4px;
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
}
.g-price {
  margin-left: auto;
  display: flex;
  align-items: baseline;
  color: #212121;
}
.g-yen {
  font-size: 11px;
}
.g-num {
  font-family: var(--ht-font-num);
  font-size: 18px;
  line-height: 21px;
}
.g-fluc {
  margin-left: 24px;
  width: 64px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  background: #d93437;
  font-size: 11px;
  color: #fff;
  white-space: nowrap;
}
.g-fluc.down {
  background: #00b578;
}

/* ===== 空态 ===== */
.empty-wrap {
  margin-top: 85px;
  display: flex;
  justify-content: center;
}
.empty-img {
  width: 142px;
  height: 142px;
}

/* ===== 排行 ===== */
.my-rank-card {
  margin: 12px 20px 0;
  height: 94px;
  background: #fff url('../../assets/images/home-rank-card.png') no-repeat center / 100% 100%;
  border-radius: 12px;
  padding: 12px 12px 10px;
}
.mrc-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.mrc-name {
  font-size: 13px;
  line-height: 18px;
  font-weight: 700;
  color: #212121;
}
.mrc-rule {
  display: flex;
  align-items: center;
  font-size: 10px;
  color: #9aa0ab;
}
.rule-ic {
  width: 12px;
  height: 12px;
  margin-right: 3px;
}
.mrc-body {
  margin-top: 10px;
  display: flex;
  align-items: center;
}
.mrc-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
}
.mrc-val {
  margin-left: 10px;
}
.mrc-label {
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
}
.mrc-num {
  font-family: var(--ht-font-num);
  font-size: 20px;
  line-height: 22px;
  color: #212121;
}
.mrc-nous {
  margin-left: auto;
  font-size: 28px;
  line-height: 33px;
  color: #d93437;
}

/* 领奖台 */
.podium {
  margin-top: 12px;
  padding: 0 20px;
  display: flex;
  align-items: flex-end;
  gap: 8px;
}
.pod {
  background: #fff;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
}
.pod-side {
  width: 105px;
  height: 146px;
  padding: 10px 6px 10px;
}
.pod-center {
  width: 109px;
  height: 158px;
  padding: 10px 6px 10px;
}
.pod-badge {
  width: 54px;
  height: 20px;
}
.pod-avatar {
  width: 48px;
  height: 48px;
  margin-top: 10px;
  border-radius: 50%;
}
.pod-avatar.big {
  width: 56px;
  height: 56px;
}
.pod-name {
  margin-top: 8px;
  max-width: 93px;
  font-size: 11px;
  line-height: 16px;
  font-weight: 700;
  color: #212121;
  text-align: center;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.pod-label {
  margin-top: 4px;
  font-size: 10px;
  line-height: 14px;
  color: #9aa0ab;
}
.pod-val {
  font-family: var(--ht-font-num);
  font-size: 16px;
  line-height: 19px;
  color: #212121;
}

/* 排行列表 */
.rank-list {
  margin-top: 8px;
  padding: 0 20px;
}
.rank-row {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 12px;
  padding: 10px;
  margin-bottom: 8px;
  cursor: pointer;
}
.rank-no {
  width: 24px;
  text-align: center;
  font-family: var(--ht-font-num);
  font-size: 20px;
  line-height: 27px;
  color: #9aa0ab;
}
.rank-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-left: 6px;
}
.rank-info {
  margin-left: 8px;
}
.rank-name {
  font-size: 13px;
  line-height: 18px;
  font-weight: 700;
  color: #212121;
}
.rank-val {
  margin-top: 5px;
  font-size: 11px;
  line-height: 16px;
  color: #9aa0ab;
}
</style>
