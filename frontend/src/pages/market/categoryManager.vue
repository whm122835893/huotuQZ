<template>
  <!-- P026 分类管理（原站 #/pages/market/categoryManager，无参数直访真实态）
       侦查实测：白底；navbar「< 分类 确认」；「我的分类」标签 + 3 胶囊按钮；
       「其他分类」标签（其下 custom_series/platform_series 均空，无内容）。 -->
  <div class="b4-cm-page">
    <img class="b4-cm-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-cm-title">分类</div>
    <div class="b4-cm-confirm">确认</div>

    <div class="b4-cm-label">我的分类</div>
    <div class="b4-cm-pill p1">我的关注</div>
    <div class="b4-cm-pill p2">推荐</div>
    <div class="b4-cm-pill p3">全部</div>
    <div class="b4-cm-label b4-cm-label2">其他分类</div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.b4-cm-page {
  --b4-ink: #000;
  --b4-sub: rgb(85, 85, 85);
  --b4-pill: rgb(244, 246, 250);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: #fff; overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-cm-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; }
.b4-cm-title {
  position: absolute; left: 0; right: 0; top: 11px; height: 23px; text-align: center;
  font-size: 17px; font-weight: 700; line-height: 23px; color: var(--b4-ink);
}
.b4-cm-confirm { position: absolute; right: 6px; top: 16px; font-size: 12px; font-weight: 400; line-height: 14px; color: var(--b4-sub); }
.b4-cm-label { position: absolute; left: 16px; top: 65px; font-size: 14px; font-weight: 400; line-height: 15px; color: var(--b4-sub); }
.b4-cm-label2 { top: 167px; }
.b4-cm-pill {
  position: absolute; top: 93px; width: 106px; height: 37px; border-radius: 43px;
  background: var(--b4-pill); font-size: 14px; font-weight: 400; color: var(--b4-sub);
  text-align: center; line-height: 37px;
}
.b4-cm-pill.p1 { left: 16px; }
.b4-cm-pill.p2 { left: 134px; }
.b4-cm-pill.p3 { left: 252px; }
</style>
