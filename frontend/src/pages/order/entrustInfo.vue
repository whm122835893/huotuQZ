<template>
  <!-- P057 委托详情：与求购详情同构，按钮文案为「继续委托」 -->
  <TradeDetail
    v-if="hasData"
    :goods-image="image" :goods-name="goodsName" :num="num"
    :price="price" :order-sn="orderSn" :time-text="timeText"
    continue-text="继续委托"
    @continue="router.push('/pages/market/submitEntrust')"
  />
  <div v-else class="ord-page">
    <OrderNav title="订单详情" />
    <div class="bi-shield-wrap"><img class="bi-shield" src="../../assets/images/order-completed.png" alt="" /></div>
    <div class="ord-panel bi-card">
      <div class="bi-title">订单详情</div>
      <div class="bi-row"><span>订单金额:</span><b>￥</b></div>
      <div class="bi-row"><span>订单ID号:</span></div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import http from '../../api/request'
import OrderNav from './OrderNav.vue'
import TradeDetail from './TradeDetail.vue'
import { isDemo, REAL_ORDER } from './demo'

const router = useRouter()
const route = useRoute()
const hasData = ref(false)
const image = ref('')
const goodsName = ref('')
const num = ref(1)
const price = ref('0.00')
const orderSn = ref('')
const timeText = ref('')

const demo = isDemo()
const id = route.query.id || (demo ? REAL_ORDER.id : '')
if (demo && id) {
  hasData.value = true
  image.value = REAL_ORDER.local_image
  goodsName.value = REAL_ORDER.goods_name
  price.value = REAL_ORDER.total_price
  orderSn.value = REAL_ORDER.order_sn
  timeText.value = REAL_ORDER.createtime_text
} else if (id) {
  http.post('/api/market/Prepayment/myOrderInfo', { id }).then((res) => {
    if (res.code === 1 && res.data) {
      const o = res.data
      hasData.value = true
      goodsName.value = o.goods_name || ''
      price.value = o.total_price || o.price || '0.00'
      orderSn.value = o.order_sn || ''
      num.value = o.num || 1
    }
  }).catch(() => {})
}
</script>

<style scoped>
.bi-shield-wrap { padding: 12px 0 12px 20px; }
.bi-shield { width: 90px; height: 90px; display: block; }
.bi-card { margin: 0 17px; padding: 16px 17px 22px; }
.bi-title { font-size: 14px; font-weight: 700; color: #212121; margin-bottom: 16px; }
.bi-row { display: flex; align-items: center; justify-content: space-between; min-height: 40px; font-size: 13px; color: #636363; }
.bi-row b { font-weight: 400; color: #191919; font-size: 14px; }
</style>
