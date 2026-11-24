<template>
  <!-- P028 赠送记录（原站 #/pages/market/giftRecord，无参数直访真实态）
       侦查实测：白底空页；navbar「< 赠送记录」；其下仅一条浅灰占位条
       (x16 y44 w343 h24 br8，tradingInformation 返回 list:[] 空态)，无其他内容。 -->
  <div class="b4-gr-page">
    <img class="b4-gr-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-gr-title">赠送记录</div>
    <div class="b4-gr-bar"></div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.b4-gr-page {
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: #fff; overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-gr-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; }
.b4-gr-title {
  position: absolute; left: 0; right: 0; top: 11px; height: 23px; text-align: center;
  font-size: 17px; font-weight: 700; line-height: 23px; color: #000;
}
.b4-gr-bar {
  position: absolute; left: 16px; top: 44px; width: 343px; height: 24px;
  border-radius: 8px; background: rgb(244, 246, 250);
}
</style>
