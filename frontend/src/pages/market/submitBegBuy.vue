<template>
  <!-- P034 发起求购（原站 #/pages/market/submitBegBuy，无参数直访真实态）
       侦查实测：collectionDetail 返回 code0「藏品ID不能为空」，页面渲染表单 + 居中 toast。
       背景 rgb(245,245,245)；navbar「< 发起求购」；顶部白色输入框(r12)；
       求购价格卡（求购价格? / ¥请输入价格 / 高价更快成交 / 购买数量 -1+ / 汇付）；
       求购说明卡；底部「合计：￥--」+「预付」按钮；toast「藏品ID不能为空」。 -->
  <div class="b4-bb-page">
    <img class="b4-bb-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-bb-title">发起求购</div>

    <div class="b4-bb-topbox"></div>

    <div class="b4-bb-card">
      <div class="b4-bb-label">求购价格 <span class="q">?</span></div>
      <div class="b4-bb-price"><span class="cur">￥</span><span class="ph">请输入价格</span><span class="hint">高价更快成交</span></div>
      <div class="b4-bb-sep"></div>
      <div class="b4-bb-qty">购买数量</div>
      <div class="b4-bb-stepper"><span class="minus">−</span><span class="val">1</span><span class="plus">+</span></div>
      <div class="b4-bb-pay">
        <span class="pay-logo">A</span><span class="pay-name">汇付</span>
        <svg class="pay-check" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
      </div>
    </div>

    <div class="b4-bb-note">求购说明</div>

    <div class="b4-bb-toast">藏品ID不能为空</div>

    <div class="b4-bb-footer">
      <span class="b4-bb-total">合计：￥--</span>
      <div class="b4-bb-paybtn">预付</div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.b4-bb-page {
  --b4-bg: rgb(245, 245, 245);
  --b4-ink: #000;
  --b4-ph: rgb(128, 128, 128);
  --b4-blue: rgb(48, 86, 237);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-bg); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-bb-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; z-index: 2; }
.b4-bb-title {
  position: absolute; left: 0; right: 0; top: 0; height: 44px; text-align: center;
  font-size: 17px; font-weight: 700; line-height: 44px; color: var(--b4-ink); background: #fff; z-index: 1;
}
.b4-bb-topbox { position: absolute; left: 16px; top: 64px; width: 343px; height: 32px; border-radius: 12px; background: #fff; }
.b4-bb-card { position: absolute; left: 16px; top: 108px; width: 343px; height: 155px; border-radius: 12px; background: #fff; padding: 16px; box-sizing: border-box; }
.b4-bb-label { position: absolute; left: 12px; top: 17px; font-size: 15px; font-weight: 700; color: var(--b4-ink); }
.b4-bb-label .q { display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: rgb(200, 200, 200); color: #fff; font-size: 9px; text-align: center; line-height: 12px; vertical-align: 2px; }
.b4-bb-price { position: absolute; left: 12px; top: 45px; width: 319px; height: 20px; font-size: 14px; }
.b4-bb-price .cur { color: var(--b4-ink); }
.b4-bb-price .ph { color: var(--b4-ph); margin-left: 0; }
.b4-bb-price .hint { color: var(--b4-ph); font-size: 12px; float: right; line-height: 20px; }
.b4-bb-sep { position: absolute; left: 12px; top: 76px; width: 319px; height: 1px; border-top: 1px dashed rgb(224, 224, 224); }
.b4-bb-qty { position: absolute; left: 12px; top: 91px; font-size: 14px; font-weight: 400; color: var(--b4-ink); }
.b4-bb-stepper {
  position: absolute; right: 12px; top: 86px; width: 93px; height: 23px; border-radius: 5px;
  border: 1px solid rgb(220, 220, 220); display: flex; align-items: center; justify-content: space-around; font-size: 14px; color: var(--b4-ink);
}
.b4-bb-stepper .minus, .b4-bb-stepper .plus { width: 17px; text-align: center; }
.b4-bb-pay { position: absolute; left: 12px; top: 122px; display: flex; align-items: center; gap: 24px; }
.b4-bb-pay .pay-logo { width: 20px; height: 20px; border-radius: 50%; background: var(--b4-blue); color: rgb(248, 243, 253); font-size: 11px; text-align: center; line-height: 20px; }
.b4-bb-pay .pay-name { font-size: 15px; font-weight: 400; color: var(--b4-ink); }
.b4-bb-pay .pay-check { margin-left: 270px; color: rgb(180, 180, 180); font-size: 20px; }
.b4-bb-note { position: absolute; left: 16px; top: 279px; width: 343px; height: 61px; border-radius: 12px; background: #fff; font-size: 14px; font-weight: 500; color: var(--b4-ink); padding: 20px 16px; box-sizing: border-box; }
.b4-bb-toast {
  position: absolute; left: 122px; top: 387px; width: 132px; height: 39px; border-radius: 5px;
  background: rgba(17, 17, 17, 0.7); color: #fff; font-size: 13px; font-weight: 400; text-align: center; line-height: 39px;
}
.b4-bb-footer { position: absolute; left: 0; right: 0; bottom: 0; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; box-sizing: border-box; }
.b4-bb-total { font-size: 15px; font-weight: 400; color: var(--b4-ink); }
.b4-bb-paybtn { width: 165px; height: 46px; border-radius: 12px; background: rgb(49, 49, 49); color: #fff; font-size: 15px; font-weight: 600; text-align: center; line-height: 46px; }
</style>
