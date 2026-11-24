<template>
  <!-- P030 市场商品列表（原站 #/pages/market/marketGoodsList，无参数直访真实态）
       侦查实测：marketGoodsDetail 返回 500 An error occurred，页面渲染为无数据的空壳态。
       顶部「< -- 」，藏品大图区 loading 灰块(220x220 br20)，「限价：¥--」浮层，
       发行(红)/流通(黑)标签；Tab「寄售 求购 委托 成交动态」；表头「编号⇅ / 价格⇅」；
       中部空态图 empty-data；底部「批量购买(红) / 快捷购买(黑)」。背景 rgb(247,249,252)。 -->
  <div class="b4-mgl-page">
    <img class="b4-mgl-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-mgl-title">--</div>

    <!-- 藏品大图 loading 灰块 -->
    <div class="b4-mgl-hero">
      <div class="b4-mgl-loading"></div>
      <div class="b4-mgl-limit">限价：¥--</div>
      <div class="b4-mgl-tags">
        <span class="b4-mgl-tag issue">发行</span><span class="b4-mgl-tag-issue-v">--</span>
        <span class="b4-mgl-tag circ">流通</span><span class="b4-mgl-tag-circ-v">--</span>
      </div>
    </div>

    <!-- Tab + 表头 -->
    <div class="b4-mgl-panel">
      <div class="b4-mgl-tabs">
        <span class="t on">寄售</span><span class="t">求购</span><span class="t">委托</span><span class="t">成交动态</span>
      </div>
      <div class="b4-mgl-underline u1"></div>
      <div class="b4-mgl-th"><span>编号 <i class="sort"></i></span><span>价格 <i class="sort"></i></span></div>
      <img class="b4-mgl-empty" :src="empty" alt="" />
    </div>

    <!-- 底部按钮 -->
    <div class="b4-mgl-actions">
      <div class="b4-mgl-btn batch">批量购买</div>
      <div class="b4-mgl-btn quick">快捷购买</div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const empty = new URL('../../assets/images/empty-data.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.b4-mgl-page {
  --b4-bg: rgb(247, 249, 252);
  --b4-ink: rgb(33, 33, 33);
  --b4-mute: rgb(154, 160, 171);
  --b4-red: rgb(217, 52, 55);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-bg); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
  padding-bottom: 64px; box-sizing: border-box;
}
.b4-mgl-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; z-index: 3; }
.b4-mgl-title { position: absolute; left: 0; right: 0; top: 87px; text-align: center; font-size: 16px; font-weight: 500; color: var(--b4-ink); }
.b4-mgl-hero { position: absolute; left: 0; right: 0; top: 0; height: 436px; background: rgba(255, 255, 255, 0.04); }
.b4-mgl-loading {
  position: absolute; left: 78px; top: 120px; width: 220px; height: 220px;
  border-radius: 20px; background: rgb(245, 245, 245);
}
.b4-mgl-limit {
  position: absolute; left: 143px; top: 118px; width: 90px; height: 29px;
  border-radius: 0 0 16px 16px; background: rgba(255, 255, 255, 0.6);
  font-size: 14px; font-weight: 400; color: var(--b4-ink); text-align: center; line-height: 29px;
}
.b4-mgl-tags { position: absolute; left: 0; top: 0; width: 375px; height: 24px; }
.b4-mgl-tag, .b4-mgl-tag-issue-v, .b4-mgl-tag-circ-v { position: absolute; top: 352px; height: 24px; line-height: 24px; }
.b4-mgl-tag { font-size: 11px; color: #fff; text-align: center; }
.b4-mgl-tag.issue { left: 116px; width: 40px; background: rgb(255, 65, 68); border-radius: 4px 0 0 4px; }
.b4-mgl-tag-issue-v { left: 156px; width: 26px; background: rgb(255, 235, 235); color: var(--b4-red); font-size: 14px; text-align: center; border-radius: 0 4px 4px 0; }
.b4-mgl-tag.circ { left: 194px; width: 40px; background: rgb(33, 33, 33); border-radius: 4px 0 0 4px; }
.b4-mgl-tag-circ-v { left: 234px; width: 26px; background: rgb(205, 204, 213); color: var(--b4-ink); font-size: 14px; text-align: center; border-radius: 0 4px 4px 0; }
.b4-mgl-panel {
  position: absolute; left: 0; right: 0; top: 406px; height: 422px;
  border-radius: 30px 30px 0 0; background: var(--b4-bg);
}
.b4-mgl-tabs { position: absolute; left: 20px; top: 16px; display: flex; gap: 40px; }
.b4-mgl-tabs .t { font-size: 14px; font-weight: 400; color: var(--b4-mute); }
.b4-mgl-tabs .t.on { font-weight: 500; color: var(--b4-ink); }
.b4-mgl-underline { position: absolute; width: 20px; height: 3px; border-radius: 1px; background: var(--b4-red); }
.b4-mgl-underline.u1 { left: 24px; top: 42px; }
.b4-mgl-th {
  position: absolute; left: 20px; right: 20px; top: 65px; display: flex; justify-content: space-between;
  font-size: 12px; font-weight: 400; color: var(--b4-mute);
}
.b4-mgl-th .sort { display: inline-block; width: 10px; height: 14px; background: url('../../assets/images/market-sort.png') no-repeat center / 10px 14px; vertical-align: -3px; }
.b4-mgl-empty { position: absolute; left: 117px; top: 131px; width: 142px; height: 142px; object-fit: contain; }
.b4-mgl-actions {
  position: fixed; left: 50%; transform: translateX(-50%); bottom: 0; width: 375px; height: 64px;
  background: #fff; display: flex; align-items: center; gap: 12px; padding: 0 20px; box-sizing: border-box; z-index: 100;
}
.b4-mgl-btn { width: 162px; height: 44px; border-radius: 12px; color: #fff; font-size: 16px; font-weight: 500; text-align: center; line-height: 44px; }
.b4-mgl-btn.batch { background: var(--b4-red); }
.b4-mgl-btn.quick { background: rgb(33, 33, 33); }
</style>
