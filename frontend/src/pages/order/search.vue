<template>
  <!-- P064 订单搜索：返回 + 灰底搜索框 + 四主Tab + 结果/空区 -->
  <div class="ord-page se-page">
    <div class="ord-nav se-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <div class="se-box">
        <input v-model="keyword" type="text" placeholder="请输入订单号或商品名" @keyup.enter="doSearch" />
        <img v-if="keyword" class="se-clear" src="../../assets/images/order-clear.png" alt="清除" @click="keyword = ''" />
      </div>
    </div>
    <div class="se-tabs">
      <div v-for="(t, i) in tabs" :key="i" class="se-tab" :class="{ on: idx === i }" @click="idx = i">
        {{ t }}<i v-if="idx === i"></i>
      </div>
    </div>
    <div v-if="results.length" class="se-results">
      <div v-for="o in results" :key="o.id" class="ord-panel se-card" @click="toDetail(o)">
        <img class="se-img" :src="o.local_image || o.goods_cover_image" alt="" />
        <div class="se-info">
          <div class="se-name">{{ o.goods_name }}</div>
          <div class="se-sn">{{ o.order_sn }}</div>
        </div>
        <div class="se-price">¥{{ o.total_price }}</div>
      </div>
      <div class="ord-nomore">已经到底了~</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './common.css'

const router = useRouter()
const tabs = ['常规', '求购', '委托', '出售']
const idx = ref(0)
const keyword = ref('')
const results = ref([])

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
async function doSearch() {
  const kw = keyword.value.trim()
  if (!kw) { results.value = []; return }
  try {
    const res = await http.post('/api/order/order/orderList', { page: 1, status: 0 })
    const rows = res.data?.data || []
    results.value = rows.filter((o) => (o.order_sn || '').includes(kw) || (o.goods_name || '').includes(kw))
  } catch (e) { results.value = [] }
}
function toDetail(o) { router.push(`/pages/order/defaultDetail?id=${o.id}&orderType=${o.order_type}`) }
</script>

<style scoped>
.se-page { background: #F7F9FC; }
.se-nav { padding: 0 16px; }
.se-box {
  flex: 1; margin-left: 34px; height: 36px; border-radius: 18px; background: #F1F1F1;
  display: flex; align-items: center; padding: 0 16px;
}
.se-box input { flex: 1; min-width: 0; border: none; outline: none; background: transparent; font-size: 14px; color: #212121; }
.se-box input::placeholder { color: #808080; }
.se-clear { width: 14px; height: 14px; cursor: pointer; }
.se-tabs { display: flex; gap: 28px; padding: 16px 0 0 82px; background: #FFFFFF; }
.se-tab { position: relative; font-size: 14px; color: #9AA0AB; padding-bottom: 10px; }
.se-tab.on { font-size: 16px; font-weight: 700; color: #212121; }
.se-tab i { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 18px; height: 3px; border-radius: 2px; background: #D93437; }
.se-results { padding: 14px 17px; }
.se-card { display: flex; align-items: center; padding: 12px; margin-bottom: 10px; }
.se-img { width: 54px; height: 54px; border-radius: 6px; object-fit: cover; background: #F5F5F5; }
.se-info { flex: 1; min-width: 0; padding-left: 10px; }
.se-name { font-size: 14px; color: #212121; }
.se-sn { font-size: 10px; color: #9AA0AB; margin-top: 4px; }
.se-price { font-size: 14px; color: #212121; }
</style>
