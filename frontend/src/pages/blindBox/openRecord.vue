<template>
  <!-- P074 开盒记录：/api/box/blind_box/openlog，原站该账号 total=0 恒空态 -->
  <div class="bb-page bbr-page">
    <div class="bb-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">开盒记录</span>
    </div>

    <div class="bbr-list">
      <div v-for="it in list" :key="it.id" class="bbr-item">
        <div class="bbr-left">
          <div class="bbr-nick">{{ it.userss?.nickname }}</div>
          <div class="bbr-time">{{ fmtDate(it.createtime) }}</div>
        </div>
        <div class="bbr-right">
          <span class="bbr-hit">抽中</span>
          <span class="bbr-name">{{ it.collections?.name }}</span>
        </div>
      </div>
    </div>

    <div v-if="!list.length" class="bb-empty bbr-empty">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, fmtDate, DEMO_OPEN_LOG } from './demo'
import './common.css'

const router = useRouter()
const boxId = Number(routeQuery().get('id') || 0)
const list = ref([])
function goBack() { router.back() }

async function load() {
  if (isDemo()) { list.value = DEMO_OPEN_LOG; return }
  try {
    const res = await http.post('/api/box/blind_box/openlog', { blind_box_id: boxId, page: 1, list_rows: 20 })
    if (res && res.code === 1 && res.data) list.value = res.data.data || []
  } catch (e) { list.value = [] }
}
load()
</script>

<style scoped>
.bbr-page { background: #F4F6FA; }
/* 原站 navbar 底色与页面同色 */
.bbr-page .bb-nav { background: #F4F6FA; }
.bbr-list { padding: 0 16px; }
.bbr-item {
  padding: 12px 0;
  display: flex; align-items: center; justify-content: space-between;
}
.bbr-nick { font-size: 14px; color: var(--bb-black); }
.bbr-time { margin-top: 8px; font-size: 12px; color: #9A9A9A; }
.bbr-right { font-size: 14px; }
.bbr-hit { color: #9A9A9A; margin-right: 4px; }
.bbr-name { color: var(--bb-black); }
/* 空态：142×142 箱子 @(117,60) + 文案 @y=213 */
.bbr-empty { padding-top: 16px; }
</style>
