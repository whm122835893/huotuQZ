<template>
  <!-- P069 寄售下单表单：无 id 时原站整页白屏（如实还原） -->
  <div class="cs-page cg-page">
    <template v-if="ready">
      <div class="cs-nav">
        <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
        <i class="nav-title">寄售</i>
      </div>

      <!-- 卡片1：藏品信息 + 寄售价格输入 -->
      <div class="cs-panel cg-card1">
        <div class="cg-goods">
          <div class="cg-thumb"><img :src="heroImg" alt="" /></div>
          <div class="cg-goods-info">
            <i class="cg-name">{{ product.name }}</i>
            <div class="cg-buy"><i>买入价</i> <i class="cg-yen">¥</i> {{ buyPrice }}</div>
          </div>
        </div>
        <div class="cg-price-label">
          <i>寄售价格</i>
          <img class="cg-q" src="../../assets/images/consign-q.png" alt="?" @click="showTips" />
        </div>
        <div class="cg-price-input">
          <i class="cg-yen2">￥</i>
          <input v-model="price" class="cg-field" type="number" placeholder="请输入寄售价格" @input="onPrice" />
        </div>
      </div>

      <!-- 卡片2：收款账号 -->
      <div class="cs-panel cg-card2">
        <div class="cg-sec-title">收款账号</div>
        <div class="cg-account">
          <div class="cg-acc-left">
            <span class="cg-acc-badge">A</span>
            <i class="cg-acc-name">汇付钱包</i>
          </div>
          <svg class="cs-check" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="10" fill="#D93437" />
            <path d="M5.5 10.2l3 3 6-6.4" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </div>

      <!-- 卡片3：预计收入 -->
      <div class="cs-panel cg-card3">
        <div class="cg-sec-title">预计收入</div>
        <div class="cg-line">
          <i class="cg-line-key">综合服务费 ({{ ratePct }}%)</i>
          <i class="cg-line-val">- ¥{{ servicePrice }}</i>
        </div>
        <div class="cg-line">
          <i class="cg-line-key">预计收益</i>
          <i class="cg-line-val cg-income">¥{{ realPrice }}</i>
        </div>
      </div>

      <!-- 底部确认按钮 -->
      <div class="cg-footer">
        <div class="cg-btn" :class="{ on: hasPrice }" @click="onConfirm"><i>确认寄售</i></div>
      </div>

      <!-- 规则说明 / 校验提示（toast 同构） -->
      <div v-if="toast" class="cg-toast">{{ toast }}</div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, localImg, DEMO_DETAIL, DEMO_RATE } from './demo'
import './common.css'

const router = useRouter()
const q = routeQuery()
const id = q.get('id') || ''
// 原站：无 id → 白屏（onLoad 里 id.split 报错）。如实还原。
const ready = ref(!!id || isDemo())

const product = ref({ name: '', main_image: '', reprice_limit: '0.00', platform_resale_rate: 6 })
const buyPrice = ref('')
const price = ref('')
const rate = ref(0.06)
const servicePrice = ref('0.00')
const realPrice = ref('0.00')
const toast = ref('')

const heroImg = computed(() => localImg(product.value.main_image))
const ratePct = computed(() => Math.round(rate.value * 100))
const hasPrice = computed(() => price.value !== '' && Number(price.value) > 0)

function goBack() { router.back() }

async function loadDetail() {
  if (isDemo() || !id) {
    product.value = (DEMO_DETAIL[id] || DEMO_DETAIL['2643699']).product
    rate.value = 0.06
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/detail/ids/' + encodeURIComponent(id), {})
    if (res.code === 1 && res.data) {
      product.value = res.data.product || product.value
      buyPrice.value = res.data.from_price || ''
      rate.value = Number((res.data.product.platform_resale_rate || 6)) / 100
    }
  } catch (e) { /* 保留占位 */ }
}

// 输入价格 → 调 show_resale_money 实时试算（原站实测）
let timer = null
function onPrice() {
  clearTimeout(timer)
  const v = Number(price.value)
  if (!v || v <= 0) { servicePrice.value = '0.00'; realPrice.value = '0.00'; return }
  timer = setTimeout(() => calc(v), 250)
}
async function calc(v) {
  if (isDemo() || !id) {
    servicePrice.value = (Math.floor(v * 0.06 * 100) / 100).toFixed(2)
    realPrice.value = (v - Number(servicePrice.value)).toFixed(2)
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/show_resale_money',
      { price: v, user_collection_id: id.split(',')[0] })
    if (res.code === 1 && res.data) {
      servicePrice.value = res.data.platform_service_price
      realPrice.value = res.data.real_price
    }
  } catch (e) { /* 保留 */ }
}

function flash(msg) { toast.value = msg; setTimeout(() => { toast.value = '' }, 2200) }
function showTips() { flash(DEMO_RATE.tips) }

function onConfirm() {
  // 原站实测：未输入价格 toast「请输入寄售价格」；超过上限 toast「…不得高于X元」
  if (!hasPrice.value) { flash('请输入寄售价格'); return }
  const limit = Number(product.value.reprice_limit || 0)
  if (limit > 0 && Number(price.value) > limit) {
    flash('该数字藏品寄售价格不得高于' + limit.toFixed(2) + '元')
    return
  }
  flash('请稍后...')
}

loadDetail()
</script>

<style scoped>
.cg-page { background: var(--cs-bg); }
/* 卡片1 原站 y=56 h=195：16(顶) + 75(图) + 16 + 20(标签) + 8 + 44(输入) + 16(底) */
.cg-card1 { padding: 16px 12px; margin-top: 12px; }
.cg-goods { display: flex; align-items: center; height: 75px; }
.cg-thumb { width: 75px; height: 75px; border-radius: 8px; background: #F2F4F7; overflow: hidden; flex: none; }
.cg-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.cg-goods-info { margin-left: 12px; }
.cg-name { font-size: 14px; font-weight: 500; color: var(--cs-ink); font-style: normal; line-height: 20px; display: block; }
.cg-buy { margin-top: 6px; font-size: 12px; color: var(--cs-sub); }
.cg-buy i { font-style: normal; }
.cg-yen { font-style: normal; color: var(--cs-ink); font-size: 14px; }

.cg-price-label { display: flex; align-items: center; margin: 16px 0 8px; height: 20px; }
.cg-price-label i { font-style: normal; font-size: 14px; font-weight: 500; color: var(--cs-ink); }
.cg-q { width: 12px; height: 12px; margin-left: 4px; cursor: pointer; }
.cg-price-input {
  height: 44px; background: var(--cs-input); border-radius: 8px;
  display: flex; align-items: center; padding: 0 12px;
}
.cg-yen2 { font-style: normal; font-size: 16px; color: var(--cs-ink); margin-right: 8px; }
.cg-field { flex: 1; border: none; outline: none; background: transparent; font-size: 14px; color: var(--cs-ink); }
.cg-field::placeholder { color: var(--cs-sub); font-size: 14px; }

/* 卡片2 原站 y=263 h=105 */
.cg-card2 { margin-top: 12px; padding: 12px; height: 105px; }
.cg-sec-title { font-size: 14px; font-weight: 500; color: var(--cs-black); line-height: 21px; }
.cg-account { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; height: 48px; border-radius: 8px; padding: 0 8px; }
.cg-acc-left { display: flex; align-items: center; }
.cg-acc-badge { width: 20px; height: 20px; border-radius: 50%; background: var(--cs-red); color: #F8F3FD; font-size: 11px; display: flex; align-items: center; justify-content: center; }
.cg-acc-name { font-style: normal; font-size: 14px; color: var(--cs-black); margin-left: 8px; }

/* 卡片3 原站 y=392 h=110（卡片2 到 368，间隔 24） */
.cg-card3 { margin-top: 24px; padding: 16px 12px; height: 110px; }
.cg-line { display: flex; align-items: center; justify-content: space-between; height: 17px; }
.cg-sec-title + .cg-line { margin-top: 12px; }
.cg-line + .cg-line { margin-top: 10px; }
.cg-line-key { font-style: normal; font-size: 12px; color: #787F8D; }
.cg-line-val { font-style: normal; font-size: 14px; color: var(--cs-ink); }
.cg-income { font-size: 18px; color: var(--cs-red); }

/* 底部按钮 原站 y=751 h=52，距底 9px */
.cg-footer { position: fixed; left: 0; right: 0; bottom: 0; height: 70px; display: flex; align-items: center; justify-content: center; }
.cg-btn {
  width: 343px; max-width: calc(100% - 32px); height: 52px; border-radius: 14px;
  background: #C2C5CC; display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.cg-btn.on { background: var(--cs-red); }
.cg-btn i { font-style: normal; font-size: 16px; font-weight: 500; color: #FFFFFF; }

.cg-toast {
  position: fixed; left: 50%; bottom: 120px; transform: translateX(-50%);
  max-width: 300px; padding: 10px 16px; background: rgba(0,0,0,0.8); border-radius: 8px;
  font-size: 13px; color: #FFFFFF; line-height: 18px; text-align: center; z-index: 99;
}
</style>
