<template>
  <div class="b3-page b3-enable-page">
    <div class="b3-natnavbar">
      <img class="b3-natback" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-nattitle">赋能中心</div>
    </div>
    <!-- 原站本页除导航外无任何内容，空页面为真实态，如实留白 -->
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-enable-page { --b3-page-bg: #fff; }
.b3-natnavbar { position: relative; width: 375px; height: 56px; background: #fff; }
.b3-natback { position: absolute; left: 5px; top: 21px; width: 9px; height: 15px; }
.b3-nattitle { position: absolute; left: 155px; top: 13px; font-size: 16px; font-weight: 700; line-height: 17px; color: #000; }
</style>
