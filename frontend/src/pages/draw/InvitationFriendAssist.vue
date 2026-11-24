<template>
  <div class="d20-page d97-page">
    <div class="d20-navbar">
      <span class="d20-back" @click="goBack">{{ '\ue601' }}</span>
      <div class="d20-title">邀请好友助力</div>
    </div>

    <!-- 邀请按钮 @16,26 343×46 rgb(49,49,49) br12 -->
    <div class="d20-btn-dark d97-invite-btn" @click="noop">邀请</div>

    <!-- 黑卡 @16,87 343×51：居中计数 14px -->
    <div class="d97-card d20-card-dark">
      <div class="d97-count">您已成功获得助力<span class="d97-count-num">{{ assistCount }}</span>次</div>
    </div>

    <!-- 表格容器 @16,138 343×187 pa-16：表头四列 + 空态 -->
    <div class="d97-table">
      <div class="d97-table-head">
        <div class="d97-th d97-th1">日期</div>
        <div class="d97-th d97-th2">用户类型</div>
        <div class="d97-th d97-th3">为您助力用户</div>
        <div class="d97-th d97-th1">结果</div>
      </div>
      <div class="d97-empty">
        <span class="d20-empty-icon">{{ '\ue62f' }}</span>
        <div class="d20-empty-text">数据为空</div>
      </div>
    </div>

    <!-- 规则卡 y=330 343×66：图标占位 15×15 + 助力规则 16px/700 -->
    <div class="d97-rule-card d20-card-dark">
      <span class="d97-rule-icon"></span>
      <div class="d97-rule-title">助力规则</div>
    </div>
  </div>
</template>

<script setup>
/* P097 邀请好友助力（原站 #/pages/draw/InvitationFriendAssist/InvitationFriendAssist）
   原站 chunk pages-draw-InvitationFriendAssist-InvitationFriendAssist.10a01bda.js：
   助力次数/列表来自页面参数，直接访问恒 0 + 空态（侦查实测 2026-11-16）；
   「邀请」按钮无参数态点击无可见反应（分享流依赖 App 环境与分享码）；
   空态图标 uicon \ue62f 90px，如实复刻。 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import './common.css'

const assistCount = ref(0)
const router = useRouter()
function goBack() { router.back() }
function noop() {}
</script>

<style scoped>
.d97-page { background: var(--d20-bg); min-height: 100vh; }
.d97-invite-btn { margin: 26px 16px 0; }
/* 黑卡 @16,87 343×51：内容垂直居中 */
.d97-card {
  margin: 15px 16px 0;
  height: 51px;
  display: flex; align-items: center; justify-content: center;
}
.d97-count { font-size: 14px; color: var(--d20-gray6); padding-top: 5px; }
.d97-count-num { color: var(--d20-theme); margin: 0 6px; }
/* 表格容器 @16,138 343×187 padding16：表头四列 62/78/109/62 无间隙 */
/* 表格容器 @16,138 343×187 padding16（紧贴黑卡底 138，无上边距） */
.d97-table {
  margin: 0 16px;
  padding: 16px;
  box-sizing: border-box;
  width: 343px;
  height: 187px;
}
.d97-table-head { display: flex; justify-content: space-between; }
.d97-th { font-size: 12px; line-height: 18px; text-align: center; color: var(--d20-gray-head); }
.d97-th1 { width: 62px; }
.d97-th2 { width: 78px; }
.d97-th3 { width: 109px; }
/* 空态：表头后 mb16，高 121 */
.d97-empty {
  margin-top: 16px;
  height: 121px;
  display: flex; flex-direction: column; align-items: center;
}
/* 规则卡：容器底 369，卡 y374 -> 间距 5px */
.d97-rule-card { margin: 5px 16px 0; height: 66px; display: flex; align-items: center; }
.d97-rule-icon { width: 15px; height: 15px; flex-shrink: 0; }  /* 原站 404 破图占位 */
.d97-rule-title { margin-left: 10px; font-size: 16px; font-weight: 700; color: var(--d20-title-gray); }
</style>
