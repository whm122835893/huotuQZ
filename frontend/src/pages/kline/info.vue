<template>
  <div class="b24-info-page">
    <!-- navbar 深蓝：返回箭头 + 标题「详情」+ 右上「切换购买」 -->
    <img class="b24-back" :src="IMG.back" alt="返回" @click="goBack" />
    <div class="b24-nav-title">详情</div>
    <div class="b24-nav-right">切换购买</div>

    <!-- banner 轮播 -->
    <div class="b24-banner">
      <img class="b24-banner-img" :src="IMG.banner1" alt="藏品宣传图" />
      <img class="b24-banner-img2" :src="IMG.banner2" alt="藏品宣传图2" />
    </div>
    <div class="b24-indicator-dot"></div>
    <div class="b24-indicator-bar"></div>

    <!-- 藏品名称 / 价格 -->
    <div class="b24-name">藏品名称</div>
    <div class="b24-name-tag">比太</div>
    <div class="b24-price">112.34CNY</div>
    <div class="b24-pct">-0.53%</div>

    <!-- 数据网格 2 列 × 3 行 -->
    <div class="b24-cell b24-l1">流通：</div><div class="b24-cell-v b24-l1v">30000</div>
    <div class="b24-cell b24-r1">最近5min成交量：</div><div class="b24-cell-v b24-r1v">30000</div>
    <div class="b24-cell b24-l2">24h最高：</div><div class="b24-cell-v b24-l2v">30000</div>
    <div class="b24-cell b24-r2">最近1h成交量：</div><div class="b24-cell-v b24-r2v">30000</div>
    <div class="b24-cell b24-l3">24h最低：</div><div class="b24-cell-v b24-l3v">30000</div>
    <div class="b24-cell b24-r3">今日成交量：</div><div class="b24-cell-v b24-r3v">30000</div>

    <!-- Tab 栏（横向，7px 缩放态） -->
    <div class="b24-sticky"></div>
    <div class="b24-itab b24-itab-1">行情</div>
    <div class="b24-itab b24-itab-2">相关公告</div>
    <div class="b24-itab b24-itab-3">成交记录</div>
    <div class="b24-itab b24-itab-4">换手榜</div>
    <div class="b24-itab b24-itab-5">寄售</div>

    <!-- 底部按钮 -->
    <div class="b24-btn-notice"><span>开启行情提醒</span></div>
    <div class="b24-btn-batch"><span>批量买卖</span></div>
    <div class="b24-score">sc3</div>
    <div class="b24-follow">关注</div>
  </div>
</template>

<script setup>
/* P106 行情-详情页（原站 #/pages/kline/info）
   侦查实测（375×812，deviceScaleFactor=2）：深色主题——整页背景 rgb(35,39,68) 深蓝
   （区别于 P105 纯黑内容区）；navbar 同色。标题「详情」17px/700 白 @(171,11)，
   右上「切换购买」12px/400 白 @(321,16)，返回箭头 7×13 @(19,16)。
   banner u-swiper：首图 @(13,52 375×163) 源图 750×580 cover，次图 @(388,52) 屏外；
   指示器暗黄点 4×4 @(192,171) + 亮绿条 9×4 @(200,171)。
   「藏品名称」16px/400 白 @(13,229)、「比太」12px/400 rgb(122,122,122) @(87,230)、
   「112.34CNY」16px/400 rgb(51,51,51) @(13,255)、「-0.53%」12px/400 rgb(51,51,51) @(100,257)。
   数据 2 列×3 行：标签 12px/400 rgb(122,122,122)、数值 12px/400 白，左列 x13、右列 x170，
   数值左列 x105、右列 x242~267，三行 y282/304/326。
   sticky Tab 栏 @(0,367 375×10) 深蓝，Tab 文字 7px 缩放态 @y369（行情白 active、其余 rgb(65,65,65)），
   「寄售」@x443 屏外如实复刻。
   底部按钮：粉色 rgb(231,141,210) @(16,763 144×38) br25「开启行情提醒」15px/400 白、
   橙黄 rgb(234,171,75) @(176,763 144×38) br25「批量买卖」15px/400 白，
   「sc3」24px/400 白 @(331,763)、「关注」10px/400 白 @(337,791)。
   接口仅全局 getConfig/getUserInfo/getBanner，静态快照复刻。
   系统隐藏「Cancel」@(163,822) 对比层 SKIP。 */
import { useRouter } from 'vue-router'
import './info.css'

const IMG = {
  back: new URL('../../assets/images/b24-back-btn.png', import.meta.url).href,
  banner1: new URL('../../assets/images/b24-banner-1.jpg', import.meta.url).href,
  banner2: new URL('../../assets/images/b24-banner-2.jpg', import.meta.url).href
}
const router = useRouter()
function goBack() { router.back() }
</script>
