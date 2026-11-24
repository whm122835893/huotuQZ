<template>
  <div class="b3-page b3-info-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">个人信息</div>
    </div>
    <div class="if-row if-row1">
      <span class="if-label">头像</span>
      <span class="if-avatar"></span>
    </div>
    <div class="if-row if-row2">
      <span class="if-label">昵称</span>
      <span class="if-value if-value2">175****1293</span>
    </div>
    <div class="if-row if-row3">
      <span class="if-label">手机号</span>
      <span class="if-value if-value3">175****1293</span>
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
.b3-info-page { --b3-page-bg: rgb(244,246,250); }
.if-row { position: absolute; left: 0; width: 375px; background: #fff; box-sizing: border-box; }
.if-row1 { top: 59px; height: 61px; }
.if-row2 { top: 120px; height: 53px; border-top: 1px solid rgb(240,240,240); }
.if-row3 { top: 173px; height: 53px; border-top: 1px solid rgb(240,240,240); }
.if-label { position: absolute; left: 16px; top: 25px; font-size: 14px; font-weight: 400; line-height: 15px; color: #000; }
.if-row1 .if-label { top: 28px; }
.if-row3 .if-label { top: 25px; }
.if-value { position: absolute; right: 28px; top: 21px; font-size: 12px; font-weight: 400; line-height: 14px; color: #000; }
.if-value2 { top: 21px; }
.if-value3 { top: 22px; }
/* 头像图 404 → 灰底空块 @299,75 60×29，如实复刻 */
.if-avatar { position: absolute; right: 16px; top: 16px; width: 60px; height: 29px; background: rgb(245,245,245); }
</style>
