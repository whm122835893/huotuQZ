<template>
  <div class="rr-page rg-page">
    <div class="rr-navbar">
      <img class="rg-nav-back" :src="IMG.back" alt="" @click="goBack" />
      <div class="rg-nav-title">转赠记录</div>
    </div>
    <div class="rr-tabs">
      <div class="rr-tab" :class="{ on: types === 1 }" @click="switchTab(1)">我转赠的</div>
      <div class="rr-tab" :class="{ on: types === 2 }" @click="switchTab(2)">我收到的</div>
    </div>
    <div v-if="!list.length" class="rr-empty">
      <img :src="IMG.empty" alt="" />
      <p>目前没有任何数据</p>
    </div>
    <div v-else>
      <div v-for="it in list" :key="it.id" class="rr-item">
        <div class="rr-item-top">
          <img class="rr-item-img" :src="localImg(it.product.listimg)" alt="" />
          <div class="rr-item-name">{{ it.product.name }}</div>
        </div>
        <div class="rr-item-line">
          <span class="rr-item-key">{{ types === 1 ? '转赠时间' : '转入时间' }}：</span>
          <span>{{ fmtDate(it.createtime) }}</span>
        </div>
        <div class="rr-item-line">
          <span class="rr-item-key">{{ types === 1 ? '接收人' : '转赠人' }}：</span>
          <span>{{ types === 1 ? it.user.mobile : it.from_user.mobile }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, localImg, fmtDate, routeQuery } from './demo'
import './common.css'

const router = useRouter()
const q = routeQuery()
const types = ref(Number(q.get('types')) === 2 ? 2 : 1)
const list = ref([])

function goBack() { router.back() }

async function load() {
  try {
    const res = await http.post('/api/user/aboutGift', { page: 1, types: types.value })
    if (res && res.code === 1 && res.data) list.value = res.data.data || []
    else list.value = []
  } catch (e) { list.value = [] }
}

function switchTab(t) {
  if (types.value === t) return
  types.value = t
  list.value = []
  load()
}

onMounted(load)
</script>
