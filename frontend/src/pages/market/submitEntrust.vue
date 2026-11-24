<template>
  <!-- P035 委托购买（原站 #/pages/market/submitEntrust，无参数直访真实态）
       侦查实测：collectionDetail + Prepayment/collectionDetail 均返回 code0「藏品ID不能为空」，
       但本页无 toast（仅渲染表单）。背景 rgb(245,245,245)；navbar「< 委托购买」；
       最高委托价卡 / 委托价格卡（价格·数量·汇付）/ 预计收入卡（总售价·综合服务费·藏品服务费）/ 委托说明卡；
       底部「合计：￥--」+「预付」。 -->
  <div class="b4-se-page">
    <img class="b4-se-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-se-title">委托购买</div>

    <div class="b4-se-card topcard"><span>最高委托价￥</span></div>

    <div class="b4-se-card pricecard">
      <div class="b4-se-label">委托价格 <span class="q">?</span></div>
      <div class="b4-se-price"><span class="cur">￥</span><span class="ph">请输入价格</span><span class="hint">高价挂单更快成交</span></div>
      <div class="b4-se-sep"></div>
      <div class="b4-se-qty">购买数量</div>
      <div class="b4-se-stepper"><span class="minus">−</span><span class="val">1</span><span class="plus">+</span></div>
      <div class="b4-se-pay">
        <span class="pay-logo">A</span><span class="pay-name">汇付</span>
        <svg class="pay-check" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
      </div>
    </div>

    <div class="b4-se-card incomecard">
      <div class="b4-se-label">预计收入</div>
      <div class="b4-se-line"><span>总售价:</span><span>¥--</span></div>
      <div class="b4-se-line"><span>综合服务费 (0%)</span><span>- ¥0</span></div>
      <div class="b4-se-line"><span>藏品服务费 (0.00%)</span><span>- ¥0.00</span></div>
    </div>

    <div class="b4-se-card notecard">委托说明</div>

    <div class="b4-se-footer">
      <span class="b4-se-total">合计：￥--</span>
      <div class="b4-se-paybtn">预付</div>
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
.b4-se-page {
  --b4-bg: rgb(245, 245, 245);
  --b4-ink: #000;
  --b4-ph: rgb(128, 128, 128);
  --b4-blue: rgb(48, 86, 237);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-bg); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-se-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; z-index: 2; }
.b4-se-title {
  position: absolute; left: 0; right: 0; top: 0; height: 44px; text-align: center;
  font-size: 17px; font-weight: 700; line-height: 44px; color: var(--b4-ink); background: #fff; z-index: 1;
}
.b4-se-card { position: absolute; left: 16px; width: 343px; border-radius: 12px; background: #fff; box-sizing: border-box; }
.b4-se-card.topcard { top: 64px; height: 53px; padding: 28px 26px 0; font-size: 14px; font-weight: 400; line-height: 14px; color: var(--b4-ink); }
.b4-se-card.pricecard { top: 129px; height: 153px; padding: 16px; }
.b4-se-label { position: absolute; left: 12px; top: 17px; font-size: 14px; font-weight: 700; color: var(--b4-ink); }
.b4-se-label .q { display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: rgb(200, 200, 200); color: #fff; font-size: 9px; text-align: center; line-height: 12px; vertical-align: 2px; }
.b4-se-price { position: absolute; left: 14px; top: 46px; width: 315px; height: 20px; font-size: 14px; }
.b4-se-price .cur { color: var(--b4-ink); }
.b4-se-price .ph { color: var(--b4-ph); margin-left: 0; }
.b4-se-price .hint { color: var(--b4-ph); font-size: 12px; float: right; line-height: 20px; }
.b4-se-sep { position: absolute; left: 12px; top: 76px; width: 319px; height: 1px; border-top: 1px dashed rgb(224, 224, 224); }
.b4-se-qty { position: absolute; left: 12px; top: 90px; font-size: 14px; font-weight: 400; color: var(--b4-ink); }
.b4-se-stepper {
  position: absolute; right: 12px; top: 85px; width: 93px; height: 23px; border-radius: 5px;
  border: 1px solid rgb(220, 220, 220); display: flex; align-items: center; justify-content: space-around; font-size: 14px; color: var(--b4-ink);
}
.b4-se-stepper .minus, .b4-se-stepper .plus { width: 17px; text-align: center; }
.b4-se-pay { position: absolute; left: 12px; top: 121px; display: flex; align-items: center; gap: 8px; }
.b4-se-pay .pay-logo { width: 20px; height: 20px; border-radius: 50%; background: var(--b4-blue); color: rgb(248, 243, 253); font-size: 11px; text-align: center; line-height: 20px; }
.b4-se-pay .pay-name { font-size: 14px; font-weight: 400; color: rgb(74, 74, 74); }
.b4-se-pay .pay-check { margin-left: 254px; color: rgb(180, 180, 180); font-size: 20px; }
.b4-se-card.incomecard { top: 284px; height: 172px; padding: 36px 12px 16px; }
.b4-se-card.incomecard .b4-se-label { position: absolute; top: 23px; left: 12px; }
.b4-se-line { display: flex; justify-content: space-between; margin-top: 27px; font-size: 14px; font-weight: 400; line-height: 14px; color: var(--b4-ink); }
.b4-se-card.notecard { top: 480px; height: 61px; padding: 20px 16px; font-size: 14px; font-weight: 700; color: var(--b4-ink); }
.b4-se-footer { position: absolute; left: 0; right: 0; bottom: 0; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; box-sizing: border-box; }
.b4-se-total { font-size: 14px; font-weight: 400; color: var(--b4-ink); }
.b4-se-paybtn { width: 165px; height: 46px; border-radius: 12px; background: rgb(49, 49, 49); color: #fff; font-size: 15px; font-weight: 600; text-align: center; line-height: 46px; }
</style>
