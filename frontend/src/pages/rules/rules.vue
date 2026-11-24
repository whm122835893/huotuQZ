<template>
  <!-- P109 活动规则（原站 #/pages/rules/rules，无参数直访真实态）
       侦查实测：白底；navbar「活动规则」（17px/700/黑）+ 返回箭头 back_btn.png；
       其下浅灰内容卡（345×406 at 15,59，rgb(244,246,250)，br12），
       规则正文 getConfig.draw_lots_rule 为空串，故内容卡为空。 -->
  <div class="b27-rules-page">
    <img class="b27-back" :src="back" alt="返回" @click="goBack" />
    <div class="b27-title">活动规则</div>
    <div class="b27-content"></div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import './rules.css'
const back = new URL('../../assets/images/b25-back-btn.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>
