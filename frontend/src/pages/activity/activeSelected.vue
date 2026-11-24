<template>
  <div class="activity-page d22--selected">
    <div class="d22-navbar">
      <img class="d22-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="d22-title">活动中心</div>
    </div>

    <div class="d22-banners">
      <div class="d22-banner"><span class="d22-banner-text">最新活动</span></div>
      <div class="d22-banner"><span class="d22-banner-text">抽奖中心</span></div>
    </div>

    <img class="d22-empty-img" :src="IMG.empty" alt="暂无数据" />
    <div class="d22-empty-text">目前没有任何数据</div>
  </div>
</template>

<script setup>
/* P102 活动精选（活动域，原站 #/pages/activeSelected/activeSelected）
   原站 chunk pages-activeSelected-activeSelected.165f52fe：onLoad →
   POST /api/Activity/category（data:[]）+ POST /api/activity/index（data:{total:0,data:[]}）
   → 两个分类下均无活动 → 空态。
   侦查实测（375×812）：整页 + navbar 均 rgb(244,246,250)；
   navbar 非白底——顶部同为浅灰蓝 Token 色；标题「活动中心」17px/700 黑 @(154,11 h23)；
   返回箭头 back_btn.png 7×13 @(19,16)。
   顶部两个分类入口 .item @(16,60) 164×64 与 @(195,60) 164×64，背景图 active-banner-bg1/bg2.png
   原站 OBS 域跨域加载失败 + cj-page-bg.png/active-new.png/active-lottey.png 均 404 破图，
   实测卡片背景 = 页面浅灰蓝 Token 色（背景图 404 后隐形），仅白字「最新活动」@(90,86)、
   「抽奖中心」@(269,86) 15px/400 rgb(255,255,255) 浮于页面底上，如实复刻不臆造图片。
   空盒图 284rpx=142px @(116.5,236) 142×142 +「目前没有任何数据」13px 灰色 @(135,389)。
   图片与批次19 中奖记录页逐像素同图（b19 资源），零重复下载。无 TabBar。 */
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './activeSelected.css'

const IMG = {
  back: new URL('../../assets/images/b19-back-btn.png', import.meta.url).href,
  empty: new URL('../../assets/images/b19-empty-data.png', import.meta.url).href
}

const router = useRouter()
function goBack() { router.back() }

onMounted(async () => {
  try { await http.post('/api/Activity/category', {}) } catch (e) { /* 空态如实保持 */ }
  try { await http.post('/api/activity/index', { page: 1, limit: 10, category_id: 1 }) } catch (e) { /* 空态如实保持 */ }
})
</script>
