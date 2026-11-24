<template>
  <div class="b3-page b3-getcode-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">修改操作密码</div>
    </div>
    <div class="gc-hint">输入当前操作密码</div>
    <div class="gc-boxes">
      <span v-for="i in 6" :key="i" class="gc-box"></span>
    </div>
    <div class="gc-forget" @click="toast('忘记密码')">忘记密码？</div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function toast() {}
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-getcode-page { --b3-page-bg: #fff; }
.gc-hint {
  position: absolute; left: 132px; top: 132px;
  font-size: 14px; font-weight: 400; line-height: 15px; color: var(--b3-gray-74);
}
/* 6 个输入框：原站白底 42×42 br8，在白页上边框极淡截图不可见 → 隐形盒保留占位 */
.gc-boxes { position: absolute; left: 37px; top: 169px; display: flex; gap: 10px; }
.gc-box { width: 42px; height: 42px; background: #fff; border-radius: 8px; box-sizing: border-box; }
.gc-forget {
  position: absolute; left: 163px; top: 241px;
  font-size: 10px; font-weight: 400; line-height: 11px; color: var(--b3-gray-85);
}
</style>
