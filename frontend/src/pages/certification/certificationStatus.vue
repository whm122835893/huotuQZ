<template>
  <div class="b23-cstatus-page">
    <!-- navbar：返回箭头 7×13 @(19,16) + 标题「认证结果」17px/700 @(154,11) -->
    <img class="b23-back" :src="IMG.back" alt="返回" @click="goBack" />
    <div class="b23-nav-title">认证结果</div>

    <!-- 结果信息 -->
    <div class="b23-fail-title">实名认证失败</div>
    <div class="b23-fail-desc">该身份信息认证失败，请重试</div>

    <!-- 中间空图标占位（原站 140×140 @(118,264) 透明空盒，截图无可见内容，如实留空） -->

    <!-- 重新认证按钮 @(101,484 174×46) bg rgb(49,49,49) br12 -->
    <div class="b23-btn-retry"><span>重新认证</span></div>
  </div>
</template>

<script setup>
/* P104 实名认证-认证结果失败态（原站 #/pages/certification/certificationStatus）
   侦查实测（375×812，deviceScaleFactor=2）：整页纯白（BODY 默认透明，无背景图；
   cj-page-bg.png 404 破图如实不落地）。有原生 uni-page-head 白底 44px + 自定义 17px 标题。
   返回箭头 back_btn.png 7×13 @(19,16)；标题「认证结果」17px/700 黑 @(154,11 h23)。
   （原生 uni-page-head 标题 16px/700 @(155,13 h17) 属框架层，与自定义标题重叠，
     DOM 文本层两条「认证结果」按序对齐口径处理。）
   「实名认证失败」24px/500 rgb(51,51,51) @(116,136 h26)；
   「该身份信息认证失败，请重试」14px/400 rgb(122,122,122) @(97,176 h15)；
   中间 140×140 @(118,264) + 280×280 @(-22,124) 透明空 DIV（无可见图标，如实留空）；
   按钮 @(101,484 174×46) bg rgb(49,49,49)=#313131 br12 +「重新认证」15px/600 白 @(157,501)。
   仅调用全局 getConfig/getUserInfo，无页面专用接口；本账号 real_name_authentication=1
   但直接访问该路由显示失败态为原站真实态，静态快照复刻不臆造。
   系统隐藏项 uni-actionsheet「Cancel」@(163,822) 对比层 SKIP。 */
import { useRouter } from 'vue-router'
import './certificationStatus.css'

const IMG = {
  back: new URL('../../assets/images/b23-back-btn.png', import.meta.url).href
}
const router = useRouter()
function goBack() { router.back() }
</script>
