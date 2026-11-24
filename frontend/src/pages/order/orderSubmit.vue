<template>
  <!-- P062 确认订单(orderSubmit)：无 storage=NaN 空壳态；?demo=1 展示完整表单 -->
  <div class="ord-page os-page">
    <OrderNav title="确认订单" />
    <div class="os-goods ord-panel">
      <img v-if="hasProduct" class="os-gimg" :src="productImage" alt="" />
      <div v-if="hasProduct" class="os-gname">{{ productName }}</div>
      <div class="os-stepper">
        <span class="os-op" @click="count = Math.max(1, count - 1)">−</span>
        <span class="os-count">{{ count }}</span>
        <span class="os-op" @click="count = count + 1">+</span>
      </div>
    </div>
    <div class="os-addr ord-panel">
      <div class="os-row"><span class="os-k">藏品编号:</span><span class="os-v">随机编号</span></div>
      <div class="os-row"><span class="os-k">区块链地址:</span><span class="os-v chain">{{ chainAddr }}<img class="os-copy" src="../../assets/images/order-icon-copy.png" alt="复制" @click="copy(chainAddr)" /></span></div>
      <div class="os-row"><span class="os-k">订单金额:</span><span class="os-v money">￥{{ amount }}</span></div>
    </div>
    <div class="os-channel">
      <span class="os-ch-name">汇付</span>
      <template v-if="hasProduct">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="9" cy="9" r="9" fill="#C9BFA8" />
          <path d="M5 9.2L7.8 12L13 6.5" stroke="#FFFFFF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </template>
      <span v-else class="os-ch-tip">未支持该支付通道收款</span>
    </div>
    <div class="os-footer">
      <div class="os-amount">¥{{ amount }}</div>
      <div class="os-pay" @click="onPay">立即支付</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import OrderNav from './OrderNav.vue'
import { isDemo, CONFIRM_PRODUCT, CHAIN_ADDR } from './demo'
import './common.css'

const hasProduct = isDemo()
const productName = hasProduct ? CONFIRM_PRODUCT.name : ''
const productImage = hasProduct ? CONFIRM_PRODUCT.main_image : ''
const chainAddr = CHAIN_ADDR
const count = ref(1)
const amount = hasProduct ? '0.00' : 'NaN'
function copy(txt) { if (navigator.clipboard) navigator.clipboard.writeText(txt).catch(() => {}) }
function onPay() { /* 预览态不触发真实支付 */ }
</script>

<style scoped>
.os-page { background: #F7F9FC; padding-bottom: 100px; }
.os-goods { margin: 12px 17px 0; padding: 14px; display: flex; align-items: center; min-height: 84px; }
.os-gimg { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; display: block; }
.os-gname { flex: 1; padding-left: 12px; font-size: 14px; color: #212121; }
.os-stepper { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.os-op { font-size: 18px; color: #212121; cursor: pointer; width: 16px; text-align: center; }
.os-count { font-size: 14px; color: #212121; }
.os-addr { margin: 12px 17px 0; padding: 14px; }
.os-row { display: flex; align-items: center; justify-content: space-between; min-height: 38px; }
.os-k { font-size: 14px; color: #999999; }
.os-v { font-size: 14px; color: #000000; display: flex; align-items: center; }
.os-v.chain .os-copy { width: 12px; height: 12px; margin-left: 4px; cursor: pointer; }
.os-v.money { font-size: 16px; font-weight: 500; }
.os-channel { margin: 14px 17px 0; height: 52px; border-radius: 10px; background: #E5E5E5; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
.os-ch-name { font-size: 14px; color: #000000; }
.os-ch-tip { font-size: 12px; color: #4A4A4A; }
.os-footer { position: fixed; bottom: 0; left: 0; right: 0; max-width: 750px; margin: 0 auto; height: 68px; padding: 0 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: space-between; }
.os-amount { font-size: 16px; color: #212121; }
.os-pay { width: 240px; height: 44px; border-radius: 10px; background: #2B2B2B; color: #FFFFFF; font-size: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; cursor: pointer; }
</style>
