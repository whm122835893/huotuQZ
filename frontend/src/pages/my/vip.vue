<template>
  <div class="b3-page b3-vip-page">
    <div class="b3-vip-navbar">
      <img class="b3-vip-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-vip-title">VIP权益中心</div>
    </div>
    <!-- 全部图像为原站 static/vip 图本地化（2x，按显示尺寸缩放）；权益卡文字均在 icons.png 内 -->
    <img class="vip-logo" :src="IMG.logo" alt="" />
    <img class="vip-title-img" :src="IMG.title" alt="" />
    <img class="vip-title2-img" :src="IMG.title2" alt="" />
    <img class="vip-icons" :src="IMG.icons" alt="" />
    <img class="vip-btn" :src="IMG.btn" alt="" />
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
import logo from '../../assets/images/b3-vip-logo-white.png'
import title from '../../assets/images/b3-vip-title.png'
import title2 from '../../assets/images/b3-vip-title2.png'
import icons from '../../assets/images/b3-vip-icons.png'
import btn from '../../assets/images/b3-vip-btn.png'
const IMG = { back, logo, title, title2, icons, btn }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-vip-page { --b3-page-bg: #000; background: #000; }
/* 原生 nav：标题白字 17px @141,11，返回箭头左位 */
.b3-vip-navbar { position: relative; width: 375px; height: 44px; background: transparent; }
.b3-vip-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; }
.b3-vip-title { position: absolute; left: 141px; top: 11px; font-size: 17px; font-weight: 700; line-height: 23px; color: #fff; }
.vip-logo { position: absolute; left: 160px; top: 94px; width: 56px; height: 67px; }
.vip-title-img { position: absolute; left: 122px; top: 191px; width: 135px; height: 36px; }
.vip-title2-img { position: absolute; left: 109px; top: 231px; width: 157px; height: 24px; }
.vip-icons { position: absolute; left: 18px; top: 275px; width: 340px; height: 331px; }
.vip-btn { position: absolute; left: 13px; top: 739px; width: 350px; height: 61px; }
</style>
