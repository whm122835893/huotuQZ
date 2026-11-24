<template>
  <div class="activity-page d21--draw"></div>
</template>

<script setup>
/* P100 抽签（活动域，原站 #/pages/activity/draw）
   原站 chunk pages-activity-draw.e83302e5：onLoad → GET /api/draw/get
   → 服务端返回 {"code":0,"msg":"活动不存在","data":null}，组件渲染时抛异常
   （pageerror "Object"）→ <uni-page-body><!----></uni-page-body> 整页纯白，
   仅剩隐藏的 uni-actionsheet（Cancel @y822，对比层 SKIP）。
   这是无活动参数直访的真实态：如实复刻为纯白空页，不臆造抽奖界面/倒计时/按钮。
   原站并行请求 /api/draw/drawLog（data:[]）与 /api/user/ranking（types:2）均不产生可见内容。 */
import { onMounted } from 'vue'
import http from '../../api/request'
import './common.css'

onMounted(async () => {
  // 与上方侦查同样的调用形态；"活动不存在" 分支下页面保持纯白
  try { await http.get('/api/draw/get') } catch (e) { /* 空态如实保持 */ }
})
</script>
