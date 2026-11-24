<template>
  <!-- 订单详情页共享骨架：盾牌状态头 + 白卡(商品行/信息行) + 底部按钮 -->
  <div class="ord-page det-page">
    <OrderNav :title="navTitle" />
    <div class="det-status">
      <img class="det-shield" src="../../assets/images/order-completed.png" alt="" />
      <span class="det-status-text">{{ statusText }}</span>
    </div>

    <div class="ord-panel det-panel">
      <div class="det-title">{{ panelTitle }}</div>
      <div class="det-goods">
        <img class="det-gimg" :src="goodsImage" alt="" />
        <div class="det-gname">{{ goodsName }}<span class="det-gnum">×{{ num }}</span></div>
        <div class="det-price"><span>单价</span><b>¥</b><strong>{{ price }}</strong></div>
      </div>
      <div v-if="assetCode" class="det-asset">
        <div class="det-asset-label">资产编号</div>
        <div class="det-asset-code">#{{ assetCode }}</div>
      </div>
      <div v-for="(r, i) in rows" :key="i" class="det-row">
        <span class="det-row-k">{{ r.k }}</span>
        <span class="det-row-v">
          {{ r.v }}
          <img v-if="r.copy" class="det-copy" src="../../assets/images/order-icon-copy.png" alt="复制" @click="copy(r.v)" />
        </span>
      </div>
      <div v-if="total !== null" class="det-total">
        实付 <b>¥</b><strong>{{ total }}</strong>
      </div>
    </div>

    <div v-if="statusBlock" class="ord-panel det-status-block">
      <div class="det-title">订单状态</div>
      <div v-for="(s, i) in statusBlock.steps" :key="i" class="det-step" :class="{ on: s.on }">
        <i></i><span>{{ s.t }}</span><em v-if="s.time">（{{ s.time }}）</em>
      </div>
    </div>

    <div class="det-footer">
      <div v-if="secondaryBtn" class="det-btn ghost" @click="$emit('secondary')">{{ secondaryBtn }}</div>
      <div class="det-btn" :class="primaryClass" @click="$emit('primary')">{{ primaryBtn }}</div>
    </div>
  </div>
</template>

<script setup>
import OrderNav from './OrderNav.vue'
import './common.css'

defineProps({
  navTitle: { type: String, default: '订单详情' },
  statusText: { type: String, default: '空投转入成功' },
  panelTitle: { type: String, default: '订单信息' },
  goodsImage: { type: String, default: '' },
  goodsName: { type: String, default: '' },
  num: { type: [Number, String], default: 1 },
  price: { type: String, default: '0.00' },
  assetCode: { type: String, default: '' },
  rows: { type: Array, default: () => [] },
  total: { type: String, default: null },
  statusBlock: { type: Object, default: null },
  secondaryBtn: { type: String, default: '' },
  primaryBtn: { type: String, default: '继续购买' },
  primaryClass: { type: String, default: 'red' },
})
defineEmits(['primary', 'secondary'])
function copy(txt) { if (navigator.clipboard) navigator.clipboard.writeText(txt).catch(() => {}) }
</script>

<style scoped>
.det-page { background: #F7F9FC; padding-bottom: 90px; }
.det-status { position: relative; height: 114px; }
.det-shield { position: absolute; left: 20px; top: 12px; width: 90px; height: 90px; display: block; }
.det-status-text { position: absolute; left: 118px; top: 44px; font-size: 18px; font-weight: 700; color: #212121; line-height: 25px; }
.det-panel { margin: 0 20px; padding: 16px 14px 18px; }
.det-title { font-size: 14px; font-weight: 500; color: #212121; margin-bottom: 14px; }
.det-goods { display: flex; align-items: flex-start; }
.det-gimg { width: 54px; height: 54px; border-radius: 6px; object-fit: cover; background: #F5F5F5; display: block; }
.det-gname { flex: 1; padding-left: 8px; font-size: 14px; color: #212121; line-height: 20px; }
.det-gnum { display: block; font-size: 11px; color: #9AA0AB; margin-top: 2px; }
.det-price { width: 62px; text-align: right; }
.det-price span { display: block; font-size: 11px; color: #9AA0AB; }
.det-price b { font-size: 11px; font-weight: 400; color: #212121; }
.det-price strong { font-size: 16px; font-weight: 400; color: #212121; }
.det-asset { margin-top: 12px; }
.det-asset-label { font-size: 12px; color: #9AA0AB; }
.det-asset-code { display: inline-block; margin-top: 8px; padding: 5px 8px; border-radius: 4px; background: #F5F5F5; font-size: 12px; color: #212121; }
.det-row { display: flex; align-items: center; justify-content: space-between; min-height: 27px; }
.det-row-k { font-size: 12px; color: #9AA0AB; }
.det-row-v { font-size: 12px; color: #212121; display: flex; align-items: center; }
.det-copy { width: 12px; height: 12px; margin-left: 4px; cursor: pointer; }
.det-total { text-align: right; font-size: 12px; color: #212121; margin-top: 10px; }
.det-total b { font-weight: 400; font-size: 12px; }
.det-total strong { font-size: 20px; font-weight: 400; }
.det-status-block { margin: 14px 20px 0; padding: 20px 14px; }
.det-step { display: flex; align-items: center; font-size: 13px; color: #B5B5B5; line-height: 30px; }
.det-step i { width: 6px; height: 6px; border-radius: 50%; background: #D8D8D8; margin-right: 10px; }
.det-step.on { color: #212121; font-weight: 700; }
.det-step.on i { background: #212121; }
.det-step em { font-style: normal; color: #9AA0AB; font-weight: 400; font-size: 12px; }
.det-footer {
  position: fixed; bottom: 0; left: 0; right: 0; max-width: 750px; margin: 0 auto;
  height: 70px; padding: 13px 16px; display: flex; gap: 10px; background: #FFFFFF;
}
.det-btn {
  flex: 1; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
  font-size: 15px; cursor: pointer; background: #2B2B2B; color: #FFFFFF;
}
.det-btn.red { background: #D93437; }
.det-btn.ghost { flex: 0 0 130px; background: #FFFFFF; color: #4A4A4A; border: 1px solid #4A4A4A; }
</style>
