<template>
  <div class="rk-page">
    <RankTabs :tabs="NAV.hotTabs" :active="NAV.hotActive" :offset="14" />

    <div class="rk-hot-list">
      <div
        v-for="(g, i) in list"
        :key="g.id"
        class="rk-card"
        :class="i < 3 ? 'rk-card--top' : 'rk-card--plain'"
      >
        <div class="rk-card-rank">{{ i < 3 ? '' : i + 1 }}</div>
        <img class="rk-card-img" :src="localHotImg(i)" :alt="g.name" />
        <div class="rk-card-main">
          <span class="rk-card-name" :style="titleStyle(i)">{{ g.name }}</span>
          <span class="rk-card-stats" :style="{ color: statsColor(i) }">发行 {{ g.total_num }} | 流通 {{ g.flux }}</span>
          <span class="rk-card-deal" :style="dealStyle(i)"><i class="rk-deal-ic"></i>成交: {{ g.hotnum }}</span>
        </div>
        <div class="rk-card-price" :style="{ color: priceColor(i) }">
          <span class="rk-price-sym">¥</span><span class="rk-price-num">{{ formatPrice(g.min_price) }}</span><span class="rk-price-suffix">起</span>
        </div>
      </div>
    </div>

    <RankTabBar />
  </div>
</template>

<script setup>
/* P091 热榜：原站 marketrebang → code:1 + 6 条真实榜单。
   前三名 rank-bg-0*.png 原站 404 破图 → 透明底（露出页面 rgb(244,245,245)），
   文字/价格按 chunk getTitleStyle/getStatsStyle/getPriceColor 逐名次上色；
   第 4-6 名白卡 + 左侧排名数字 4/5/6。
   本项目以专用端点 /api/rank/hotList 返回同一快照，不动已交付的首页
   /api/market/market/marketrebang（返回 1 条演示数据）。 */
import { ref, onMounted } from 'vue'
import http from '../../api/request'
import RankTabs from './RankTabs.vue'
import RankTabBar from './RankTabBar.vue'
import { NAV, HOT_SNAPSHOT, HOT_STYLE, localHotImg, formatPrice } from './demo'
import './common.css'

const list = ref(HOT_SNAPSHOT)

function key(i) { return i < 3 ? i + 1 : 'd' }
function titleStyle(i) { const s = HOT_STYLE.title[key(i)]; return { fontSize: s.fs, color: s.color } }
function statsColor(i) { return HOT_STYLE.stats[key(i)] }
function priceColor(i) { return HOT_STYLE.price[key(i)] }
function dealStyle(i) {
  return {
    background: HOT_STYLE.dealBg[i < 3 ? 'top' : 'd'],
    color: HOT_STYLE.dealText
  }
}

async function loadHot() {
  try {
    const r = await http.post('/api/rank/hotList', {})
    if (r && r.code === 1 && Array.isArray(r.data) && r.data.length) list.value = r.data
  } catch (e) { /* 保留本地快照 */ }
}

onMounted(loadHot)
</script>
