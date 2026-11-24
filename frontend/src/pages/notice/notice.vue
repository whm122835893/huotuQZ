<template>
  <!-- P038 发现-消息通知：与原站逐像素对齐（标题/副标题/黑椭圆搜索钮/9分类横滚/公告卡列表） -->
  <div class="notice-page">
    <!-- 顶部 -->
    <div class="head">
      <h1 class="page-title">消息通知</h1>
      <p class="page-sub">重要信息及时掌握，不错过每一次活动</p>
      <div class="search-btn" @click="onSearch">
        <img class="search-ic" :src="imgSearch" alt="搜索" />
      </div>
    </div>

    <!-- 分类横滚 -->
    <div class="cat-scroll">
      <div class="cat-row">
        <div
          v-for="cat in cats"
          :key="cat.id"
          class="cat-item"
          @click="selectCat(cat)"
        >
          <div class="cat-box" :class="{ on: curCat === cat.id }">
            <img class="cat-ic" :class="{ dim: curCat !== cat.id }" :src="cat.icon" :alt="cat.name" />
          </div>
          <span class="cat-label" :class="{ on: curCat === cat.id }">{{ cat.name }}</span>
        </div>
      </div>
    </div>

    <!-- 公告列表 -->
    <div class="list">
      <div v-for="item in list" :key="item.id" class="card" @click="onCard(item)">
        <div class="card-top">
          <div class="card-icbox">
            <img class="card-ic" :src="typeIcon(item.affiche_type)" :alt="item.affiche_type" />
          </div>
          <span class="card-type">「{{ item.affiche_type }}」</span>
          <span class="card-time">{{ fmtTime(item.createtime) }}</span>
        </div>
        <div class="card-bottom">
          <span class="card-title">{{ item.title }}</span>
          <img class="card-arrow" :src="imgArrow" alt="" />
        </div>
      </div>
      <div v-if="loading" class="loading-tip">加载中...</div>
      <div v-else-if="finished && list.length" class="loading-tip">没有更多了</div>
    </div>

    <TabBar />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import http from '../../api/request'
import TabBar from '../../components/TabBar.vue'

const imgSearch = new URL('../../assets/images/notice-search.png', import.meta.url).href
const imgArrow = new URL('../../assets/images/checkin-arrow-right.png', import.meta.url).href

const ICON = (n) => new URL(`../../assets/images/${n}`, import.meta.url).href
/* 图标映射按原站 DOM 实测（求购与官方共用公文包图标） */
const catIconMap = { 全部: 'notice-all.png', 官方: 'notice-official.png', 运营: 'notice-operation.png', 活动: 'notice-activity.png', 寄售: 'notice-consign.png', 空投: 'notice-airdrop.png', 求购: 'notice-official.png', 限价: 'notice-limit.png', 合成: 'notice-new.png' }
const typeIconMap = { 官方: 'notice-official.png', 运营: 'notice-operation.png', 活动: 'notice-activity.png', 寄售: 'notice-consign.png', 空投: 'notice-airdrop.png', 求购: 'notice-official.png', 限价: 'notice-limit.png', 合成: 'notice-new.png' }

const cats = ref([{ id: 0, name: '全部' }])
const curCat = ref(0)
const list = ref([])
const page = ref(1)
const total = ref(0)
const loading = ref(false)
const finished = ref(false)

function typeIcon(t) {
  return ICON(typeIconMap[t] || 'notice-official.png')
}

function fmtTime(ts) {
  const d = new Date(Number(ts) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}.${p(d.getMonth() + 1)}.${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
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

function onSearch() {
  window.location.href = '#/pages/notice/search'
}
function onCard(item) {
  window.location.href = `#/pages/notice/noticeDetails?id=${item.id}&category=${encodeURIComponent(item.affiche_type || '')}`
}

async function loadTypes() {
  const res = await http.post('/api/index/affichetypelist')
  const types = Array.isArray(res.data) ? res.data : []
  cats.value = [
    { id: 0, name: '全部', icon: ICON(catIconMap['全部']) },
    ...types.map((t) => ({ id: t.id, name: t.name, icon: ICON(catIconMap[t.name] || 'notice-official.png') }))
  ]
}

async function loadList(reset = false) {
  if (loading.value || finished.value) return
  loading.value = true
  try {
    const res = await http.post('/api/index/afficheList', { page: page.value, list_rows: 10, category: curCat.value })
    const d = res.data || {}
    const rows = d.data || []
    if (reset) list.value = []
    list.value.push(...rows)
    total.value = Number(d.total || 0)
    if (!rows.length || list.value.length >= total.value) finished.value = true
    else page.value += 1
  } finally {
    loading.value = false
  }
}

function selectCat(cat) {
  if (curCat.value === cat.id) return
  curCat.value = cat.id
  page.value = 1
  finished.value = false
  list.value = []
  loadList(true)
}

function onScroll() {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  if (scrollTop + window.innerHeight >= document.documentElement.scrollHeight - 60) {
    loadList()
  }
}

onMounted(async () => {
  await loadTypes()
  await loadList(true)
  window.addEventListener('scroll', onScroll)
})
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.notice-page {
  min-height: 100vh;
  background: #f5f6f8;
  padding-bottom: 76px;
}

/* ===== 顶部 ===== */
.head {
  position: relative;
  padding: 19px 20px 0;
  background: #f5f6f8;
}
.page-title {
  margin: 0;
  font-family: 'CKTKingKong', 'Alibaba-PuHuiTi', sans-serif;
  font-size: 24px;
  line-height: 33px;
  font-weight: 700;
  color: #212121;
}
.page-sub {
  margin: 2px 0 0;
  font-size: 14px;
  line-height: 20px;
  color: #787f8d;
}
.search-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 45px;
  height: 35px;
  border-radius: 21px;
  background: #212121;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.search-ic {
  width: 20px;
  height: 20px;
}

/* ===== 分类横滚 ===== */
.cat-scroll {
  margin-top: 20px;
  padding: 0 20px;
  overflow-x: auto;
  scrollbar-width: none;
}
.cat-scroll::-webkit-scrollbar {
  display: none;
}
.cat-row {
  display: inline-flex;
  gap: 16px;
}
.cat-item {
  width: 40px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.cat-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #f0f3f6;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.cat-box.on {
  background: #d93437;
}
.cat-ic {
  width: 24px;
  height: 24px;
}
.cat-ic.dim {
  filter: brightness(0) invert(62%);
}
.cat-label {
  margin-top: 6px;
  font-size: 12px;
  line-height: 17px;
  white-space: nowrap;
  color: #9aa0ab;
}
.cat-label.on {
  color: #d93437;
}

/* ===== 公告列表 ===== */
.list {
  margin-top: 20px;
  padding: 0 20px;
}
.card {
  background: #fff;
  border-radius: 12px;
  padding: 20px 14px;
  margin-bottom: 14px;
  cursor: pointer;
}
.card-top {
  display: flex;
  align-items: center;
}
.card-icbox {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #d93437;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.card-ic {
  width: 24px;
  height: 24px;
}
.card-type {
  margin-left: 6px;
  font-size: 14px;
  color: #212121;
}
.card-time {
  margin-left: auto;
  font-size: 12px;
  color: #c2c5cc;
}
.card-bottom {
  margin-top: 11px;
  display: flex;
  align-items: flex-start;
}
.card-title {
  flex: 1;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 14px;
  line-height: 20px;
  font-weight: 500;
  color: #212121;
  white-space: pre-line;
  word-break: break-all;
}
.card-arrow {
  width: 12px;
  height: 12px;
  margin-left: 10px;
  margin-top: 4px;
  flex-shrink: 0;
}
.loading-tip {
  text-align: center;
  font-size: 12px;
  color: #9aa0ab;
  padding: 8px 0 4px;
}
</style>
