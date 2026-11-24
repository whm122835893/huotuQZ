<template>
  <div class="b3-page b3-flow-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">流转记录</div>
    </div>
    <img class="fl-empty" :src="IMG.empty" alt="" />
    <div class="fl-empty-tip">目前没有任何数据</div>
    <!-- 原站 getRecord 接口无参数直访返回「参数错误」→ 弹出 toast，如实复刻 -->
    <div class="fl-toast">参数错误</div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
import empty from '../../assets/images/b3-empty-data.png'
const IMG = { back, empty }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-flow-page { --b3-page-bg: #fff; }
.fl-empty { position: absolute; left: 117px; top: 144px; width: 142px; height: 142px; }
.fl-empty-tip { position: absolute; left: 135px; top: 297px; font-size: 13px; font-weight: 400; line-height: 15px; color: var(--b3-gray-153); }
.fl-toast {
  position: absolute; left: 141px; top: 387px; width: 92px; min-height: 39px;
  background: rgba(17,17,17,0.7); border-radius: 5px; box-sizing: border-box;
  font-size: 13px; font-weight: 400; line-height: 18px; text-align: center; color: #fff;
  padding: 10px 0;
}
</style>
