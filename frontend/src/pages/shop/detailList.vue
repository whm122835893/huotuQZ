<template>
  <!-- P050 龙晶明细：导航44 / u-sticky 104 / 三tab 14px / 蓝色下划线18x4 / 空态142 -->
  <div class="shop-page detail-page">
    <div class="shop-nav">
      <img class="nav-back is-dark" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">龙晶明细</span>
    </div>

    <div class="tabs-wrap">
      <div class="tabs">
        <div v-for="(t, i) in tabs" :key="t.value" class="tab" :class="{ active: active === i }" @click="switchTab(i)">
          {{ t.name }}
        </div>
      </div>
      <div class="tab-line" :style="{ left: lineLeft }"></div>
      <!-- 原站 u-icon 字体未加载时渲染出的同名文本，按实测保留 -->
      <span class="more-cate">more-cate</span>
    </div>

    <div v-if="list.length" class="detail-list">
      <div v-for="r in list" :key="r.id" class="detail-row">
        <div class="row-title">{{ r.title || r.memo }}</div>
        <div class="row-time">{{ fmtTime(r) }}</div>
      </div>
      <div class="shop-nomore">已经到底了~</div>
    </div>
    <div v-else class="shop-empty empty-pos">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>暂无数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_DETAIL } from './demo'
import './common.css'

const router = useRouter()
const tabs = [{ name: '全部', value: 0 }, { name: '龙晶获取', value: 1 }, { name: '龙晶消耗', value: 2 }]
const active = ref(0)
const all = ref([])
const lineLeft = computed(() => `${73.5 + active.value * 105}px`)

const list = computed(() => {
  const v = tabs[active.value].value
  return v === 0 ? all.value : all.value.filter(x => Number(x.type) === v)
})

if (isDemo()) all.value = DEMO_DETAIL
else load()

async function load() {
  try {
    const res = await http.post('/api/crystal_goods/crystalDetail', { page: 1 })
    all.value = res.data?.data || []
  } catch (e) { all.value = [] }
}

function fmtTime(r) {
  if (r.time) return r.time
  const t = Number(r.createtime || 0)
  if (!t) return ''
  const d = new Date(t * 1000)
  const p = n => String(n).padStart(2, '0')
  return `${d.getFullYear()}.${p(d.getMonth() + 1)}.${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}

function switchTab(i) { active.value = i }
function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.detail-page { background: #F4F6FA; }
.tabs-wrap { position: relative; height: 60px; padding-top: 10px; }
.tabs { display: flex; height: 40px; margin: 0 30px; }
.tab {
  flex: 1; display: flex; align-items: center; justify-content: center;
  font-size: 14px; color: #333333; cursor: pointer;
}
.tab.active { color: #212121; font-weight: 600; }
.tab-line {
  position: absolute; top: 45px; width: 18px; height: 4px;
  background: #2A51EF; border-radius: 2px; transition: left .2s;
}
.more-cate {
  position: absolute; top: 22px; right: 16px;
  width: 64px; height: 16px; line-height: 16px;
  font-size: 16px; color: #FFFFFF;
  white-space: nowrap; overflow: hidden;
}
.detail-list { margin: 8px 16px 0; background: #FFFFFF; border-radius: 12px; padding: 0 16px; }
.detail-row { padding: 16px 0; border-bottom: 1px solid #F0F0F0; }
.detail-row:last-child { border-bottom: none; }
.row-title { font-size: 16px; color: #333333; }
.row-time { margin-top: 6px; font-size: 11px; color: #999999; }
.empty-pos { position: absolute; top: 304px; left: 0; }
</style>
