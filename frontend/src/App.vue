<template>
  <div class="phone-viewport">
    <router-view />
  </div>
</template>

<script setup>
</script>

<style>
/* 全宽容器：与原站一致，页面随手机屏幕宽度等比缩放（postcss-px-to-viewport 处理尺寸） */
.phone-viewport {
  width: 100%;
  min-height: 100vh;
  background: var(--ht-bg);
  position: relative;
}
</style>
