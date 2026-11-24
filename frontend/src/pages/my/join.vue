<template>
  <div class="b3-page b3-join-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">加入社群</div>
    </div>
    <!-- 原站二维码图加载失败 → 193×193 灰底空块（loading_error），如实复刻不臆造二维码 -->
    <div class="jn-qr"></div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-join-page { --b3-page-bg: #fff; }
.jn-qr { position: absolute; left: 91px; top: 334px; width: 193px; height: 193px; background: rgb(245,245,245); }
</style>
