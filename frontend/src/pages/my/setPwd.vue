<template>
  <div class="b3-page b3-setpwd-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">设置操作密码</div>
    </div>
    <div class="sp-form">
      <div class="sp-input"><span class="sp-ph">请输入手机号码</span></div>
      <div class="sp-input">
        <span class="sp-ph">请输入验证码</span>
        <span class="sp-code-btn">获取验证码</span>
      </div>
      <div class="sp-input"><span class="sp-ph">请输入6位数字操作密码</span></div>
      <div class="sp-input"><span class="sp-ph">请再次输入6位数字</span></div>
      <div class="sp-submit">确认</div>
    </div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-setpwd-page {
  /* 原站 register-bg.png 1125×2436=3x，含红渐变+火兔logo+Welcome back，整页铺满 */
  background: rgb(244, 248, 252) url('../../assets/images/b3-register-bg.png') no-repeat top center;
  background-size: 375px auto;
}
/* navbar 在背景图上透明，标题黑字 */
.b3-setpwd-page .b3-navbar { background: transparent; }
.sp-form { position: absolute; top: 344px; left: 0; width: 375px; }
.sp-input {
  position: relative;
  width: 327px; height: 48px; margin: 0 auto 12px;
  background: rgb(236, 240, 244); border-radius: 24px; box-sizing: border-box;
  display: flex; align-items: center;
}
.sp-input:nth-of-type(4) { margin-bottom: 20px; }
.sp-ph {
  position: absolute; left: 20px;
  font-size: 14px; font-weight: 400; line-height: 20px; color: rgb(196, 200, 208);
}
.sp-code-btn {
  position: absolute; right: 6px; width: 87px; height: 36px;
  background: #000; border-radius: 18px;
  font-size: 12px; font-weight: 400; line-height: 36px; text-align: center; color: #fff;
}
.sp-submit {
  width: 327px; height: 48px; margin: 0 auto;
  background: rgb(232, 208, 210); border-radius: 24px;
  font-size: 16px; font-weight: 500; line-height: 48px; text-align: center; color: #fff;
}
/* 占位符文字垂直位置微调：原站 range 顶比输入盒顶低约 14px */
.sp-input .sp-ph { top: 14px; }
</style>
