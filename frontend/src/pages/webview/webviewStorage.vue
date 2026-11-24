<template>
  <!-- P113 webview-storage（原站 #/pages/webview/webview-storage，无参数直访真实态）
       侦查实测：原生 uni-page-head 返回箭头（框架层，对比 SKIP）+ wv-page 白底全屏；
       无 url 参数时内容空白；body 透明叠白底。clone 复刻为白底空页。 -->
  <div class="b29-wvs-page"></div>
</template>

<script setup>
import './webviewStorage.css'
</script>
