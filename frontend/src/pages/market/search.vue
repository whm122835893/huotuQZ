<template>
  <!-- P031 市场搜索（原站 #/pages/market/search，无参数直访真实态）
       侦查实测：顶部搜索框「请输入内容」+ 返回箭头；Tab「数字藏品 / 盲盒」；
       8 个商品行（48x48 缩略图 + 名称 + 成交量 + ¥价格 + 涨跌幅标签）。
       背景 rgb(247,249,252)。涨跌幅：涨红底白字、跌绿底白字、平灰底灰字（与原站一致）。 -->
  <div class="b4-s-page">
    <img class="b4-s-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-s-search">请输入内容</div>

    <div class="b4-s-tabs">
      <span class="b4-s-tab on">数字藏品</span>
      <span class="b4-s-tab b4-s-tab2">盲盒</span>
    </div>
    <div class="b4-s-underline u1"></div>
    <div class="b4-s-sepline"></div>

    <div v-for="(g, i) in goods" :key="i" class="b4-s-row" :style="{ top: (122 + i * 76) + 'px' }">
      <img class="b4-s-thumb" :src="g.img" alt="" />
      <div class="b4-s-name">{{ g.name }}</div>
      <div class="b4-s-vol">成交量: {{ g.vol }}</div>
      <div class="b4-s-price"><span class="cur">¥</span><span class="num">{{ g.price }}</span></div>
      <div class="b4-s-chg" :class="g.cls">{{ g.chg }}</div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const u = (n) => new URL(`../../assets/images/${n}`, import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
/* 8 条商品行取自原站 getMarketList 真实快照（名称/成交量/价格/涨跌幅逐字一致） */
const goods = [
  { name: '弥勒-达观祥和', vol: 3, price: '36.00', chg: '+ 0%', cls: 'flat', img: u('b4-goods-1.jpg') },
  { name: '今晚吃鸡', vol: 4, price: '1,500.00', chg: '+ 50.00%', cls: 'up', img: u('b4-goods-2.png') },
  { name: '2026豪华舱', vol: 463, price: '3.00', chg: '+ 0%', cls: 'flat', img: u('b4-goods-3.jpg') },
  { name: '一飞冲天', vol: 16, price: '9.00', chg: '+ 0%', cls: 'flat', img: u('b4-goods-4.jpg') },
  { name: '大吉大利', vol: 2, price: '50.00', chg: '- 66.67%', cls: 'down', img: u('b4-goods-5.jpg') },
  { name: '天官赐福', vol: 5, price: '50.00', chg: '- 94.99%', cls: 'down', img: u('b4-goods-6.jpg') },
  { name: '熊小七', vol: 3, price: '77.00', chg: '- 84.60%', cls: 'down', img: u('b4-goods-7.jpg') },
  { name: '2026青花瓷-龙王', vol: 2, price: '177.00', chg: '- 64.60%', cls: 'down', img: u('b4-goods-8.jpg') }
]
</script>

<style scoped>
.b4-s-page {
  --b4-bg: rgb(247, 249, 252);
  --b4-ink: rgb(33, 33, 33);
  --b4-mute: rgb(154, 160, 171);
  --b4-up: rgb(217, 52, 55);
  --b4-down: rgb(45, 191, 106);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-bg); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-s-back { position: absolute; left: 15px; top: 15px; width: 10px; height: 18px; cursor: pointer; z-index: 2; }
.b4-s-search {
  position: absolute; left: 52px; top: 6px; width: 303px; height: 36px; border-radius: 50px;
  background: rgb(238, 241, 245); font-size: 14px; font-weight: 400; color: rgb(128, 128, 128); line-height: 36px; padding-left: 16px; box-sizing: border-box;
}
.b4-s-tabs { position: absolute; left: 20px; top: 54px; display: flex; flex-direction: column; }
.b4-s-tab { font-size: 16px; font-weight: 400; color: var(--b4-mute); line-height: 24px; }
.b4-s-tab.on { font-weight: 700; color: var(--b4-ink); }
.b4-s-tab2 { margin-left: 63px; margin-top: -1px; }
.b4-s-underline { position: absolute; width: 32px; height: 2px; border-radius: 3px; }
.b4-s-underline.u1 { left: 35px; top: 112px; background: var(--b4-up); }
.b4-s-sepline { position: absolute; left: 20px; top: 112px; width: 335px; height: 1px; background: rgb(232, 235, 240); }
.b4-s-row { position: absolute; left: 20px; width: 335px; height: 68px; border-radius: 12px; background: #fff; box-sizing: border-box; }
.b4-s-thumb { position: absolute; left: 10px; top: 10px; width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: rgb(245, 245, 245); }
.b4-s-name { position: absolute; left: 66px; top: 14px; font-size: 14px; font-weight: 700; color: var(--b4-ink); line-height: 20px; }
.b4-s-vol { position: absolute; left: 66px; top: 38px; font-size: 11px; font-weight: 400; color: var(--b4-mute); line-height: 16px; }
.b4-s-price { position: absolute; right: 98px; top: 23px; color: var(--b4-ink); }
.b4-s-price .cur { font-size: 11px; line-height: 16px; }
.b4-s-price .num { font-size: 18px; line-height: 21px; font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif; }
.b4-s-chg { position: absolute; right: 10px; top: 22px; min-width: 25px; padding: 0 6px; height: 24px; border-radius: 4px; font-size: 11px; font-weight: 400; text-align: center; line-height: 24px; box-sizing: border-box; }
.b4-s-chg.flat { background: rgb(236, 239, 243); color: rgb(154, 160, 171); right: 23px; top: 20px; }
.b4-s-chg.up { background: var(--b4-up); color: #fff; }
.b4-s-chg.down { background: var(--b4-down); color: #fff; }
</style>
