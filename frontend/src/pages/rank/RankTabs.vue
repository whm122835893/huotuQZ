<template>
  <div class="rk-tabs" :style="{ top: offset + 'px' }">
    <span
      v-for="(t, i) in tabs"
      :key="t"
      class="rk-tab"
      :class="[i > 0 ? 'rk-tab--gap' : '', t === active ? 'rk-tab--active' : '']"
      @click="onTap(t)"
    >{{ t }}<i v-if="t === active" class="rk-tab-dot"></i></span>
    <span v-if="toast" class="rk-tabs-toast">{{ toast }}</span>
  </div>
</template>

<script setup>
/* 顶部导航：原站实测四 Tab 点击跳转
   经典 → /（首页） | 关注 → /pages/market/attentionList（P025 未交付）
   热榜 → /pages/rank/userBuy | 名人堂 → /pages/rank/rankList
   未交付目标沿用 TabBar 既有提示，不臆造页面。 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { NAV } from './demo'

const props = defineProps({
  tabs: { type: Array, required: true },
  active: { type: String, required: true },
  /* 原站两页顶部容器高度不同：rankList 文字 y=19，userBuy y=22，故分别下移 */
  offset: { type: Number, default: 11 }
})

const router = useRouter()
const toast = ref('')
let timer = null

function showToast(msg) {
  toast.value = msg
  clearTimeout(timer)
  timer = setTimeout(() => (toast.value = ''), 1600)
}

const delivered = {
  '经典': '/',
  '热榜': '/pages/rank/userBuy',
  '名人堂': '/pages/rank/rankList'
}

function onTap(t) {
  if (t === props.active) return
  if (delivered[t]) router.push(delivered[t])
  else showToast(NAV.pendingToast)
}
</script>

<style scoped>
.rk-tab--gap { margin-left: 32px; }
.rk-tabs-toast {
  position: absolute; left: 50%; top: 44px; transform: translateX(-50%);
  padding: 6px 12px; border-radius: 5px;
  background: rgba(17, 17, 17, 0.7);
  color: #ffffff; font-size: 12px; line-height: 16px;
  white-space: nowrap;
}
</style>
