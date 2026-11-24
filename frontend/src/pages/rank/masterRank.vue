<template>
  <div class="rk-page rk-page--master">
    <div class="rk-navbar">
      <img class="rk-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="rk-navbar-title">{{ NAV.masterTitle }}</div>
    </div>

    <div class="rk-empty-box rk-empty-box--master">
      <img class="rk-empty-img" :src="IMG.empty" alt="暂无数据" />
    </div>
  </div>
</template>

<script setup>
/* P089 大师动态：原站实测为纯空态页。
   navbar「大师动态」@154,11 17px/700 + 返回箭头 @19,16 + 空盒图 @117,105 142x142。
   无专用接口（仅 getConfig / getUserInfo 全局请求）、无 TabBar、list 为空。
   chunk: navTitle 默认「大师动态」，onLoad 带 id/user_id 时才请求收藏清单，
   直接访问该路由维持空态，故不臆造任何列表数据。 */
import { useRouter } from 'vue-router'
import { IMG, NAV } from './demo'
import './common.css'

const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
/* 原站 masterRank 背景 rgb(247,249,252)，与另两页 rgb(244,245,245) 不同 */
.rk-page--master { background: var(--rk-master-bg); }
.rk-empty-box--master {
  top: 105px;
}
</style>
