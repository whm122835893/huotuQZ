<template>
  <div class="b3-page b3-addredit-page">
    <div class="b3-navbar">
      <img class="b3-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="b3-title">添加收货地址</div>
    </div>
    <div class="ae-card">
      <div class="ae-row">
        <span class="ae-label">收货人</span><span class="ae-ph">请输入姓名</span>
      </div>
      <div class="ae-row">
        <span class="ae-label">手机号码</span><span class="ae-ph">请输入手机号</span>
      </div>
      <div class="ae-row">
        <span class="ae-label">所在地区</span><span class="ae-ph">请选择市区</span>
      </div>
      <div class="ae-row">
        <span class="ae-label">详细地址</span><span class="ae-ph">街道 小区 门牌号等</span>
      </div>
      <div class="ae-row ae-row-default">
        <span class="ae-label">设置默认收货地址</span>
        <span class="ae-switch"><span class="ae-switch-dot"></span></span>
      </div>
    </div>
    <div class="ae-save"><span class="ae-save-txt">保存</span></div>
  </div>
</template>

<script setup>
import back from '../../assets/images/b3-back-btn.png'
const IMG = { back }
function goBack() { history.back() }
</script>

<style scoped>
@import './b3-common.css';
.b3-addredit-page { --b3-page-bg: rgb(244,246,250); }
.b3-addredit-page .b3-navbar { background: rgb(244,246,250); }
.ae-card {
  position: absolute; left: 16px; top: 60px; width: 343px; height: 286px;
  background: rgb(244,246,250); border-radius: 12px; box-sizing: border-box;
}
.ae-row { position: relative; height: 53px; }
.ae-label {
  position: absolute; left: 16px; top: 32px;
  font-size: 14px; font-weight: 700; line-height: 15px; color: #000;
}
.ae-ph {
  position: absolute; left: 106px; top: 30px;
  font-size: 14px; font-weight: 400; line-height: 15px; color: var(--b3-gray-192);
}
.ae-row-default { margin-top: 0; }
.ae-row-default .ae-label { top: 39px; }
.ae-switch {
  position: absolute; left: 285px; top: 9px; width: 42px; height: 22px;
  background: #fff; border-radius: 50px; box-sizing: border-box;
}
.ae-switch-dot {
  position: absolute; left: 11px; top: 1px; width: 30px; height: 20px;
  background: #fff; border-radius: 0 50px 50px 0;
}
.ae-save {
  position: absolute; left: 16px; top: 742px; width: 343px; height: 46px;
  background: var(--b3-btn-dark); border-radius: 12px;
  font-size: 15px; font-weight: 600; line-height: 46px; text-align: center; color: #fff;
}
.ae-save-txt { display: inline-block; transform: translateY(5px); }
</style>
