<template>
  <!-- P110 下载 APP（原站 #/pages/index/downloadApp，无参数直访真实态）
       侦查实测：浅灰底 rgb(247,249,252)；navbar「下载 APP」；
       顶部白卡（火兔 logo 占位 + 火兔标题 + 副标题 + 邀请机器人图 + 火兔角标）；
       红「Android 下载」/ 黑「iOS 下载」两按钮；
       「为什么下载 火兔」白卡 3 项说明；底部邀请码卡。 -->
  <div class="b28-da-page">
    <img class="b28-da-back" :src="IMG.back" alt="返回" @click="goBack" />
    <div class="b28-da-title">下载 APP</div>

    <!-- 顶部白卡 -->
    <div class="b28-da-card1">
      <div class="b28-da-logo"></div>
      <div class="b28-da-brand">火兔</div>
      <div class="b28-da-sub">探索数字藏品 · 官方发售与二级市场</div>
      <img class="b28-da-invite" :src="IMG.invite" alt="邀请" />
      <img class="b28-da-corner" :src="IMG.corner" alt="" />
    </div>

    <!-- 下载按钮 -->
    <div class="b28-da-btn-android"><span>Android 下载</span></div>
    <div class="b28-da-btn-ios"><span>iOS 下载</span></div>

    <!-- 说明白卡 -->
    <div class="b28-da-card2">
      <div class="b28-da-card2-title">为什么下载 火兔</div>
      <div class="b28-da-item">
        <div class="b28-da-icobox"><img :src="IMG.ic1" alt="" /></div>
        <div class="b28-da-item-txt">
          <div class="b28-da-item-h">官方发售</div>
          <div class="b28-da-item-d">首发藏品与盲盒，一手掌握开售节奏</div>
        </div>
      </div>
      <div class="b28-da-item">
        <div class="b28-da-icobox"><img :src="IMG.ic2" alt="" /></div>
        <div class="b28-da-item-txt">
          <div class="b28-da-item-h">二级市场</div>
          <div class="b28-da-item-d">寄售、求购、委托，交易更灵活</div>
        </div>
      </div>
      <div class="b28-da-item">
        <div class="b28-da-icobox"><img :src="IMG.ic3" alt="" /></div>
        <div class="b28-da-item-txt">
          <div class="b28-da-item-h">活动福利</div>
          <div class="b28-da-item-d">合成、抽奖、签到，更多官方惊喜</div>
        </div>
      </div>
    </div>

    <!-- 邀请码卡 -->
    <div class="b28-da-card3">
      <div class="b28-da-invite-t">好友邀请你加入 火兔</div>
      <div class="b28-da-code">HT173634</div>
      <div class="b28-da-copy">
        <img :src="IMG.copy" alt="" />
        <span>复制邀请码</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import './downloadApp.css'
const IMG = {
  back: new URL('../../assets/images/b25-back-btn.png', import.meta.url).href,
  invite: new URL('../../assets/images/b25-invite-friends.png', import.meta.url).href,
  corner: new URL('../../assets/images/b25-banner-right.png', import.meta.url).href,
  ic1: new URL('../../assets/images/b25-home-today.png', import.meta.url).href,
  ic2: new URL('../../assets/images/b25-market-recommend.png', import.meta.url).href,
  ic3: new URL('../../assets/images/b25-lottery-activity.png', import.meta.url).href,
  copy: new URL('../../assets/images/b25-icon-copy.png', import.meta.url).href,
}
const router = useRouter()
function goBack() { router.back() }
</script>
