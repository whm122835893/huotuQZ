<template>
  <div class="d20-page d99-page">
    <div class="d20-navbar">
      <span class="d20-back" @click="goBack">{{ '\ue601' }}</span>
      <div class="d20-title">分享</div>
    </div>

    <!-- 黑卡：积分计数 + 分享图片按钮 @16,16 343×57 -->
    <div class="d99-card d20-card-dark">
      <div class="d99-card-count">分享社群，已获得 <span class="d99-count-num">{{ score }}</span> 积分</div>
      <div class="d99-share-btn" @click="noop">分享图片</div>
    </div>

    <!-- 上传图片标题 @16,89 -->
    <div class="d99-upload-title">上传图片:</div>
    <!-- 上传框 80×80（原站加号图 404 破图，渲染为空白） -->
    <div class="d99-upload-box"><span class="d99-upload-plus"></span></div>
    <!-- 提示 @16,223 -->
    <div class="d99-upload-tip">上传截图，图片大小限制5M内，数量3张</div>

    <!-- 表头 y=260 三列居中（无空态，当前真实态下无记录区） -->
    <div class="d99-table-head">
      <div class="d99-th d99-th1">日期</div>
      <div class="d99-th d99-th2">上传图片数量</div>
      <div class="d99-th d99-th3">审核结果</div>
    </div>

    <!-- 规则卡 y=299 343×64：图标占位 15×15 + 分享社群规则 15px/400 黑字 -->
    <div class="d99-rule-card d20-card-dark">
      <div class="d99-rule-title">分享社群规则</div>
    </div>

    <!-- 提交按钮 y=379 343×46 bg rgb(20,20,22) br0 -->
    <div class="d99-submit" @click="noop">提交</div>
  </div>
</template>

<script setup>
/* P099 分享社群（原站 #/pages/draw/shareCommunityGroup/shareCommunityGroup）
   原站 chunk pages-draw-shareCommunityGroup-shareCommunityGroup.fe090c11.js：
   onLoad 请求 GET /api/score/get_my_share_details?type=1|2 —— 原站实测 HTTP 404
   空响应体（2026-11-16 curl 验证），前端 catch 后积分默认 0，页面静态渲染。
   本地后端同构返回 HTTP 404（controller/Draw.php），行为逐帧一致。
   上传/提交/分享图片交互入口无参数态下点击无可见反应。 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './common.css'

const score = ref(0)
const router = useRouter()
function goBack() { router.back() }
function noop() {}

onMounted(async () => {
  /* 原站 chunk：shareType = 页面参数 type||1；404 空 body -> catch -> 默认 0 */
  try { await http.get('/api/score/get_my_share_details', { params: { type: 1 } }) } catch (e) { /* 404 如实保持 */ }
})
</script>

<style scoped>
.d99-page { background: var(--d20-bg); min-height: 100vh; }
/* 黑卡 @16,16 343×57 padding 12px 20px（原站实测） */
.d99-card {
  margin: 16px 16px 0;
  height: 57px;
  padding: 12px 20px;
  display: flex; align-items: center; justify-content: space-between;
}
.d99-card-count { font-size: 14px; color: var(--d20-gray6); padding-top: 5px; }
.d99-count-num { color: var(--d20-theme); margin: 0 6px; }
.d99-share-btn {
  width: 68px; height: 33px;
  border-radius: 8px;
  padding: 11px 6px 6px;
  box-sizing: border-box;
  font-size: 14px; line-height: 21px;
  text-align: center;
  color: var(--d20-theme);
  cursor: pointer;
}
.d99-upload-title { margin: 21px 16px 0; font-size: 15px; line-height: 22px; color: var(--d20-title-gray); }
.d99-upload-box {
  margin: 11px 16px 0;
  width: 80px; height: 80px;
  background: var(--d20-upload-bg);
  border-radius: var(--d20-radius-upload);
  display: flex; align-items: center; justify-content: center;
}
.d99-upload-plus { width: 16px; height: 16px; }  /* 原站 404 破图占位 */
.d99-upload-tip { margin: 21px 16px 0; font-size: 14px; line-height: 21px; color: var(--d20-gray9); }
/* 表头 y=260 三列 */
.d99-table-head { margin: 11px 16px 0; display: flex; justify-content: space-between; }
.d99-th { font-size: 12px; line-height: 18px; text-align: center; color: var(--d20-gray-head); }
.d99-th1 { width: 120px; }
.d99-th2 { width: 86px; }
.d99-th3 { width: 69px; }
/* 规则卡 y=299 343×64：无图标（原站 E 无 u-icon），标题 15px/400 黑字行首 x31 */
.d99-rule-card { margin: 21px 16px 0; height: 64px; padding: 15px; box-sizing: border-box; }
.d99-rule-title { font-size: 15px; font-weight: 400; line-height: 22px; padding-top: 5px; color: var(--d20-black); }
/* 提交按钮 y=379：bg rgb(20,20,22) 无圆角（原站 br-33 类计算值 0px），白字 15px/400 */
.d99-submit {
  margin: 16px 16px 0;
  height: 46px;
  padding: 17px 0 7px;
  box-sizing: border-box;
  background: var(--d20-btn-jb);
  color: var(--d20-white);
  font-size: 15px; font-weight: 400; line-height: 21px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
}
</style>
