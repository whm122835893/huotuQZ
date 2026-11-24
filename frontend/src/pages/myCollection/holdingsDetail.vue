<template>
  <!-- P066 持仓详情：藏品信息 + 编号实例卡（原站 getDetail 快照） -->
  <div class="mc-page hold-page">
    <div class="mc-nav hold-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <div class="nav-title">{{ coll.name }}</div>
    </div>

    <div class="hold-head">
      <img class="hh-img" :src="imgOf(coll)" alt="" />
      <div class="hh-info">
        <div class="hh-name">{{ coll.name }}</div>
        <div class="hh-stat">藏品总数 {{ total }}</div>
        <div class="hh-stat">寄售数量 {{ sellNum }}</div>
      </div>
      <div class="hh-market" @click="toMarket">查看寄售市场
        <img src="../../assets/images/icon-right.png" alt="" />
      </div>
    </div>

    <div class="hold-bar">
      <div class="hb-left">编号
        <img src="../../assets/images/market-sort.png" alt="" />
      </div>
      <div class="hb-right">
        <span class="hb-circle" :class="{ on: resaleFirst }" @click="resaleFirst = !resaleFirst"></span>
        <span @click="resaleFirst = !resaleFirst">寄售优先</span>
      </div>
    </div>

    <div v-if="items.length" class="hold-cards">
      <div v-for="it in items" :key="it.id" class="hc-card" @click="toDetail(it)">
        <div class="hc-code">#{{ it.collection_code }}</div>
        <div class="hc-price">购入价 <span>¥ {{ priceText(it.from_price) }}</span></div>
        <div class="hc-price">寄售价 <span>¥ {{ priceText(it.sale_price) }}</span></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, DEMO_COLLECTIONS, DEMO_ITEMS, DEMO_LIST, localImg } from './demo'
import './common.css'

const router = useRouter()
const route = useRoute()
const cid = Number(routeQuery().get('id') || route.query.id || 0)

const coll = ref({ name: '', main_image: '', total_num: 0 })
const items = ref([])
const total = ref(0)
const sellNum = ref(0)
const resaleFirst = ref(false)

const imgOf = (c) => localImg(c?.main_image || c?.listimg)
const priceText = (v) => (v === null || v === undefined || v === '' || v === 0) ? '--' : v

function goBack() { router.back() }
function toMarket() { /* 原站实测：停留原地并 toast「藏品不存在」，不跳转 */ }
function toDetail(it) { router.push(`/pages/myCollection/collectionDetails?id=${it.id}`) }

async function load() {
  if (isDemo()) {
    const c = DEMO_COLLECTIONS.find((x) => x.id === cid) || DEMO_COLLECTIONS[0]
    const holder = DEMO_LIST.find((x) => x.collection_id === cid)
    coll.value = c
    items.value = DEMO_ITEMS[cid] || []
    total.value = holder?.num ?? items.value.length
    sellNum.value = Number(holder?.sell_num ?? 0)
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/getDetail', { collection_id: String(cid), types: 2, page: 1 })
    const d = res.data || {}
    coll.value = d.collection || { name: '', main_image: '', total_num: 0 }
    items.value = d.list?.data || []
    total.value = d.list?.total ?? items.value.length
    sellNum.value = 0
  } catch (e) {
    coll.value = { name: '', main_image: '', total_num: 0 }
    items.value = []
  }
}

load()
</script>

<style scoped>
.hold-page { background: #F7F9FC; }
.hold-nav { background: #F7F9FC; }
/* 顶部藏品信息区：图 80×80 @(16,56) */
.hold-head { position: relative; height: 96px; padding: 12px 16px; }
.hh-img { position: absolute; left: 16px; top: 12px; width: 80px; height: 80px; border-radius: 8px; background: #F5F5F5; object-fit: cover; }
.hh-info { position: absolute; left: 108px; top: 20px; }
.hh-name { font-size: 16px; font-weight: 500; color: #212121; line-height: 22px; }
.hh-stat { font-size: 12px; color: #9AA0AB; line-height: 17px; margin-top: 4px; }
.hh-market {
  position: absolute; right: 16px; top: 44px;
  font-size: 11px; color: #9AA0AB; display: flex; align-items: center; cursor: pointer;
}
.hh-market img { width: 10px; height: 10px; margin-left: 2px; }

/* 编号 / 寄售优先 行 */
.hold-bar { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px 12px; }
.hb-left { font-size: 12px; color: #212121; display: flex; align-items: center; }
.hb-left img { width: 10px; height: 10px; margin-left: 3px; }
.hb-right { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #212121; cursor: pointer; }
.hb-circle { width: 16px; height: 16px; border: 1px solid #C8C9CB; border-radius: 50%; display: inline-block; }
.hb-circle.on { border-color: #D93437; background: #D93437; box-shadow: inset 0 0 0 3px #FFFFFF; }

/* 编号卡：白卡约 105×80，间隔 8，横向排列 */
.hold-cards { display: flex; flex-wrap: wrap; gap: 8px; padding: 0 16px; }
.hc-card {
  width: 105px; height: 80px; background: #FFFFFF; border-radius: 12px;
  padding: 12px 9px; cursor: pointer;
}
.hc-code { font-size: 16px; font-weight: 700; color: #212121; line-height: 22px; }
.hc-price { font-size: 10px; color: #9AA0AB; line-height: 14px; margin-top: 5px; }
.hc-price span { color: #212121; font-size: 12px; }
</style>
