<template>
  <div class="activity-page d21--record">
    <div class="d21-navbar">
      <img class="d21-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="d21-title">中签记录</div>
    </div>

    <div class="d21-record-strip"></div>

    <img class="d21-record-empty-img" :src="IMG.empty" alt="暂无数据" />
    <div class="d21-record-empty-text">目前没有任何数据</div>
  </div>
</template>

<script setup>
/* P101 中签记录（活动域，原站 #/pages/activity/record）
   原站 chunk pages-activity-record.92abb2be：onLoad → POST /api/draw/drawLog
   → 返回 data:[] → 空态。navbar u-navbar 44px 白底；返回箭头 back_btn.png 7×13 @(19,16)；
   标题「中签记录」17px/700 黑居中 @y11 h23；navbar 后透明空条带 .br-12.pa-16 @(16,44) 343×32
   （draw_notice 为空残留，无可见内容）；空盒图 b19 同源 empty-data.png 142×142 @(116.5,276)；
   提示「目前没有任何数据」13px 灰色 @(135,429)。整页纯白，无 TabBar。
   图片与批次19 中奖记录页逐像素同图，复用 b19 资源不重复下载。 */
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './common.css'

const IMG = {
  back: new URL('../../assets/images/b19-back-btn.png', import.meta.url).href,
  empty: new URL('../../assets/images/b19-empty-data.png', import.meta.url).href
}

const router = useRouter()
function goBack() { router.back() }

onMounted(async () => {
  // data:[] 空态如实保持，不臆造中签数据
  try { await http.post('/api/draw/drawLog', {}) } catch (e) { /* 空态如实保持 */ }
})
</script>
