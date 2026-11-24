<template>
  <!-- P063 赠送订单详情：原站无对应订单时退回首页；?demo=1 展示结构 -->
  <DetailFrame
    status-text="空投转入成功" panel-title="订单信息"
    :goods-image="image" :goods-name="info.goods_name || ''" :num="info.num || 1"
    :price="fmtPrice(info.price)" :asset-code="assetCode" :rows="rows" :total="fmtPrice(info.total_price)"
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
const image = computed(() => info.value.local_image || info.value.goods_cover_image || '')
const assetCode = computed(() => {
  const c = info.value.collection_code
  return Array.isArray(c) ? (c[0] || '') : (c || '')
})
const rows = computed(() => {
  const o = info.value
  return [
    { k: '订单编号', v: o.order_sn || '', copy: true },
    { k: '创建订单', v: o.create_text || '' },
    { k: '支付单号', v: '--' },
    { k: '完成付款', v: o.create_text || '' },
    { k: '开始转入', v: o.create_text || '' },
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

const id = route.query.id || (demo ? REAL_ORDER.id : '')
if (!id) { setTimeout(() => router.replace('/'), 1500) }
else if (demo) { info.value = { ...REAL_ORDER, local_image: REAL_ORDER.local_image, create_text: REAL_ORDER.createtime_text } }
else {
  http.post(`/api/order/order/giveOrderDetail/ids/${id}`, {}).then((res) => {
    const o = res.data || {}
    info.value = { ...o, create_text: fmt(o.createtime) }
  }).catch(() => { setTimeout(() => router.replace('/'), 1500) })
}
</script>
