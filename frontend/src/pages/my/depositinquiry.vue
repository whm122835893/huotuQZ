<template>
  <div class="b3-page b3-deposit-page">
    <div class="b3-natnavbar">
      <img class="b3-natback" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-nattitle">存证查询</div>
    </div>
    <div class="dp-card">
      <div class="dp-label dp-label1">请输入区块链信息</div>
      <div class="dp-area dp-area1"><span class="dp-ph">可输入合约地址</span></div>
      <div class="dp-label dp-label2">查询结果</div>
      <div class="dp-area dp-area2"><span class="dp-ph">这里显示查询结果</span></div>
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
.b3-deposit-page { --b3-page-bg: rgb(244,246,250); }
/* 原生 nav：标题 16px @155,13，返回箭头仿原生左位 */
.b3-natnavbar { position: relative; width: 375px; height: 56px; background: rgb(244,246,250); }
.b3-natback { position: absolute; left: 5px; top: 21px; width: 9px; height: 15px; }
.b3-nattitle { position: absolute; left: 155px; top: 13px; font-size: 16px; font-weight: 700; line-height: 17px; color: #000; }
.dp-card {
  position: absolute; left: 12px; top: 56px; width: 351px; min-height: 400px;
  background: #fff; border-radius: 8px; box-sizing: border-box;
}
.dp-label { position: absolute; left: 12px; top: 17px; font-size: 15px; font-weight: 400; line-height: 16px; color: var(--b3-gray-74); }
.dp-label2 { top: 139px; }
.dp-area {
  position: absolute; left: 12px; width: 327px; height: 80px;
  background: #fff; border: 1px solid rgb(220,220,220); border-radius: 2px; box-sizing: border-box;
}
.dp-area1 { top: 44px; }
.dp-area2 { top: 166px; }
.dp-ph { position: absolute; left: 5px; top: 6px; font-size: 7px; font-weight: 400; line-height: 8px; color: var(--b3-gray-192); }
</style>
