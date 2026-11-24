<template>
  <!-- 求购/委托详情共享骨架：盾牌头 + 商品卡 + 订单详情卡 + 状态时间轴 + 双按钮 -->
  <div class="ord-page td-page">
    <OrderNav :title="navTitle" />
    <div class="det-status">
      <img class="det-shield" src="../../assets/images/order-completed.png" alt="" />
      <span class="det-status-text">{{ statusText }}</span>
    </div>

    <div class="ord-panel td-goods" @click="$emit('goods')">
      <img class="td-gimg" :src="goodsImage" alt="" />
      <div class="td-gname">{{ goodsName }}<span class="td-gnum">x{{ num }}</span></div>
      <img class="td-arrow" src="../../assets/images/icon-right.png" alt="" />
    </div>

    <div class="ord-panel td-card">
      <div class="td-title">订单详情</div>
      <div class="td-row"><span class="td-k">订单金额:</span><span class="td-money"><b>￥</b> {{ price }}</span></div>
      <div class="td-row"><span class="td-k">订单ID号:</span><span class="td-v">{{ orderSn }} <em @click.stop="copy(orderSn)">复制</em></span></div>
      <div class="td-row"><span class="td-k">分账ID号:</span><span class="td-v">{{ orderSn }} <em @click.stop="copy(orderSn)">复制</em></span></div>
      <div class="td-row"><span class="td-k">支付方式:</span><span class="td-v">{{ payWay }}</span></div>
    </div>

    <div class="ord-panel td-card">
      <div class="td-title">订单状态</div>
      <div class="td-step"><i></i>等待支付</div>
      <div class="td-step"><i></i>支付成功,转入中</div>
      <div class="td-step on"><i></i>转入成功 <em>（{{ timeText }}）</em></div>
    </div>

    <div class="td-footer">
      <div class="td-btn ghost" @click="$emit('cancel')">取消订单</div>
      <div class="td-btn dark" @click="$emit('continue')">{{ continueText }}</div>
    </div>
  </div>
</template>

<script setup>
import OrderNav from './OrderNav.vue'
import './common.css'
defineProps({
  navTitle: { type: String, default: '订单详情' },
  statusText: { type: String, default: '空投转入成功' },
  goodsImage: { type: String, default: '' },
  goodsName: { type: String, default: '' },
  num: { type: [Number, String], default: 1 },
  price: { type: String, default: '0.00' },
  orderSn: { type: String, default: '' },
  payWay: { type: [Number, String], default: 1 },
  timeText: { type: String, default: '' },
  continueText: { type: String, default: '继续求购' },
})
defineEmits(['goods', 'cancel', 'continue'])
function copy(txt) { if (navigator.clipboard) navigator.clipboard.writeText(txt).catch(() => {}) }
</script>

<style scoped>
.td-page { background: #F7F9FC; padding-bottom: 90px; }
.det-status { display: flex; align-items: center; gap: 22px; padding: 12px 0 12px 20px; }
.det-shield { width: 90px; height: 90px; display: block; }
.det-status-text { font-size: 18px; font-weight: 700; color: #212121; }
.td-goods { margin: 0 17px; padding: 14px; display: flex; align-items: center; }
.td-gimg { width: 66px; height: 66px; border-radius: 8px; object-fit: cover; background: #F5F5F5; display: block; }
.td-gname { flex: 1; padding-left: 12px; font-size: 16px; font-weight: 700; color: #212121; }
.td-gnum { display: block; font-size: 14px; font-weight: 400; margin-top: 4px; }
.td-arrow { width: 8px; height: 14px; }
.td-card { margin: 14px 17px 0; padding: 16px 17px; }
.td-title { font-size: 14px; font-weight: 700; color: #212121; margin-bottom: 14px; }
.td-row { display: flex; align-items: center; min-height: 40px; }
.td-k { width: 88px; font-size: 13px; color: #636363; }
.td-money { flex: 1; text-align: right; font-size: 14px; color: #191919; }
.td-money b { font-weight: 400; }
.td-v { flex: 1; text-align: right; font-size: 13px; color: #212121; word-break: break-all; }
.td-v em { font-style: normal; color: #9AA0AB; margin-left: 4px; cursor: pointer; }
.td-step { display: flex; align-items: center; font-size: 13px; color: #B5B5B5; line-height: 32px; }
.td-step i { width: 6px; height: 6px; border-radius: 50%; background: #D8D8D8; margin-right: 10px; }
.td-step.on { color: #212121; font-weight: 700; }
.td-step.on i { background: #212121; }
.td-step em { font-style: normal; color: #9AA0AB; font-weight: 400; font-size: 12px; }
.td-footer {
  position: fixed; bottom: 0; left: 0; right: 0; max-width: 750px; margin: 0 auto;
  height: 78px; padding: 13px 16px; display: flex; gap: 10px; background: #FFFFFF;
}
.td-btn { flex: 1; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; cursor: pointer; }
.td-btn.ghost { background: #E8DDD0; color: #9C9C9C; }
.td-btn.dark { background: #2B2B2B; color: #FFFFFF; }
</style>
