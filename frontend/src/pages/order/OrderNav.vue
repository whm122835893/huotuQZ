<template>
  <!-- 订单域共享导航栏：44px 白底 / 17px 700 居中标题 / 左侧返回 -->
  <div class="ord-nav">
    <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
    <span class="nav-title">{{ title }}</span>
    <slot name="extra">
      <div v-if="search" class="nav-extra" @click="$emit('search')">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="8.6" cy="8.6" r="5.6" stroke="#212121" stroke-width="1.6" />
          <line x1="12.8" y1="12.8" x2="17.4" y2="17.4" stroke="#212121" stroke-width="1.6" stroke-linecap="round" />
        </svg>
      </div>
    </slot>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
defineProps({ title: { type: String, default: '' }, search: { type: Boolean, default: false } })
defineEmits(['search'])
const router = useRouter()
function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>
