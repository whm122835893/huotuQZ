<template>
  <!-- P056 订单详情（常规）：真实数据来自 /api/order/order/orderDetail/ids/{id} -->
  <DetailFrame
    :status-text="statusText" :goods-image="image" :goods-name="info.goods_name || ''"
    :num="info.num || 1" :price="fmtPrice(info.price)" :asset-code="assetCode"
    :rows="rows" :total="fmtPrice(info.total_price)"
    secondary-btn="我的资产" primary-btn="继续购买"
    @primary="router.push('/')" @secondary="router.push('/pages/myCollection/myCollection')"
  />
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import http from '../../api/request'
import DetailFrame from './DetailFrame.vue'
import { isDemo, REAL_ORDER } from './demo'

const router = useRouter()
const route = useRoute()
const info = ref({})
const demo = isDemo()

const statusText = computed(() => {
  const st = Number(info.value.order_status)
  if (st === 2) return '空投转入成功'
  if (st === 1) return '等待支付'
  return '订单已完成'
})
const image = computed(() => info.value.local_image || info.value.goods_cover_image || '')
const assetCode = computed(() => {
  const c = info.value.collection_code
  if (Array.isArray(c)) return c[0] || ''
  return c || ''
})
const rows = computed(() => {
  const o = info.value
  return [
    { k: '订单编号', v: o.order_sn || '', copy: true },
    { k: '创建订单', v: o.create_text || '' },
    { k: '支付单号', v: o.pay_sn || '--' },
    { k: '完成付款', v: o.pay_time_text || '--' },
    { k: '开始转入', v: '--' },
    { k: '转入成功', v: o.create_text || '' },
    { k: '完成时间', v: o.create_text || '' },
  ]
})

function fmtPrice(v) { return v === undefined || v === null ? '0.00' : String(v) }
function fmt(ts) {
  const d = new Date((Number(ts) || 0) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}
function localImg(path) {
  if (!path) return ''
  if (path.includes('4ba378fee1beb93954cd5401ee9385b6')) return new URL('../../assets/images/order-goods-tuer.jpg', import.meta.url).href
  if (path.includes('88748beb27fa6100fa3346938cb8edd0')) return new URL('../../assets/images/order-goods-dengjipai.jpg', import.meta.url).href
  if (path.includes('27a8d4c90fbc3dc59b26092839f0a680')) return new URL('../../assets/images/order-goods-chaoku.jpg', import.meta.url).href
  return path
}

const id = route.query.id || (demo ? REAL_ORDER.id : '')
if (!id) { setTimeout(() => router.replace('/'), 1500) }
else if (demo) {
  info.value = { ...REAL_ORDER, create_text: REAL_ORDER.createtime_text }
} else {
  load(id)
}
async function load(i) {
  try {
    const res = await http.post(`/api/order/order/orderDetail/ids/${i}`, {})
    const o = res.data || {}
    info.value = {
      ...o,
      local_image: localImg(o.goods_cover_image),
      create_text: fmt(o.createtime),
      pay_time_text: o.pay_time ? fmt(o.pay_time) : '--',
    }
  } catch (e) { info.value = {} }
}
</script>
