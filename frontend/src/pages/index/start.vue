<template>
  <!-- P111 启动页（原站 #/pages/index/start，无参数直访真实态）
       侦查实测：整页 start-bg.png 启动图（含「赴火兔之约 藏数字山河」艺术字 + 底部火兔 logo），
       右上「跳过」（14px/400/rgb(33,33,33) at 323,12）；body 透明。 -->
  <div class="b28-st-page">
    <img class="b28-st-bg" :src="bg" alt="启动页" />
    <div class="b28-st-skip" @click="goHome">跳过</div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import './start.css'
const bg = new URL('../../assets/images/b25-start-bg.png', import.meta.url).href
const router = useRouter()
function goHome() { router.replace('/') }
</script>
