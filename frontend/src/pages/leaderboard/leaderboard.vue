<template>
  <!-- P107 排行榜（原站 #/pages/leaderboard/leaderboard，无参数直访真实态）
       侦查实测：newrank1 返回 code0「名人堂未开启」，整页为 rank_bg.png 背景
       （含麦穗/今日热榜/POPULAR LIST/墨绿渐变）+ 空盒子图 +「目前没有任何数据」。
       背景深灰 rgb(68,68,68)。左上圆形返回键。 -->
  <div class="b25-lb-page">
    <img class="b25-lb-bg" :src="IMG.bg" alt="" />
    <div class="b25-lb-back-circle" @click="goBack">
      <img class="b25-lb-back" :src="IMG.back" alt="返回" />
    </div>
    <img class="b25-lb-empty" :src="IMG.empty" alt="空数据" />
    <div class="b25-lb-empty-text">目前没有任何数据</div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import './leaderboard.css'
const IMG = {
  bg: new URL('../../assets/images/b25-rank-bg.png', import.meta.url).href,
  back: new URL('../../assets/images/b25-back-white.png', import.meta.url).href,
  empty: new URL('../../assets/images/b25-empty-data.png', import.meta.url).href,
}
const router = useRouter()
function goBack() { router.back() }
</script>
