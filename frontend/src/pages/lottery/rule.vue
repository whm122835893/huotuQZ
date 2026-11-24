<template>
  <div class="l19-page l19--rule">
    <div class="l19-navbar">
      <img class="l19-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="l19-title">活动规则</div>
    </div>

    <div class="l19-rule-card"></div>
  </div>
</template>

<script setup>
/* P094 活动规则（抽奖域，原站 #/pages/lottery/rule）
   原站 chunk pages-lottery-rule：data() -> lotteryRule = uni.getStorageSync('lotteryRule')
   -> <u-parse :content="handleLang(lotteryRule)"/> 渲染富文本。
   实测（2026-11-15，页面内 fetch + 完整从「我的→抽奖活动」链路监控）：
     - getConfig 中 draw_lots_rule / draw_lots_notice / helpdraw_rule / share_rule 等
       全部规则 key 均为空字符串；
     - localStorage 全程只有 __DC_STAT_UUID / App_version / token，
       lotteryRule 恒为 null（P092 抽奖页不写入该 storage）；
     - 故 .content 卡内 u-parse 无任何节点 → 空白卡是原站当前真实态。
   整页 + navbar 白；规则卡 @(16,59) 343×650 min-height 80vh br12 rgb(244,246,250)；无 TabBar。
   本项目如实复刻空白卡，不臆造规则正文；无页面专用接口（正文为本地存储），
   不新增后端端点。 */
import { useRouter } from 'vue-router'
import './common.css'

const IMG = {
  back: new URL('../../assets/images/b19-back-btn.png', import.meta.url).href
}

const router = useRouter()
function goBack() { router.back() }
</script>
