<template>
  <nav class="rk-tabbar">
    <div v-for="tab in tabs" :key="tab.name" class="rk-tb-item" @click="onTab(tab)">
      <img class="rk-tb-icon" :class="{ 'rk-tb-icon--center': tab.center }" :src="tab.icon" :alt="tab.name" />
      <span class="rk-tb-label" :class="{ 'rk-tb-label--active': tab.active }">{{ tab.name }}</span>
    </div>
  </nav>
</template>

<script setup>
/* 底部 5 Tab：原站 rankList/userBuy 实测为「首页」激活（tab-home-active.png + 红色标签），
   与 route.path 无关（当前路由是 rank 域）。故本批专用组件固定该态，
   不改动已交付的共享 TabBar.vue（其按 route.path 判断激活）。 */
import { useRouter } from 'vue-router'

const router = useRouter()
const tabs = [
  { name: '首页', path: '/', icon: new URL('../../assets/images/tab-home-active.png', import.meta.url).href, active: true },
  { name: '活动', path: '/pages/compound/compound', icon: new URL('../../assets/images/tab-market.png', import.meta.url).href },
  { name: '市场', path: '/pages/market/market', icon: new URL('../../assets/images/tab-activity.png', import.meta.url).href, center: true },
  { name: '发现', path: '/pages/notice/notice', icon: new URL('../../assets/images/tab-discover.png', import.meta.url).href },
  { name: '我的', path: '/pages/my/my', icon: new URL('../../assets/images/tab-mine.png', import.meta.url).href }
]

const delivered = ['/', '/pages/compound/compound', '/pages/market/market', '/pages/notice/notice', '/pages/my/my']
function onTab(tab) {
  if (delivered.includes(tab.path)) router.push(tab.path)
}
</script>
