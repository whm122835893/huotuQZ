<template>
  <div class="d20-page d98-page">
    <div class="d20-navbar">
      <span class="d20-back" @click="goBack">{{ '\ue601' }}</span>
      <div class="d20-title">参与人数</div>
    </div>

    <!-- 黑卡全宽 @0,16 375×57：居中「参与人数{0}人」，数字 666 加粗 -->
    <div class="d98-card d20-card-dark">
      <div class="d98-count">参与人数<span class="d98-count-num">{{ joinCount }}</span>人</div>
    </div>

    <div class="d98-gap"></div>

    <!-- 底部固定栏 375×91 白底：按钮 360×46 @8,15 -->
    <div class="d98-footer">
      <div class="d20-btn-dark d98-invite-btn" @click="noop">邀请好友助力</div>
    </div>
  </div>
</template>

<script setup>
/* P098 参与人数（原站 #/pages/draw/Leaderboard/Leaderboard）
   原站 chunk pages-draw-Leaderboard-Leaderboard.726db9b5.js：
   参与人数来自页面参数，直接访问恒 0（侦查实测 2026-11-16）；
   底部「邀请好友助力」按钮无参数态点击无可见反应（路由跳转依赖活动参数）。 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import './common.css'

const joinCount = ref(0)
const router = useRouter()
function goBack() { router.back() }
function noop() {}
</script>

<style scoped>
.d98-page { background: var(--d20-bg); min-height: 100vh; position: relative; }
/* 黑卡全宽 375×57 padding 12px 20px（原站实测），br20 圆角 */
.d98-card {
  margin: 16px 0 0;
  height: 57px;
  padding: 12px 20px;
  display: flex; align-items: center; justify-content: center;
}
.d98-count { font-size: 14px; color: var(--d20-gray6); padding-top: 5px; }
.d98-count-num { font-weight: 700; margin: 0 6px; }
.d98-gap { height: 121px; }
/* 底部固定栏 375×91 白底：按钮 360×46 @8 顶 15 底 30 */
.d98-footer {
  position: fixed; left: 0; right: 0; bottom: 0;
  height: 91px;
  background: var(--d20-white);
  padding: 15px 7px 30px 8px;
  box-sizing: border-box;
}
.d98-invite-btn { width: 360px; }
</style>
