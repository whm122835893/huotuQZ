<template>
  <div class="b23-cert-page">
    <!-- navbar：返回箭头 7×13 @(19,16) + 标题「实名认证」17px/700 @(154,11) -->
    <img class="b23-back" :src="IMG.back" alt="返回" @click="goBack" />
    <div class="b23-nav-title">实名认证</div>

    <!-- 认证状态主信息 -->
    <div class="b23-status">当前已完成实名认证</div>
    <div class="b23-field b23-field-name">姓名：王*绵</div>
    <div class="b23-field b23-field-id">身份证号：520************838</div>

    <!-- 隐私说明（两行） -->
    <div class="b23-privacy">为响应国家隐私数据相关条例，实名认证信息仅用于认证用户真实性，不会对个人信息进行泄露与保存。</div>

    <!-- 底部按钮 -->
    <div class="b23-btn-done"><span>已完成实名</span></div>
  </div>
</template>

<script setup>
/* P103 实名认证-已完成态（原站 #/pages/certification/certification）
   侦查实测（375×812，deviceScaleFactor=2）：整页背景图 cert-verified.png（1125×2436=3x，
   background-size:375px，50% 0% no-repeat）含粉渐变 + 3D 勾 + 底部云朵。
   navbar 无原生白底，标题「实名认证」17px/700 黑 @(154,11 h23)，返回箭头 back_btn.png 7×13 @(19,16)。
   主文案「当前已完成实名认证」20px/700 rgb(217,52,55) @(99,396 h27)；
   「姓名：王*绵」「身份证号：520************838」14px/400 rgb(197,202,211) @(150,436)/@(93,460)；
   隐私说明 11px/400 rgb(154,160,171) @(31,693 w314 h34 两行)；
   底部按钮 @(20,756 335×44) bg rgb(197,202,211) br12 +「已完成实名」14px/400 白 @(153,768)。
   仅调用全局 getConfig/getUserInfo（real_name/id_card 字段，页面二次脱敏），无页面专用接口。
   本账号 real_name_authentication=1 → 已完成态为真实态，静态快照复刻。
   系统隐藏项 uni-actionsheet「Cancel」@(163,822) 对比层 SKIP。 */
import { useRouter } from 'vue-router'
import './certification.css'

const IMG = {
  back: new URL('../../assets/images/b23-back-btn.png', import.meta.url).href
}
const router = useRouter()
function goBack() { router.back() }
</script>
