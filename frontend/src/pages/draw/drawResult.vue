<template>
  <div class="d20-page d96-page">
    <div class="d20-navbar">
      <span class="d20-back" @click="goBack">{{ '\ue601' }}</span>
      <div class="d20-title">中签结果</div>
    </div>

    <!-- u-loading-page：全屏透明覆盖（navbar 可见），wrapper 顶 y=348（整页坐标）。
         原站 chunk：onLoad -> JSON.parse(undefined) 抛异常 -> loading 恒 true，
         永久加载态是当前真实态，如实复刻，不臆造中签结果。 -->
    <div class="d96-loading-page">
      <div class="d96-wrapper">
        <div class="d96-icon-box">
          <span class="d96-spinner"></span>
        </div>
        <div class="d96-text">加载中...</div>
      </div>
    </div>
  </div>
</template>

<script setup>
/* P096 中签结果（原站 #/pages/draw/drawResult/drawResult）
   原站 chunk pages-draw-drawResult-drawResult.36af198a.js：
   onLoad 缺少页面参数（result JSON）时 JSON.parse(undefined) 直接抛异常，
   loading 恒为 true -> 永久 spinner + 「加载中...」（侦查实测 2026-11-16）。
   整页背景 rgb(245,245,245)，加载层透明覆盖全屏。 */
import { useRouter } from 'vue-router'
import './common.css'

const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.d96-page { background: var(--d20-bg); min-height: 100vh; position: relative; }
/* 加载层：全屏透明，不遮 navbar */
.d96-loading-page {
  position: fixed; left: 0; top: 0; right: 0; bottom: 0;
  background: transparent;
  pointer-events: none;
}
.d96-wrapper {
  position: absolute; left: 165px; top: 348px;
  width: 45px; height: 42px;
}
.d96-icon-box {
  width: 19px; height: 19px;
  margin: 0 auto;
  position: relative;
}
/* spinner 26×26 @(-3.5,-4) 溢出居中：圆环 3px 边框（底环为加载色 30% 透明变体），顶段深色旋转 */
.d96-spinner {
  position: absolute; left: -3.5px; top: -4px;
  width: 26px; height: 26px;
  border: 3px solid var(--d20-loading-ring);
  border-top-color: var(--d20-loading);
  border-radius: 50%;
  box-sizing: border-box;
  animation: d20-spin 0.8s linear infinite;
}
.d96-text {
  margin-top: 8px;
  width: 45px;
  font-size: 12px; line-height: 18px;
  text-align: center;
  white-space: nowrap;
  color: var(--d20-loading);
}
</style>
