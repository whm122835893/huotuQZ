<template>
  <div class="b3-page b3-cancel-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">注销账号申请</div>
    </div>
    <div class="ca-h1">注销账号</div>
    <div class="ca-warn">您正在注销账号</div>
    <div class="ca-warn-red">请谨慎操作</div>
    <div class="ca-card">
      <div class="ca-t1"><span class="ca-tip"></span>帐号安全状态：</div>
      <div class="ca-t2">您的账号没有被盗风险及违规行为</div>
      <div class="ca-t3"><span class="ca-tip"></span>帐号安全状态：</div>
      <div class="ca-t4">1、无法登录；</div>
      <div class="ca-t5">2、所有信息将用于删除和清理（包括加入社区、账户余额、发布动态、聊天记录等）；</div>
      <div class="ca-t6">3、绑定手机/微信/AppleID/人脸认证等将会解绑；</div>
      <div class="ca-t7">4、其他不限于以上所列举的你的账号的资产和权益。</div>
    </div>
    <div class="ca-submit" @click="toast"><span class="ca-submit-txt">确认注销</span></div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function toast() {}
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-cancel-page { --b3-page-bg: rgb(247,247,247); }
.b3-cancel-page .b3-navbar { background: rgb(247,247,247); }
.ca-h1 { position: absolute; left: 24px; top: 88px; font-size: 24px; font-weight: 600; line-height: 26px; color: #000; }
.ca-warn { position: absolute; left: 135px; top: 206px; font-size: 15px; font-weight: 600; line-height: 16px; color: #000; }
.ca-warn-red { position: absolute; left: 150px; top: 228px; font-size: 15px; font-weight: 400; line-height: 16px; color: var(--b3-red); }
.ca-card {
  position: absolute; left: 32px; top: 262px; width: 311px; height: 310px;
  background: #fff; border-radius: 10px; box-sizing: border-box;
}
/* 卡片内文本逐行绝对定位（原站实测全屏 y，换算为卡片内偏移） */
/* 原站 uni-app 正文未配中文字体→Chrome 回退 Times New Roman 渲染中文，字形窄细；
   本地继承全局 PuHuiTi 字形不同导致整片 MAE 偏高，此处显式锁定原站字体栈对齐像素 */
.ca-t1,.ca-t3 { position: absolute; left: 17px; font-size: 16px; font-weight: 600; line-height: 17px; color: #000; font-family: "Times New Roman", serif; }
.ca-t1 { top: 31px; } .ca-t3 { top: 91px; }
/* 原站为小黄点（4px 圆点），非 product-tips 菱形图；用 CSS 圆点如实复刻 */
.ca-tip { position: absolute; left: -8px; top: 6px; width: 4px; height: 4px; border-radius: 50%; background: rgb(240,180,50); }
.ca-t2,.ca-t4,.ca-t5,.ca-t6,.ca-t7 { position: absolute; left: 12px; font-size: 15px; font-weight: 400; line-height: 20px; color: #000; font-family: "Times New Roman", serif; }
.ca-t2 { top: 58px; }
.ca-t4 { top: 118px; }
.ca-t5 { top: 138px; width: 285px; }
.ca-t6 { top: 206px; width: 290px; }
.ca-t7 { top: 250px; width: 285px; }
.ca-submit {
  position: absolute; left: 12px; top: 733px; width: 351px; height: 55px;
  background: var(--b3-btn-dark); border: 1px solid rgb(201,152,47); border-radius: 50px; box-sizing: border-box;
  font-size: 14px; font-weight: 400; line-height: 53px; text-align: center; color: #fff;
}
.ca-submit-txt { display: inline-block; transform: translateY(3px); }
</style>
