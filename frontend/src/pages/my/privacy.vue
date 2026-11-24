<template>
  <div class="b3-page b3-privacy-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">隐私设置</div>
    </div>
    <div class="pv-card">
      <span class="pv-label">对外展示排行信息</span>
      <span class="pv-switch"><span class="pv-dot"></span></span>
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
.b3-privacy-page { --b3-page-bg: rgb(247,247,247); }
.b3-privacy-page .b3-navbar { background: rgb(247,247,247); }
.pv-card {
  position: absolute; left: 20px; top: 56px; width: 335px; height: 56px;
  background: #fff; border-radius: 12px; box-sizing: border-box;
}
.pv-label { position: absolute; left: 12px; top: 18px; font-size: 14px; font-weight: 400; line-height: 20px; color: rgb(33,33,33); }
.pv-switch { position: absolute; left: 281px; top: 17px; width: 42px; height: 22px; background: rgb(51,51,51); border-radius: 50px; }
.pv-dot { position: absolute; right: 2px; top: 2px; width: 18px; height: 18px; background: #fff; border-radius: 50%; }
</style>
