<template>
  <!-- P112 webview（原站 #/pages/webview/webview，无参数直访真实态）
       侦查实测：原生 uni-page-head 返回箭头（框架层，对比 SKIP）+ 全屏 webview；
       无 url 参数时 iframe 空白；body 白底。clone 复刻为白底空页。 -->
  <div class="b29-wv-page"></div>
</template>

<script setup>
import './webview.css'
</script>
