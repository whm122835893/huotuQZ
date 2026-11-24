<template>
  <!-- P059 订单确认(首发)：无订单上下文=信息缺失禁用态；?demo=1 展示完整表单 -->
  <div class="ord-page fs-page">
    <OrderNav title="订单确认" />
    <div class="fs-top">
      <div class="ord-panel fs-goods">
        <img v-if="hasProduct" class="fs-gimg" :src="productImage" alt="" />
        <div v-else class="fs-gph"></div>
        <div class="fs-gtext">
          <div v-if="hasProduct" class="fs-gname">{{ productName }}</div>
          <div class="fs-issue">发行0份</div>
        </div>
      </div>
    </div>
    <div class="ord-panel fs-form">
      <div class="fs-label">支付方式</div>
      <div class="fs-pay" :class="{ on: hasProduct }">
        <div class="fs-pay-name">汇付支付</div>
        <div class="fs-pay-desc">使用汇付支付进行快捷支付</div>
      </div>
      <div class="fs-row">
        <span class="fs-label">购买数量</span>
        <div class="fs-stepper">
          <span class="fs-op" @click="count = Math.max(1, count - 1)">−</span>
          <span class="fs-count">{{ count }}</span>
          <span class="fs-op" @click="count = Math.min(maxCount, count + 1)">+</span>
          <span class="fs-unit">份</span>
        </div>
      </div>
      <div class="fs-limit">首发限购{{ maxCount }}份</div>
      <div class="fs-row fs-total-row">
        <span class="fs-label">资产总价</span>
        <span class="fs-total">¥{{ total }}</span>
      </div>
    </div>
    <div v-if="!hasProduct" class="fs-toast">订单信息缺失</div>
    <div class="fs-footer">
      <div class="fs-buy" :class="{ off: !hasProduct }" @click="onBuy">立即购买</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import OrderNav from './OrderNav.vue'
import { isDemo, CONFIRM_PRODUCT } from './demo'
import './common.css'

const hasProduct = isDemo()
const productName = hasProduct ? CONFIRM_PRODUCT.name : ''
const productImage = hasProduct ? CONFIRM_PRODUCT.main_image : ''
const count = ref(1)
const maxCount = 1
const total = computed(() => hasProduct ? '0.00' : '0.00')
function onBuy() { /* 预览态不触发真实支付 */ }
</script>

<style scoped>
.fs-page { background: #F7F9FC; padding-bottom: 100px; }
.fs-top { padding: 12px 17px 0; }
.fs-goods { padding: 14px; display: flex; align-items: center; }
.fs-gimg { width: 54px; height: 54px; border-radius: 8px; object-fit: cover; display: block; }
.fs-gph { width: 54px; height: 54px; border-radius: 8px; background: #F0F0F0; }
.fs-gtext { padding-left: 12px; }
.fs-gname { font-size: 14px; font-weight: 700; color: #212121; }
.fs-issue { font-size: 12px; color: #9AA0AB; margin-top: 6px; }
.fs-form { margin: 14px 17px 0; padding: 18px 17px; }
.fs-label { font-size: 15px; font-weight: 700; color: #212121; }
.fs-pay { margin-top: 14px; padding: 14px; border-radius: 8px; background: #F7F9FC; border: 1px solid transparent; }
.fs-pay.on { background: #FEF2F2; border-color: #D93437; }
.fs-pay-name { font-size: 14px; font-weight: 500; color: #212121; }
.fs-pay-desc { font-size: 12px; color: #9AA0AB; margin-top: 6px; }
.fs-row { display: flex; align-items: center; justify-content: space-between; margin-top: 22px; }
.fs-stepper { display: flex; align-items: center; }
.fs-op { width: 20px; text-align: center; font-size: 18px; color: #9AA0AB; cursor: pointer; }
.fs-count { width: 32px; height: 32px; border-radius: 6px; background: #F5F5F5; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #212121; }
.fs-unit { margin-left: 8px; font-size: 14px; color: #212121; }
.fs-limit { text-align: right; font-size: 12px; color: #9AA0AB; margin-top: 6px; }
.fs-total-row { margin-top: 12px; }
.fs-total { font-size: 16px; color: #D93437; font-weight: 700; }
.fs-toast {
  position: fixed; top: 386px; left: 50%; transform: translateX(-50%);
  background: rgba(61, 61, 61, 0.9); color: #FFFFFF; font-size: 13px;
  padding: 10px 20px; border-radius: 6px; z-index: 10;
}
.fs-footer { position: fixed; bottom: 0; left: 0; right: 0; max-width: 750px; margin: 0 auto; height: 82px; padding: 12px 16px; background: #FFFFFF; }
.fs-buy { height: 48px; border-radius: 10px; background: #D93437; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.fs-buy.off { background: #C7CBD4; }
</style>
