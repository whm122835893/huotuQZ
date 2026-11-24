<template>
  <!-- 底部 Tab 栏：与原站逐像素对齐（y756 h56，5 项各 75 宽，中心凸起红色市场按钮） -->
  <nav class="ht-tabbar">
    <div
      v-for="tab in tabs"
      :key="tab.name"
      class="tab-item"
      :class="{ center: tab.center }"
      @click="onTab(tab)"
    >
      <img class="tab-icon" :class="{ big: tab.center }" :src="tab.center ? tab.icon : (isActive(tab) ? tab.active : tab.icon)" :alt="tab.name" />
      <span class="tab-label" :class="{ active: isActive(tab) }">{{ tab.name }}</span>
    </div>
  </nav>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

/* 图标命名沿用原站资源名（原站命名与标签非直观对应，按下标关系使用） */
const tabs = [
  { name: '首页', path: '/', icon: new URL('../assets/images/tab-home.png', import.meta.url).href, active: new URL('../assets/images/tab-home-active.png', import.meta.url).href },
  { name: '活动', path: '/pages/compound/compound', icon: new URL('../assets/images/tab-market.png', import.meta.url).href, active: new URL('../assets/images/tab-market-active.png', import.meta.url).href },
  { name: '市场', path: '/pages/market/market', icon: new URL('../assets/images/tab-activity.png', import.meta.url).href, active: '', center: true },
  { name: '发现', path: '/pages/notice/notice', icon: new URL('../assets/images/tab-discover.png', import.meta.url).href, active: new URL('../assets/images/tab-market-discover.png', import.meta.url).href },
  { name: '我的', path: '/pages/my/my', icon: new URL('../assets/images/tab-mine.png', import.meta.url).href, active: new URL('../assets/images/tab-mine-active.png', import.meta.url).href }
]

function isActive(tab) {
  if (tab.center) return route.path === tab.path
  return route.path === tab.path
}

let toastTimer = null
function showToast(msg) {
  let el = document.querySelector('.ht-toast')
  if (!el) {
    el = document.createElement('div')
    el.className = 'ht-toast'
    document.body.appendChild(el)
  }
  el.textContent = msg
  el.style.display = 'block'
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (el.style.display = 'none'), 2000)
}

/* 已交付的 Tab 页可直接跳转，未交付的给出提示 */
const deliveredPaths = ['/', '/pages/index/index', '/pages/my/my', '/pages/market/market', '/pages/notice/notice', '/pages/compound/compound']
function onTab(tab) {
  if (route.path === tab.path) return
  if (deliveredPaths.includes(tab.path)) {
    router.push(tab.path)
    return
  }
  showToast('该页面将在后续批次交付')
}
</script>

<style scoped>
.ht-tabbar {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: 56px;
  background: #fff;
  display: flex;
  z-index: 100;
  padding-bottom: env(safe-area-inset-bottom);
}
.tab-item {
  width: 75px;
  height: 55px;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 8px;
  cursor: pointer;
}
.tab-icon {
  width: 24px;
  height: 24px;
  display: block;
}
.tab-icon.big {
  width: 37px;
  height: 37px;
  margin-top: -5px;
}
.tab-label {
  margin-top: 3px;
  font-size: 10px;
  line-height: 14px;
  color: #6d727b;
}
.tab-label.active {
  color: #d93437;
}
</style>
