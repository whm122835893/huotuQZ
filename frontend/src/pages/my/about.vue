<template>
  <div class="b3-page b3-about-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">关于我们</div>
    </div>
    <div class="ab-rate" @click="toast('去评分')">去评分</div>
    <div class="ab-label">关于我们：</div>
    <div class="ab-links">
      <span class="ab-link" @click="toast('用户服务协议')">《用户服务协议》</span>
      <span class="ab-link" @click="toast('隐私政策')">《隐私政策》</span>
    </div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function toast(msg) { /* 原站点击为页内响应，静态快照复刻不额外实现 */ }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-about-page { --b3-page-bg: #fff; }
.ab-rate {
  position: absolute; left: 29px; top: 433px;
  font-size: 15px; font-weight: 400; line-height: 16px; color: var(--b3-gray-85);
}
.ab-label {
  position: absolute; left: 29px; top: 492px;
  font-size: 15px; font-weight: 500; line-height: 16px; color: #000;
}
.ab-links { position: absolute; left: 0; top: 623px; width: 375px; }
.ab-link {
  font-size: 15px; font-weight: 400; line-height: 16px; color: var(--b3-gray-153);
}
.ab-link:first-child { position: absolute; left: 82px; }
.ab-link:last-child { position: absolute; left: 203px; }
</style>
