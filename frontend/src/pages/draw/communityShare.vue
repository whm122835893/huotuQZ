<template>
  <div class="d20-page d95-page">
    <div class="d20-navbar">
      <span class="d20-back" @click="goBack">{{ '\ue601' }}</span>
      <div class="d20-title">分享</div>
    </div>

    <!-- 黑卡：助力计数 + 分享图片按钮 @16,16 343×57 -->
    <div class="d95-card d20-card-dark">
      <div class="d95-card-count">您已成功获得助力 <span class="d95-count-num">{{ assistCount }}</span> 次</div>
      <div class="d95-share-btn" @click="noop">分享图片</div>
    </div>

    <!-- 上传图片标题 @16,89 -->
    <div class="d95-upload-title">上传图片:</div>
    <!-- 上传框 80×80 #E5E5E5 br12（原站加号图 404 破图，渲染为空白，16×16 占位） -->
    <div class="d95-upload-box"><span class="d95-upload-plus"></span></div>
    <!-- 提示 @16,223 -->
    <div class="d95-upload-tip">上传截图，图片大小限制5M内，数量3张</div>

    <!-- 表头 y=260 三列居中 -->
    <div class="d95-table-head">
      <div class="d95-th d95-th1">日期</div>
      <div class="d95-th d95-th2">上传图片数量</div>
      <div class="d95-th d95-th3">审核结果</div>
    </div>

    <!-- 空态 y=314：uicon \ue62f 90px + 暂无分享记录 -->
    <div class="d95-empty">
      <span class="d20-empty-icon">{{ '\ue62f' }}</span>
      <div class="d20-empty-text">暂无分享记录</div>
    </div>

    <!-- 规则卡 y=460 343×66：图标占位 15×15 + 标题 16px/700 -->
    <div class="d95-rule-card d20-card-dark">
      <span class="d95-rule-icon"></span>
      <div class="d95-rule-title">分享社群规则</div>
    </div>

    <div class="d95-gap"></div>

    <!-- 底部固定提交栏 375×76 #F5F5F5 -->
    <div class="d95-footer">
      <div class="d20-btn-dark d95-submit" @click="noop">提交</div>
    </div>
  </div>
</template>

<script setup>
/* P095 分享助力（原站 #/pages/draw/communityShare/communityShare）
   原站 chunk pages-draw-communityShare-communityShare.6ffa8f31.js：
   助力次数来自页面参数/storage，直接访问恒为 0（侦查实测 2026-11-16）；
   上传/提交/分享图片均为交互态入口，无参数态下点击无可见反应；
   上传框加号 IconAdd.png 原站 404（background-image:none），如实复刻空白占位。 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import './common.css'

const assistCount = ref(0)
const router = useRouter()
function goBack() { router.back() }
/* 无参数态下原站点击均无可见响应，本地保持一致 */
function noop() {}
</script>

<style scoped>
/* 黑卡 @16,16 343×57 padding 12px 20px（原站实测）：计数行 33px 居中，数字主题色 */
.d95-page { background: var(--d20-bg); min-height: 100vh; position: relative; }
.d95-card {
  margin: 16px 16px 0;
  height: 57px;
  padding: 12px 20px;
  display: flex; align-items: center; justify-content: space-between;
}
.d95-card-count { font-size: 14px; color: var(--d20-gray6); padding-top: 5px; }
.d95-count-num { color: var(--d20-theme); margin: 0 6px; }
.d95-share-btn {
  width: 68px; height: 33px;
  border-radius: 8px;
  padding: 11px 6px 6px;
  box-sizing: border-box;
  font-size: 14px; line-height: 21px;
  text-align: center;
  color: var(--d20-theme);
  cursor: pointer;
}/* 上传标题 @16,133 15px（文字 y138）；上传框 @16,127 80×80；提示 @16,267（文字 y272）。margin 链 21/11/21/11 补偿字体 metrics 差 */
.d95-upload-title { margin: 21px 16px 0; font-size: 15px; line-height: 22px; color: var(--d20-title-gray); }
.d95-upload-box {
  margin: 11px 16px 0;
  width: 80px; height: 80px;
  background: var(--d20-upload-bg);
  border-radius: var(--d20-radius-upload);
  display: flex; align-items: center; justify-content: center;
}
.d95-upload-plus { width: 16px; height: 16px; }  /* 原站 404 破图占位 */
.d95-upload-tip { margin: 21px 16px 0; font-size: 14px; line-height: 21px; color: var(--d20-gray9); }
/* 表头 y=260：三列 120/86/69 文字居中，flex space-between（列起点 16/170/290） */
.d95-table-head {
  margin: 11px 16px 0;
  display: flex; justify-content: space-between;
}
.d95-th { font-size: 12px; line-height: 18px; text-align: center; color: var(--d20-gray-head); }
.d95-th1 { width: 120px; }
.d95-th2 { width: 86px; }
.d95-th3 { width: 69px; }
/* 空态 y=314 高 121：图标 90px + 文字 21px（表头行底 278 -> 36px 间距） */
.d95-empty {
  margin: 36px 16px 0;
  height: 121px;
  display: flex; flex-direction: column; align-items: center; justify-content: flex-start;
}
/* 规则卡 y=460 343×66：图标占位 15×15 + 标题 16px/700（原站低对比暗字，如实复刻） */
.d95-rule-card { margin: 25px 16px 0; height: 66px; display: flex; align-items: center; }
.d95-rule-icon { width: 15px; height: 15px; flex-shrink: 0; }  /* 原站 404 破图占位 */
.d95-rule-title { margin-left: 10px; font-size: 16px; font-weight: 700; color: var(--d20-title-gray); }
.d95-gap { height: 222px; }
/* 底部固定栏 375×76：按钮 338×46 @19,15 */
.d95-footer {
  position: fixed; left: 0; right: 0; bottom: 0;
  height: 76px;
  background: var(--d20-bg);
  padding: 15px 18px 15px 19px;
  box-sizing: border-box;
}
.d95-submit { width: 338px; }
</style>
