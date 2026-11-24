<template>
  <div class="b3-page b3-addrlst-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">收货地址</div>
    </div>
    <img class="al-empty" :src="IMG.empty" alt="" />
    <div class="al-add" @click="goAdd">去添加收货地址</div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
import empty from '../../assets/images/b3-empty-data.png'
const IMG = { back, empty }
function goBack() { history.back() }
function goAdd() { location.hash = '#/pages/my/address/edit' }
</script>

<style scoped>
@import './b3-common.css';
.b3-addrlst-page { --b3-page-bg: rgb(244,246,250); }
.b3-addrlst-page .b3-navbar { background: rgb(244,246,250); }
.al-empty { position: absolute; left: 117px; top: 180px; width: 142px; height: 142px; }
.al-add {
  position: absolute; left: 122px; top: 330px; width: 132px; height: 33px;
  background: var(--b3-btn-dark); border-radius: 12px;
  font-size: 12px; font-weight: 600; line-height: 33px; text-align: center; color: #fff;
}
</style>
