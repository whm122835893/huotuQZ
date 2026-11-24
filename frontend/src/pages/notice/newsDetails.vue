<template>
  <!-- P037 资讯详情(新闻发声)：布局与公告详情同构，导航标题「新闻发声」，接口 /discover/ant_news/info -->
  <div class="detail-page">
    <!-- 原站行为：资讯不存在时整页白屏（导航栏随数据一起渲染） -->
    <template v-if="info.title">
    <div class="nav">
      <img class="back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">新闻发声</span>
    </div>

    <div v-if="info.title" class="body">
      <h1 class="title">{{ info.title }}</h1>

      <img v-if="bannerSrc" class="banner" :src="bannerSrc" alt="" />

      <div class="time">{{ fmtTime(info.publish_time) }}</div>

      <!-- 富文本正文（p/span #242424） -->
      <div class="rich" v-html="content"></div>
    </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import http from '../../api/request'

const info = ref({})
const category = ref('')
const imageParam = ref('')

const content = computed(() => info.value.content || '')
const bannerSrc = computed(() => {
  const img = imageParam.value || info.value.image || ''
  return img || ''
})

function fmtTime(ts) {
  if (!ts) return ''
  const d = new Date(Number(ts) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}

function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = '#/pages/notice/notice'
}

async function load(id) {
  const res = await http.post('/api/discover/ant_news/info', { id })
  info.value = res.data || {}
}

{
  const q = new URLSearchParams(window.location.hash.split('?')[1] || '')
  category.value = q.get('category') || ''
  imageParam.value = q.get('image') || ''
  load(q.get('id') || 0)
}
</script>

<style scoped>
.detail-page {
  min-height: 100vh;
  background: #fff;
}

.nav {
  position: sticky;
  top: 0;
  z-index: 5;
  height: 44px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}
.back {
  position: absolute;
  left: 19px;
  top: 50%;
  transform: translateY(-50%);
  width: 7px;
  height: 13px;
  cursor: pointer;
}
.nav-title {
  font-size: 17px;
  line-height: 23px;
  color: #000;
}

.body {
  padding: 0 16px;
}

.title {
  margin: 18px 0 0;
  font-family: 'SourceHanSansCN-Medium', 'Alibaba-PuHuiTi', sans-serif;
  font-size: 18px;
  line-height: 26px;
  font-weight: 500;
  color: #161616;
}

.banner {
  display: block;
  margin-top: 12px;
  width: 343px;
  border-radius: 8px;
}

.time {
  margin-top: 12px;
  padding-bottom: 11px;
  font-family: 'SourceHanSansCN', sans-serif;
  font-size: 10px;
  line-height: 22px;
  font-weight: 300;
  color: #847765;
}

.rich {
  margin-top: 20px;
  font-size: 14px;
  font-weight: 400;
  color: #333;
  line-height: 26px;
  letter-spacing: 1px;
  word-break: break-all;
}
.rich :deep(p) {
  margin: 0;
  color: #242424;
}
.rich :deep(span) {
  color: #242424;
}
.rich :deep(img) {
  max-width: 100%;
}
</style>
