<template>
  <!-- P079 /pages/product/notice 相关公告：无 keyword 不发请求（原站纯 navbar） -->
  <div class="pd-page pd-notice-page">
    <div class="pd-notice-nav">
      <img class="pd-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="pd-notice-title-bar"><span>相关公告</span></div>
    </div>

    <div v-if="!keyword"></div>

    <div v-else-if="loaded && list.length" class="pd-notice-list">
      <div v-for="it in list" :key="it.id" class="pd-notice-card">
        <div class="pd-notice-card-title"><span>{{ it.title }}</span></div>
        <div class="pd-notice-card-date"><span>{{ fmtDate(it.createtime) }}</span></div>
      </div>
    </div>

    <div v-else-if="loaded" class="pd-notice-empty">
      <img :src="IMG.empty" alt="" />
      <span>目前没有任何数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, routeQuery, fmtDate } from './demo'
import './common.css'

const router = useRouter()
const keyword = (routeQuery().get('keyword') || '').trim()
const list = ref([])
const loaded = ref(false)

function goBack() { router.back() }

onMounted(async () => {
  if (!keyword) return
  try {
    const res = await http.post('/api/product/relatedNotice', { search: keyword, page: 1 })
    if (res && res.code === 1 && res.data) {
      list.value = Array.isArray(res.data.data) ? res.data.data : []
    }
  } catch (e) { /* 保持空态 */ }
  loaded.value = true
})
</script>
