<template>
  <div class="b24-buy-page">
    <!-- navbar 深蓝 rgb(35,39,68)：返回箭头 + 标题「购买」+ 右上「切换行情」 -->
    <img class="b24-back" :src="IMG.back" alt="返回" @click="goBack" />
    <div class="b24-nav-title">购买</div>
    <div class="b24-nav-right">切换行情</div>

    <!-- banner 轮播（首图可见 + 次图屏外，指示器两点） -->
    <div class="b24-banner">
      <img class="b24-banner-img" :src="IMG.banner1" alt="藏品宣传图" />
      <img class="b24-banner-img2" :src="IMG.banner2" alt="藏品宣传图2" />
    </div>
    <div class="b24-indicator-dot"></div>
    <div class="b24-indicator-bar"></div>

    <!-- 藏品信息行 -->
    <div class="b24-item-name">凤吹晚霞</div>
    <div class="b24-item-tag">火兔</div>
    <div class="b24-item-pct">-0.53%</div>
    <div class="b24-item-price">112.34CNY</div>

    <!-- Tab 栏 -->
    <div class="b24-tab b24-tab-active">流通</div>
    <div class="b24-tab b24-tab-muted">相关公告</div>
    <div class="b24-tab b24-tab-muted2">成交额</div>
  </div>
</template>

<script setup>
/* P105 行情-购买页（原站 #/pages/kline/buy）
   侦查实测（375×812，deviceScaleFactor=2）：深色主题——内容区背景 rgb(0,0,0) 纯黑，
   navbar rgb(35,39,68) 深蓝。标题「购买」17px/700 白 @(171,11)，
   右上「切换行情」12px/400 白 @(321,16)，返回箭头 back_btn.png 7×13 @(19,16)。
   banner 为 u-swiper 轮播：首图 @(16,44 375×163) 750×580 源图 object-fit cover，
   次图 @(391,44) 屏外；指示器暗黄点 4×4 @(195,163) + 亮绿条 9×4 @(203,163)。
   信息行「凤吹晚霞」18px/600 白 @(16,224)、「火兔」12px/400 rgb(122,122,122) @(102,226)、
   「-0.53%」12px/400 rgb(51,51,51) @(221,224)、「112.34CNY」20px/400 rgb(51,51,51) @(263,220)。
   Tab「流通」15px/400 白 active @(16,257)（背景图 OBS 域加载失败如实不落地）、
   「相关公告」「成交额」15px/400 rgb(122,122,122) @(58,257)/@(130,257)。
   接口：全局 getConfig/getUserInfo + banner/show/getBanner（data.total=2 两张 banner），
   均为全局接口，无页面专用接口。静态快照复刻。系统隐藏「Cancel」@(163,822) 对比层 SKIP。 */
import { useRouter } from 'vue-router'
import './buy.css'

const IMG = {
  back: new URL('../../assets/images/b24-back-btn.png', import.meta.url).href,
  banner1: new URL('../../assets/images/b24-banner-1.jpg', import.meta.url).href,
  banner2: new URL('../../assets/images/b24-banner-2.jpg', import.meta.url).href
}
const router = useRouter()
function goBack() { router.back() }
</script>
