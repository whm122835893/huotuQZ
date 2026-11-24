<template>
  <div class="l19-page l19--record">
    <div class="l19-navbar">
      <img class="l19-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="l19-title">中奖记录</div>
    </div>

    <div class="l19-record-body">
      <div class="l19-record-empty">
        <img class="l19-empty-img" :src="IMG.empty" alt="暂无数据" />
      </div>
    </div>
  </div>
</template>

<script setup>
/* P093 中奖记录（抽奖域，原站 #/pages/lottery/record?id=2）
   原站 chunk pages-lottery-record：onLoad -> this.id = A.id||A.lottery_id||''
   -> getRaffleRecord() -> lotteryStatistics({lottery_id}) -> data:[] -> 空态。
   整页 + navbar 均 rgb(247,249,252)；空盒图 284rpx=142px @(117,97)；无列表、无空态文案、无 TabBar。
   注意：原站该页实际调 statistics（不是 /lottery/record），本项目等价走本地后端，
   返回 data:[] 时空态如实保持，不臆造中奖数据。 */
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './common.css'

const IMG = {
  back: new URL('../../assets/images/b19-back-btn.png', import.meta.url).href,
  empty: new URL('../../assets/images/b19-empty-data.png', import.meta.url).href
}

const router = useRouter()
function goBack() { router.back() }

onMounted(async () => {
  try { await http.post('/api/lottery/statistics', {}) } catch (e) { /* data:[] 空态如实保持 */ }
})
</script>
