<template>
  <!-- P051 确认订单：原站无商品上下文时整页白屏（默认态）；?demo=1 预览订单结构 -->
  <div class="shop-page exchange-page">
    <template v-if="demo">
      <div class="shop-nav">
        <img class="nav-back is-dark" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
        <span class="nav-title">确认订单</span>
      </div>

      <div class="goods-row">
        <img class="g-img" :src="info.list_image" alt="" />
        <div class="g-right">
          <div class="g-name">{{ info.goods_name }}</div>
          <div class="stepper">
            <img src="../../assets/images/shop-minus-active.png" alt="减" @click="count = Math.max(1, count - 1)" />
            <span>{{ count }}</span>
            <img src="../../assets/images/shop-add-active.png" alt="加" @click="count++" />
          </div>
        </div>
      </div>

      <div class="address-box">
        <div class="addr-tip">您还未添加收货地址</div>
        <button class="addr-btn" @click="router.push('/pages/my/address/list')">立即添加</button>
      </div>

      <div class="info-box">
        <div class="info-row"><span>创作者:</span><span class="v">火兔</span></div>
        <div class="info-row">
          <span>区块链地址:</span>
          <span class="v">0x8a2****b423d
            <img class="copy" src="../../assets/images/shop-copy-dark.png" alt="复制" />
          </span>
        </div>
        <div class="info-row"><span>订单金额:</span><span class="v">{{ info.crystal_number }}</span></div>
      </div>

      <div class="pay-row">
        <span class="pay-name">{{ crystalLabel }} <em>(剩余{{ balance }})</em></span>
        <span class="pay-check"></span>
      </div>

      <div class="shop-footer">
        <span class="amount">{{ info.crystal_number }}</span>
        <button class="btn" @click="submit">立即兑换</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_INFO, CRYSTAL_LABEL } from './demo'
import './common.css'

const router = useRouter()
const demo = isDemo()
const info = DEMO_INFO
const count = ref(1)
const balance = ref(0)
const crystalLabel = computed(() => CRYSTAL_LABEL[info.crystal_type] || '黄水晶')

if (demo) loadBalance()

async function loadBalance() {
  try {
    const u = (await http.post('/api/user/getUserInfo', {})).data || {}
    const key = `crystal_${info.crystal_type}`
    balance.value = u[key] || 0
  } catch (e) { balance.value = 0 }
}

async function submit() {
  if (!demo) return
  try {
    await http.post('/api/crystal_goods/exchange', {
      goods_id: info.id, number: count.value, address_id: 0, pay_password: ''
    })
  } catch (e) { /* 预览态不依赖真实下单 */ }
}

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.exchange-page { background: #FFFFFF; min-height: 100vh; }
.goods-row { display: flex; align-items: center; padding: 16px 20px 8px; }
.g-img { width: 76px; height: 76px; border-radius: 8px; background: #F5F5F5; object-fit: cover; }
.g-right { flex: 1; padding-left: 16px; }
.g-name { font-size: 15px; color: #212121; }
.stepper { display: flex; align-items: center; gap: 10px; margin-top: 18px; }
.stepper img { width: 20px; height: 20px; cursor: pointer; }
.stepper span { font-size: 14px; color: #212121; min-width: 20px; text-align: center; }
.address-box { padding: 24px 0 8px; text-align: center; }
.addr-tip { font-size: 14px; color: #787F8D; }
.addr-btn {
  display: block; width: 240px; height: 32px; margin: 16px auto 0;
  background: #F7F7F7; border: none; border-radius: 6px;
  font-size: 13px; font-weight: 600; color: #212121; cursor: pointer;
}
.info-box { margin: 24px 20px 0; padding: 16px 0; border-top: 1px solid #F2F2F2; border-bottom: 1px solid #F2F2F2; }
.info-row { display: flex; align-items: center; justify-content: space-between; height: 34px; font-size: 14px; color: #787F8D; }
.info-row .v { display: flex; align-items: center; gap: 6px; color: #212121; }
.copy { width: 14px; height: 14px; }
.pay-row { display: flex; align-items: center; justify-content: space-between; margin: 28px 20px 0; font-size: 14px; color: #212121; }
.pay-name em { font-style: normal; color: #787F8D; }
.pay-check { width: 18px; height: 18px; border-radius: 50%; background: #FF7A00; position: relative; }
.pay-check::after {
  content: ''; position: absolute; left: 5px; top: 4px; width: 5px; height: 8px;
  border: solid #FFFFFF; border-width: 0 2px 2px 0; transform: rotate(45deg);
}
</style>
