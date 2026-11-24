<template>
  <!-- P108 市值排行（原站 #/pages/marketCapRanking/marketCapRanking，无参数直访真实态）
       侦查实测：ranking 接口返回 data:{data:[], my:'', activityRule:''} 全空。
       整页纯白，无任何列表/文字内容；仅 uni 框架原生 head 返回箭头（框架层，对比 SKIP）。
       body 背景透明（叠白底）。如实复刻为空页。 -->
  <div class="b26-mcr-page"></div>
</template>

<script setup>
import './marketCapRanking.css'
</script>
