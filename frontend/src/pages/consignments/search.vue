<template>
  <!-- P071 寄售搜索：圆角搜索框 + 输入后三Tab + 搜索历史 + myConsignment{keyword} -->
  <div class="cs-page se-page">
    <div class="cs-nav se-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <div class="se-input">
        <svg class="se-icon" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="8.6" cy="8.6" r="5.6" stroke="#B7BAC1" stroke-width="1.6" />
          <line x1="12.8" y1="12.8" x2="17.4" y2="17.4" stroke="#B7BAC1" stroke-width="1.6" stroke-linecap="round" />
        </svg>
        <input v-model="keyword" class="se-field" type="search" placeholder="请输入搜索内容"
          @focus="onFocus" @blur="onBlur" @input="onInput" @keyup.enter="doSearch" />
        <span v-if="keyword" class="se-clear" @mousedown.prevent="clear">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="6" cy="6" r="6" fill="#C6C7CB" />
            <path d="M4 4l4 4M8 4l-4 4" stroke="#FFFFFF" stroke-width="1.2" stroke-linecap="round" />
          </svg>
        </span>
      </div>
    </div>

    <!-- 搜索历史（有历史且聚焦时显示，原站 showHistory 逻辑） -->
    <div v-if="showHistory" class="se-history">
      <div class="se-his-head">
        <i>搜索历史</i>
        <span class="se-his-clear" @mousedown.prevent="clearHistory">清除</span>
      </div>
      <div class="se-his-tags">
        <span v-for="(h, i) in history" :key="i" class="se-his-tag" @mousedown.prevent="pick(h)">{{ h }}</span>
      </div>
    </div>

    <!-- 输入关键词后显示三 Tab -->
    <div v-if="keyword && !showHistory" class="se-tabs">
      <span v-for="t in tabs" :key="t.name" class="se-tab"
        :class="{ on: goodsType === t.type }" @click="changeTab(t)">{{ t.name }}</span>
    </div>

    <!-- 搜索结果：原站该账号恒空，demo 结构预览 -->
    <div v-if="searched && !list.length && !keyword" class="cs-empty se-empty">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>
    <div v-else-if="searched && list.length" class="se-list">
      <div v-for="it in list" :key="it.id" class="se-item">
        <img :src="localImg(it.goods_cover_image)" alt="" />
        <div class="se-item-info">
          <i class="se-item-name">{{ it.goods_name }}</i>
          <span class="se-item-price">¥{{ it.price }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, localImg, DEMO_CONSIGNMENT } from './demo'
import './common.css'

const router = useRouter()
const keyword = ref('')
const goodsType = ref('')
const searched = ref(false)
const list = ref([])
const history = ref(readHistory())
const hasFocus = ref(true)
const tabs = [
  { name: '全部', type: '' },
  { name: '数字藏品', type: '2' },
  { name: '盲盒', type: '1' },
]
const showHistory = computed(() => history.value.length && hasFocus.value && !searched.value)

const HIS_KEY = 'consignments-history'
// uni-app H5 storage 契约：读写 {"type":"...","data":...} 包装
function readHistory() {
  try {
    const raw = localStorage.getItem(HIS_KEY)
    if (!raw) return []
    const o = JSON.parse(raw)
    const v = o && typeof o === 'object' && 'data' in o ? o.data : o
    return Array.isArray(v) ? v : []
  } catch (e) { return [] }
}
function writeHistory(arr) {
  try { localStorage.setItem(HIS_KEY, JSON.stringify({ type: 'object', data: arr })) } catch (e) { /* ignore */ }
}
function setHistory(kw) {
  const arr = readHistory().filter((x) => x !== kw)
  arr.unshift(kw)
  if (arr.length > 10) arr.pop()
  writeHistory(arr)
  history.value = arr
}
function clearHistory() { writeHistory([]); history.value = [] }
function pick(kw) { keyword.value = kw; doSearch() }
function clear() { keyword.value = ''; searched.value = false; list.value = [] }

function goBack() { router.back() }
function onFocus() { hasFocus.value = true }
function onBlur() { setTimeout(() => { hasFocus.value = false }, 120) }
function onInput() { if (!keyword.value) { searched.value = false; list.value = [] } }
function changeTab(t) { goodsType.value = t.type; doSearch() }

async function doSearch() {
  const kw = keyword.value.trim()
  if (!kw) return
  setHistory(kw)
  hasFocus.value = false
  searched.value = true
  if (isDemo()) { list.value = DEMO_CONSIGNMENT.filter((x) => x.goods_name.includes(kw)); return }
  try {
    const res = await http.post('/api/order/order/myConsignment',
      { keyword: kw, status: '', goods_type: goodsType.value, page: 1 })
    if (res.code === 1 && res.data) list.value = res.data.data || []
  } catch (e) { list.value = [] }
}
</script>

<style scoped>
.se-page { background: #FFFFFF; }
.se-nav { justify-content: flex-start; padding: 0 16px; }
/* 原站实测：搜索框 x=54 w=287 h=34 @y=5，bg #F2F2F2 r100；放大镜 x=60 */
.se-input {
  margin-left: 38px; width: 287px; height: 34px; border-radius: 100px;
  background: #F2F2F2; display: flex; align-items: center; padding: 0 12px;
}
.se-icon { width: 18px; height: 18px; flex: none; }
.se-field { flex: 1; border: none; outline: none; background: transparent; font-size: 14px; color: var(--cs-ink); margin-left: 6px; }
.se-field::placeholder { color: #ABABAB; font-size: 14px; }
.se-field::-webkit-search-cancel-button { -webkit-appearance: none; }
.se-clear { width: 12px; height: 12px; display: flex; cursor: pointer; flex: none; }

.se-history { padding: 20px 20px 0; }
.se-his-head { display: flex; align-items: center; justify-content: space-between; }
.se-his-head i { font-style: normal; font-size: 14px; font-weight: 500; color: var(--cs-ink); }
.se-his-clear { font-size: 13px; color: #999999; cursor: pointer; }
.se-his-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
.se-his-tag { padding: 6px 14px; background: #F2F2F2; border-radius: 100px; font-size: 13px; color: #666666; cursor: pointer; }

/* 原站实测 Tab 文字：全部 x=76 / 数字藏品 x=162 / 盲盒 x=276，y=44（fs14/fw500 红） */
.se-tabs {
  position: relative; height: 32px; margin-top: -6px;
  display: flex; align-items: center; padding-left: 43px;
}
.se-tab { width: 100px; flex: none; text-align: center; font-size: 14px; font-weight: 400; color: #ABABAB; cursor: pointer; line-height: 20px; }
.se-tab.on { color: #DF0007; font-weight: 500; }

.se-empty { padding-top: 250px; }
.se-list { padding: 12px 20px; }
.se-item { display: flex; align-items: center; padding: 12px; background: #FFFFFF; border-radius: 12px; margin-bottom: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
.se-item img { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; }
.se-item-info { margin-left: 12px; flex: 1; }
.se-item-name { font-style: normal; font-size: 14px; color: var(--cs-ink); display: block; }
.se-item-price { font-size: 15px; font-weight: 600; color: var(--cs-red); }
</style>
