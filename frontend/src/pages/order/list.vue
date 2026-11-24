<template>
  <!-- P060 订单列表：主Tab(常规/求购/委托/出售) + 状态胶囊 + 订单卡片 + 搜索入口 -->
  <div class="ord-page list-page">
    <div class="ord-top-tabs">
      <div class="tt-list">
        <div v-for="(t, i) in mainTabs" :key="i" class="tt" :class="{ on: mainIdx === i }" @click="onMain(i, t)">{{ t.label }}</div>
      </div>
      <div class="nav-extra" @click="router.push('/pages/order/search')">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="8.6" cy="8.6" r="5.6" stroke="#212121" stroke-width="1.6" />
          <line x1="12.8" y1="12.8" x2="17.4" y2="17.4" stroke="#212121" stroke-width="1.6" stroke-linecap="round" />
        </svg>
      </div>
    </div>

    <div class="ord-pills">
      <div v-for="(p, i) in pills" :key="i" class="pill" :class="{ on: pillIdx === i }" @click="onPill(i)">{{ p.label }}</div>
    </div>

    <div v-if="list.length" class="ord-list">
      <div v-for="o in list" :key="o.id" class="ord-card" @click="toDetail(o)">
        <div class="oc-head">
          <span class="oc-sn">{{ o.order_sn }}</span>
          <img class="oc-copy" src="../../assets/images/order-icon-copy.png" alt="复制" @click.stop="copy(o.order_sn)" />
          <span class="oc-time">{{ o.create_text }}</span>
        </div>
        <div class="oc-body">
          <img class="oc-img" :src="o.local_image || o.goods_cover_image" alt="" />
          <div class="oc-info">
            <div class="oc-name">{{ o.goods_name }}</div>
            <div class="oc-line">创建订单: {{ o.create_text }}</div>
            <div class="oc-line">转入成功: {{ o.create_text }}</div>
          </div>
          <div class="oc-right">
            <div class="oc-status">空投转入成功</div>
            <div class="oc-num">x {{ o.num }}</div>
          </div>
        </div>
        <div class="oc-foot">
          <span class="oc-more" @click.stop="toDetail(o)">更多信息
            <img src="../../assets/images/icon-right.png" alt="" />
          </span>
          <span class="oc-pay">实付款 <b>¥</b><strong>{{ o.total_price }}</strong></span>
        </div>
      </div>
      <div class="ord-nomore">已经到底了~</div>
    </div>
    <div v-else class="ord-empty empty-pos">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, REAL_ORDERS } from './demo'
import './common.css'

const router = useRouter()
const mainTabs = [
  { label: '常规', type: '' },
  { label: '求购', type: 'beg' },
  { label: '委托', type: 'entrust' },
  { label: '出售', type: 'sale' },
]
const pills = [
  { label: '全部', status: 0 }, { label: '已完成', status: 1 },
  { label: '批量付', status: 0 }, { label: '待支付', status: 0 },
  { label: '进行中', status: 2 }, { label: '已取消', status: 3 },
]
const mainIdx = ref(0)
const pillIdx = ref(0)
const list = ref([])

if (isDemo()) list.value = REAL_ORDERS
else load()

async function load() {
  try {
    const res = await http.post('/api/order/order/orderList', { page: 1, status: pills[pillIdx.value].status })
    const rows = res.data?.data || []
    list.value = rows.map((o) => ({ ...o, create_text: fmt(o.createtime), local_image: localImg(o.goods_cover_image) }))
  } catch (e) { list.value = [] }
}

function fmt(ts) {
  const d = new Date((Number(ts) || 0) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}
function localImg(path) {
  if (!path) return ''
  if (path.includes('4ba378fee1beb93954cd5401ee9385b6')) return new URL('../../assets/images/order-goods-tuer.jpg', import.meta.url).href
  if (path.includes('88748beb27fa6100fa3346938cb8edd0')) return new URL('../../assets/images/order-goods-dengjipai.jpg', import.meta.url).href
  if (path.includes('27a8d4c90fbc3dc59b26092839f0a680')) return new URL('../../assets/images/order-goods-chaoku.jpg', import.meta.url).href
  return path
}

function onMain(i, t) {
  mainIdx.value = i
  if (t.type === 'beg') router.push('/pages/order/beg/list')
  else if (t.type === 'entrust') router.push('/pages/order/entrust/list')
  else if (t.type === 'sale') { list.value = []; return }
  else { pillIdx.value = 0; load() }
}
function onPill(i) { pillIdx.value = i; if (!isDemo()) load() }
function toDetail(o) { router.push(`/pages/order/defaultDetail?id=${o.id}&orderType=${o.order_type}`) }
function copy(txt) { if (navigator.clipboard) navigator.clipboard.writeText(txt).catch(() => {}) }
</script>

<style scoped>
.list-page { background: #F7F9FC; }
.ord-top-tabs .nav-extra { position: absolute; right: 16px; top: 11px; }
.ord-list { padding: 0 23px 0 17px; }
.ord-card { background: #FFFFFF; border-radius: 12px; padding: 12px; margin-bottom: 10px; }
.oc-head { display: flex; align-items: center; height: 14px; }
.oc-sn { flex: 1; min-width: 0; font-size: 10px; color: #9AA0AB; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.oc-copy { width: 12px; height: 12px; margin-left: 4px; cursor: pointer; }
.oc-time { font-size: 10px; color: #9AA0AB; margin-left: 8px; white-space: nowrap; }
.oc-body { display: flex; margin-top: 10px; height: 54px; }
.oc-img { width: 54px; height: 54px; border-radius: 6px; background: #F5F5F5; object-fit: cover; display: block; }
.oc-info { flex: 1; min-width: 0; padding-left: 8px; }
.oc-name { font-size: 14px; font-weight: 500; color: #212121; line-height: 20px; }
.oc-line { font-size: 10px; color: #9AA0AB; line-height: 14px; }
.oc-right { width: 65px; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; }
.oc-status { font-size: 11px; color: #00B578; white-space: nowrap; }
.oc-num { font-size: 11px; color: #9AA0AB; }
.oc-foot { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; height: 20px; }
.oc-more { font-size: 11px; color: #9AA0AB; display: flex; align-items: center; cursor: pointer; }
.oc-more img { width: 10px; height: 10px; margin-left: 2px; }
.oc-pay { font-size: 11px; color: #212121; }
.oc-pay b { font-weight: 400; }
.oc-pay strong { font-size: 18px; font-weight: 400; margin-left: 1px; }
.empty-pos { position: absolute; top: 260px; left: 0; }
</style>
