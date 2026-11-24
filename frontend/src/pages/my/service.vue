<template>
  <div class="b3-page b3-service-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">客服</div>
    </div>
    <div class="sv-row">
      <span class="sv-text">客服电话：待定</span>
      <svg class="sv-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
      </svg>
    </div>
    <div class="sv-row sv-row2">
      <span class="sv-text">客服邮箱：待定</span>
      <svg class="sv-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/>
      </svg>
    </div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-service-page { --b3-page-bg: rgb(245,245,245); }
.b3-service-page .b3-navbar { background: rgb(245,245,245); }
.sv-row { position: absolute; left: 16px; top: 64px; height: 24px; }
.sv-row2 { top: 118px; }
.sv-text { font-size: 15px; font-weight: 400; line-height: 16px; color: #000; }
.sv-ic { position: absolute; left: 109px; top: -4px; width: 20px; height: 21px; color: rgb(42,81,239); }
</style>
