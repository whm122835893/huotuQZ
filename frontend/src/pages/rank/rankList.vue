<template>
  <div class="rk-page">
    <RankTabs :tabs="NAV.tabs" :active="NAV.hallActive" />

    <div class="rk-seg">
      <div
        v-for="(s, i) in HALL.segments"
        :key="s"
        class="rk-seg-btn"
        :class="{ 'rk-seg-btn--on': seg === i }"
        @click="switchSeg(i)"
      >{{ s }}</div>
    </div>

    <div class="rk-podium">
      <div v-for="(p, i) in podiums" :key="i" class="rk-pod" :class="'rk-pod-' + (i + 1)">
        <img class="rk-pod-img" :src="IMG.logo" alt="" />
      </div>
      <div v-for="(p, i) in podiums" :key="'c' + i" class="rk-pod-col" :class="'rk-pod-col--' + (i + 1)">
        <span class="rk-pod-dash" :style="{ color: HALL.placeColors[i] }">{{ HALL.placeholder }}</span>
        <span class="rk-pod-wealth">{{ HALL.wealth }}</span>
      </div>
      <div class="rk-pod-line"></div>
    </div>

    <div class="rk-empty-box rk-empty-box--hall">
      <img class="rk-empty-img" :src="IMG.empty" alt="暂无数据" />
      <span class="rk-empty-txt">{{ HALL.empty }}</span>
    </div>

    <RankTabBar />
  </div>
</template>

<script setup>
/* P090 名人堂：原站 newrank1 → code:0「名人堂未开启」data:null，
   chunk .catch 走 uni.showToast("名人堂加载失败") + purchaseData=[] / saleData=[] / loading="empty"。
   稳定态（toast 消失后）：三领奖台占位 --/¥0、分隔线、空盒图 + 「目前没有任何数据」。
   /static/tangpai/hall-of-fame-*.png 原站全部 404 破图，故领奖台仅渲染圆环 + logo，
   不臆造奖牌/名次图。Segment 切换仅改选中态，数据恒为空（购入/售出同为空）。 */
import { ref, onMounted } from 'vue'
import http from '../../api/request'
import RankTabs from './RankTabs.vue'
import RankTabBar from './RankTabBar.vue'
import { IMG, NAV, HALL } from './demo'
import './common.css'

const seg = ref(0)
const podiums = [0, 1, 2]

async function loadHall() {
  try {
    await http.post('/api/rank/hallOfFame', { type: seg.value + 1 })
  } catch (e) { /* 原站 code:0 空态，如实保持 */ }
}

function switchSeg(i) {
  if (seg.value === i) return
  seg.value = i
  loadHall()
}

onMounted(loadHall)
</script>

<style scoped>
/* 空盒图 @117,487 142x142 + 文案 @135,640（13px/15px） */
.rk-empty-box--hall { top: 487px; }
.rk-empty-box--hall .rk-empty-txt { margin-top: 11px; }
</style>
