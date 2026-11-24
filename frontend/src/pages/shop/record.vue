<template>
  <!-- P052 兑换记录：导航44 / 白卡列表(80x80图) / 空态142 @ y=260 -->
  <div class="shop-page record-page">
    <div class="shop-nav">
      <img class="nav-back is-dark" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">兑换记录</span>
    </div>

    <div v-if="list.length" class="rec-list">
      <div v-for="r in list" :key="r.id" class="rec-card">
        <img class="rec-img" :src="r.list_image" alt="" />
        <div class="rec-info">
          <div class="rec-name">{{ r.goods_name }}</div>
          <div class="rec-num">{{ r.crystal_number }}</div>
          <div class="rec-time">{{ r.create_time }}</div>
        </div>
        <img class="rec-arrow" src="../../assets/images/shop-arrow-right.png" alt="" />
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_RECORD } from './demo'
import './common.css'

const router = useRouter()
const list = ref([])

if (isDemo()) list.value = DEMO_RECORD
else load()

async function load() {
  try {
    const res = await http.post('/api/crystal_goods/exchangeRecord', { page: 1 })
    list.value = res.data?.data || []
  } catch (e) { list.value = [] }
}

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.record-page { background: #F4F6FA; }
.rec-list { padding: 16px; }
.rec-card {
  display: flex; align-items: center;
  background: #FFFFFF; border-radius: 12px; padding: 12px; margin-bottom: 12px;
}
.rec-img { width: 80px; height: 80px; border-radius: 8px; background: #F5F5F5; object-fit: cover; display: block; }
.rec-info { flex: 1; min-width: 0; padding-left: 12px; }
.rec-name { font-size: 16px; color: #212121; }
.rec-num { margin-top: 8px; font-size: 14px; color: #212121; }
.rec-time { margin-top: 6px; font-size: 11px; color: #999999; }
.rec-arrow { width: 8px; height: 14px; margin-right: 4px; }
.empty-pos { position: absolute; top: 260px; left: 0; }
</style>
