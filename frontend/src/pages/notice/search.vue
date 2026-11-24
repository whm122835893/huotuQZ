<template>
  <!-- P040 公告搜索：与原站 search 页逐像素对齐（自动聚焦搜索框/搜索历史notice-history/结果卡335/空态empty-data 142） -->
  <div class="search-page">
    <!-- 导航 44：返回 + 搜索框 -->
    <div class="nav">
      <img class="back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <div class="search-bar">
        <img class="sb-icon" :src="ICON('notice-search.png')" alt="" />
        <input
          ref="inputEl"
          v-model="keyword"
          class="sb-input"
          type="search"
          placeholder="请输入搜索内容"
          @focus="hasFocus = true"
          @blur="onBlur"
          @keyup.enter="doSearch()"
          @search="doSearch()"
        />
      </div>
    </div>

    <!-- 搜索历史（聚焦且有历史时显示） -->
    <div v-if="showHistory" class="history">
      <div class="his-head">
        <span class="his-title">搜索历史</span>
        <span class="his-clear" @click="clearHistory">清空</span>
      </div>
      <div class="his-pills">
        <span v-for="(h, i) in history" :key="'his-' + i" class="his-pill" @click="doSearch(h)">{{ h }}</span>
      </div>
    </div>

    <!-- 搜索结果 -->
    <div v-else class="results">
      <div v-for="a in displayNoticeList" :key="'notice-' + a.id" class="card" @click="goNoticeDetail(a)">
        <div class="card-row1">
          <div class="card-icbox">
            <img class="card-ic" :src="a.typeIcon" :alt="a.typeName" />
          </div>
          <span class="card-type">「{{ a.typeName }}」</span>
          <span class="card-time">{{ a.time }}</span>
        </div>
        <div class="card-row2">
          <span class="card-title">{{ a.title }}</span>
          <span class="card-arrow">›</span>
        </div>
        <div class="card-content">{{ a.content }}</div>
      </div>

      <!-- 空态 / 加载 -->
      <div v-if="loading === 'empty'" class="empty">
        <img class="empty-img" :src="ICON('empty-data.png')" alt="" />
      </div>
      <div v-else-if="loading === 'loading'" class="loading-tip">加载中...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import http from '../../api/request'

const ICON = (n) => new URL(`../../assets/images/${n}`, import.meta.url).href

const inputEl = ref(null)
const keyword = ref('')
const hasFocus = ref(true)
const history = ref([])
const list = ref([])
const page = ref(1)
const loading = ref('none')

const showHistory = computed(() => history.value.length > 0 && hasFocus.value)

/* 展示字段加工与原站 displayNoticeList 一致 */
const displayNoticeList = computed(() =>
  (list.value || []).map((a) => ({
    ...a,
    typeName: displayText(a.affiche_type),
    typeIcon: getCategoryIcon(a.affiche_type),
    time: formatDateTime(a.createtime),
    title: displayText(a.title),
    content: displayText(getNoticeSummary(a))
  }))
)

function displayText(v) {
  return v === 0 || v === '0' ? '0' : v === undefined || v === null || v === '' ? '-' : v
}
function getCategoryIcon(t) {
  const s = String(t || '')
  if (s.indexOf('上新') > -1) return ICON('notice-new.png')
  if (s.indexOf('活动') > -1) return ICON('notice-activity.png')
  if (s.indexOf('限价') > -1) return ICON('notice-limit.png')
  if (s.indexOf('寄售') > -1) return ICON('notice-consign.png')
  return ICON('notice-official.png')
}
function getNoticeSummary(a) {
  if (!a) return ''
  const s = a.desc || a.summary || a.intro || a.content || ''
  return String(s).replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').trim()
}
function formatDateTime(ts) {
  if (!ts) return '-'
  const d = new Date(Number(ts) * 1000)
  if (Number.isNaN(d.getTime())) return '-'
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}.${p(d.getMonth() + 1)}.${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}

/* 历史记录：本地存储 key=notice-history，最多 10 条、去重、最新在前（与原站一致） */
function getHistory() {
  let arr = []
  try {
    const raw = localStorage.getItem('notice-history')
    arr = raw ? JSON.parse(raw) : []
  } catch (e) {
    arr = []
  }
  history.value = arr
  return arr
}
function setHistory(kw) {
  const arr = getHistory()
  if (arr.length > 9) arr.splice(10, 1)
  const idx = arr.indexOf(kw)
  if (idx > -1) arr.splice(idx, 1)
  arr.unshift(kw)
  localStorage.setItem('notice-history', JSON.stringify(arr))
  history.value = arr
}
function clearHistory() {
  localStorage.removeItem('notice-history')
  getHistory()
}

function doSearch(kw) {
  const k = kw !== undefined ? kw : keyword.value
  if (!k) {
    showToast('搜索内容不可为空')
    return
  }
  keyword.value = k
  hasFocus.value = false
  if (inputEl.value) inputEl.value.blur()
  page.value = 1
  list.value = []
  getList({ search: k, page: 1 })
  setHistory(k)
}

async function getList(form) {
  if (loading.value === 'loading') return
  loading.value = 'loading'
  try {
    const res = await http.post('/api/index/afficheList', form)
    const d = res.data || {}
    const rows = d.data || []
    if (rows.length) page.value = form.page
    list.value = list.value.concat(rows)
    loading.value = 'none'
    if (!d.total) loading.value = 'empty'
  } catch (e) {
    loading.value = 'none'
  }
}

function goNoticeDetail(a) {
  window.location.href = `#/pages/notice/noticeDetails?id=${a.id}&category=${encodeURIComponent(a.affiche_type || '')}`
}
function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = '#/pages/notice/notice'
}
function onBlur() {
  setTimeout(() => (hasFocus.value = false), 100)
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

onMounted(() => {
  getHistory()
  if (inputEl.value) inputEl.value.focus()
})
</script>

<style scoped>
.search-page {
  min-height: 100vh;
  background: #f7f9fc;
}

/* ===== 导航 44：返回 + 搜索框 ===== */
.nav {
  position: sticky;
  top: 0;
  z-index: 5;
  height: 44px;
  background: #fff;
  display: flex;
  align-items: center;
}
.back {
  width: 7px;
  height: 13px;
  margin-left: 19px;
  flex-shrink: 0;
  cursor: pointer;
}
.search-bar {
  margin-left: 6px;
  width: 320px;
  height: 34px;
  background: #f5f5f5;
  border-radius: 17px;
  display: flex;
  align-items: center;
  padding: 0 10px;
}
.sb-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}
.sb-input {
  flex: 1;
  min-width: 0;
  height: 100%;
  border: none;
  outline: none;
  background: transparent;
  margin-left: 4px;
  font-size: 14px;
  color: #000;
}
.sb-input::placeholder {
  color: #909399;
  font-size: 14px;
}
.sb-input::-webkit-search-cancel-button {
  display: none;
}

/* ===== 搜索历史 ===== */
.history {
  padding: 16px 20px 20px;
}
.his-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.his-title {
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-weight: 700;
  font-size: 14px;
  color: #212121;
  line-height: 20px;
}
.his-clear {
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 12px;
  color: #9aa0ab;
  line-height: 17px;
  cursor: pointer;
}
.his-pills {
  margin-top: 8px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.his-pill {
  padding: 4px 12px;
  background: #eef1f5;
  border-radius: 32px;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 12px;
  color: #212121;
  line-height: 17px;
  cursor: pointer;
}

/* ===== 搜索结果 ===== */
.results {
  padding: 12px 20px 40px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.card {
  width: 335px;
  min-height: 159px;
  padding: 20px 14px;
  box-sizing: border-box;
  background: #fff;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}
.card-row1 {
  display: flex;
  align-items: center;
}
.card-icbox {
  width: 40px;
  height: 40px;
  background: #d93437;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.card-ic {
  width: 24px;
  height: 24px;
  filter: brightness(0) invert(1);
}
.card-type {
  margin-left: 6px;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 14px;
  color: #212121;
  line-height: 20px;
}
.card-time {
  margin-left: auto;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 12px;
  color: #c2c5cc;
  line-height: 16px;
  flex-shrink: 0;
}
.card-row2 {
  margin-top: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.card-title {
  flex: 1;
  min-width: 0;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-weight: 500;
  font-size: 14px;
  color: #212121;
  line-height: 22px;
}
.card-arrow {
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 16px;
  color: #c5cad3;
  line-height: 16px;
  flex-shrink: 0;
}
.card-content {
  margin-top: 10px;
  font-family: 'Alibaba-PuHuiTi', sans-serif;
  font-size: 12px;
  color: #787f8d;
  line-height: 18px;
}

.empty {
  width: 100%;
  min-height: 240px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.empty-img {
  width: 142px;
  height: 142px;
}
.loading-tip {
  text-align: center;
  font-size: 12px;
  color: #9aa0ab;
  padding: 8px 0;
}
</style>
