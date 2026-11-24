<template>
  <div class="lt-page">
    <div class="lt-navbar">
      <img class="lt-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="lt-title">抽奖活动</div>
      <div class="lt-mywin" @click="goRecord">我的中奖</div>
    </div>

    <div class="lt-machine">
      <img class="lt-bg" :src="IMG.bg" alt="抽奖机" />
      <span class="lt-count">当前可抽奖 0次</span>
    </div>

    <div class="lt-record-title">抽奖记录</div>
    <div class="lt-noact">暂无抽奖活动</div>
    <div class="lt-empty">
      <img class="lt-empty-img" :src="IMG.empty" alt="暂无数据" />
    </div>

    <div class="lt-actions">
      <img class="lt-btn" :src="IMG.drawOne" alt="抽 1 次" @click="toast('暂无抽奖活动')" />
      <img class="lt-btn lt-btn--five" :src="IMG.drawFive" alt="抽 5 次" @click="toast('暂无抽奖活动')" />
    </div>

    <div v-if="toastMsg" class="lt-toast">{{ toastMsg }}</div>
  </div>
</template>

<script setup>
/* 批次3 占位落地页：抽奖活动（P092，原站 #/pages/lottery/lottery?id=2）
   原站 my 页「抽奖活动」点击后跳此路由，停留约 1s 显示「暂无抽奖活动」
   再自动返回 my。本地保留稳定展示态便于逐批验收（批次14 钱包同款处理，
   不模拟自动跳走，避免「点了没反应」），差异如实登记在 API 文档。
   lottery/info 原站 code:0「暂无抽奖活动」→ 老虎机九宫格空态。 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'

const IMG = {
  back: new URL('../../assets/images/b17-back-btn.png', import.meta.url).href,
  bg: new URL('../../assets/images/b17-lottery-bg.png', import.meta.url).href,
  empty: new URL('../../assets/images/b17-empty-data.png', import.meta.url).href,
  drawOne: new URL('../../assets/images/b17-lottery-draw-one.png', import.meta.url).href,
  drawFive: new URL('../../assets/images/b17-lottery-draw-five.png', import.meta.url).href
}

const router = useRouter()
const toastMsg = ref('')
let toastTimer = null

function goBack() { router.back() }

/** 原站「我的中奖」点击跳 #/pages/lottery/record?id=2（批次19 P093）。
 *  批次3/17 占位清理的同款联动：此处由 toast 占位改为真实跳转，视觉零改动。 */
function goRecord() { router.push('/pages/lottery/record?id=2') }

function toast(msg) {
  toastMsg.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastMsg.value = '' }, 1600)
}

/** 原站 onLoad 并发请求 lottery/info（code:0 暂无抽奖活动）、
 *  statistics（data:[]）、record（data.total:0），本项目等价走本地后端 */
onMounted(async () => {
  try { await http.post('/api/lottery/info', {}) } catch (e) { /* 空态如实保持 */ }
  try { await http.post('/api/lottery/statistics', {}) } catch (e) { /* 静默 */ }
  try { await http.post('/api/lottery/record', { page: 1 }) } catch (e) { /* 静默 */ }
})
</script>

<style scoped>
.lt-page {
  --lt-bg: #F4F6FA;
  --lt-dark: #212121;
  --lt-red: #D93437;
  --lt-count: #FF4144;
  --lt-black: #000000;
  --lt-white: #FFFFFF;
  min-height: 100vh;
  background: var(--lt-bg);
  font-family: var(--ht-font);
  box-sizing: border-box;
}
/* navbar：返回 @(19,16) / 标题「抽奖活动」17px 700 @(154,11) / 「我的中奖」14px 红 @(314,12) */
.lt-navbar { height: 44px; position: relative; background: var(--lt-white); }
.lt-back {
  position: absolute; left: 19px; top: 16px;
  width: 7px; height: 13px; cursor: pointer; z-index: 2;
}
.lt-title {
  position: absolute; left: 0; right: 0; top: 11px; height: 23px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px; font-weight: 700; line-height: 23px; color: var(--lt-black);
}
.lt-mywin {
  position: absolute; right: 6px; top: 12px;
  font-size: 14px; line-height: 20px; color: var(--lt-red);
  cursor: pointer; user-select: none;
}

/* 原站 navbar 44 + 4px 间隔 → 老虎机背景 375×470 @(0,48)，
   「当前可抽奖 0次」@(147,79)，按钮 @(20,756)/(195,756)。 */
.lt-machine { position: relative; height: 470px; margin-top: 4px; }
.lt-bg { width: 375px; height: 470px; display: block; }
.lt-count {
  position: absolute; left: 0; right: 0; top: 31px;
  text-align: center;
  font-size: 12px; line-height: 17px; color: var(--lt-count);
}
/* 原站「暂无抽奖活动」是 uni.showToast：118×39 @(128,387) br=5px rgba(17,17,17,.7)，
   文字 13px 白 @(148,400)，约 1s 后自动返回 my。本地按批次14 钱包同款保留
   稳定展示态便于逐批验收，几何如实对齐 toast，不模拟自动跳走。 */
.lt-noact {
  position: fixed; left: 128px; top: 387px;
  width: 118px; height: 39px; border-radius: 5px;
  background: rgba(17, 17, 17, 0.7);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; line-height: 15px; color: var(--lt-white);
  white-space: nowrap; z-index: 5;
}

/* 「抽奖记录」16px @(20,522)；空态图 142×142 @(117,593) */
.lt-record-title {
  padding: 4px 0 0 20px;
  font-size: 16px; line-height: 22px; color: var(--lt-dark);
}
.lt-empty { display: flex; justify-content: center; margin-top: 49px; }
.lt-empty-img { width: 142px; height: 142px; object-fit: contain; }

/* 两个抽奖按钮 160×48 @(20,756)/(195,756) */
.lt-actions {
  margin-top: 21px; padding: 0 20px;
  display: flex; gap: 15px;
}
.lt-btn { width: 160px; height: 48px; object-fit: contain; cursor: pointer; }
/* 原站「暂无抽奖活动」态实测：仅「抽 1 次」禁用态按钮可见，右侧空白。
   「抽 5 次」的 uni-image 预加载 img 虽在 DOM 中（img 本身 opacity:0，视觉由父层
   background-image 决定），但该态下父层未渲染。按不臆造如实隐藏，保留 DOM 占位
   供几何对照，不额外造出原站没有的红色按钮。 */
.lt-btn--five { opacity: 0; }

.lt-toast {
  position: fixed; left: 50%; bottom: 90px; transform: translateX(-50%);
  max-width: 300px; padding: 9px 16px; border-radius: 8px;
  background: rgba(33, 33, 33, 0.92); color: var(--lt-white);
  font-size: 13px; line-height: 18px; text-align: center; z-index: 60;
}
</style>
