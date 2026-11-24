<template>
  <!-- P025 关注列表（原站 #/pages/market/attentionList，无参数直访真实态）
       侦查实测：顶栏「经典/关注/热榜」自定义导航，筛选栏「全部/推荐/全部」，
       2 列瀑布流 8 个藏品卡（真实快照数据），底部 TabBar 首页高亮。
       背景 rgb(244,245,245)；左卡白底、右卡深灰底。截图 2x 坐标已换算 CSS 像素。 -->
  <div class="b4-att-page">
    <!-- 自定义顶栏 -->
    <div class="b4-att-nav">
      <span class="b4-att-nav-jd">经典</span>
      <span class="b4-att-nav-gz">关注</span>
      <span class="b4-att-nav-dot"></span>
      <span class="b4-att-nav-rb">热榜</span>
    </div>

    <!-- 筛选栏 -->
    <div class="b4-att-filter">
      <div class="b4-att-filter-tab on">全部</div>
      <div class="b4-att-filter-tab">推荐</div>
      <div class="b4-att-filter-tab">全部</div>
    </div>

    <!-- 卡片瀑布流 -->
    <div
      v-for="(g, i) in goods"
      :key="i"
      class="b4-att-card"
      :class="{ dark: g.dark }"
      :style="{ top: (154 + Math.floor(i / 2) * 254) + 'px', left: (i % 2 === 0 ? 17 : 196) + 'px' }"
    >
      <img class="b4-att-img" :src="g.img" alt="" />
      <div class="b4-att-name" style="top:155px">{{ g.name }}</div>
      <span class="b4-att-fs" style="top:176px;left:15px">发售</span>
      <span class="b4-att-num" :style="{ top: '177px', left: (g.dark ? '50px' : '45px') }">{{ g.num }}</span>
      <span class="b4-att-series" style="top:199px;left:18px">{{ g.series }}</span>
      <span class="b4-att-ind" style="top:199px;left:12px"></span>
      <div class="b4-att-price" :style="{ left: (g.dark ? '83px' : '107px') }">
        <span class="b4-att-cur">￥</span><span class="b4-att-pnum">{{ g.price }}</span><span class="b4-att-qi">起</span>
      </div>
    </div>

    <!-- 底部 TabBar（首页高亮，与原站一致） -->
    <nav class="b4-att-tabbar">
      <div class="b4-att-tab">
        <img class="b4-att-tab-ic" :src="IC.home" alt="" />
        <span class="b4-att-tab-tx act">首页</span>
      </div>
      <div class="b4-att-tab">
        <img class="b4-att-tab-ic" :src="IC.act" alt="" />
        <span class="b4-att-tab-tx">活动</span>
      </div>
      <div class="b4-att-tab center">
        <img class="b4-att-tab-ic big" :src="IC.market" alt="" />
        <span class="b4-att-tab-tx">市场</span>
      </div>
      <div class="b4-att-tab">
        <img class="b4-att-tab-ic" :src="IC.discover" alt="" />
        <span class="b4-att-tab-tx">发现</span>
      </div>
      <div class="b4-att-tab">
        <img class="b4-att-tab-ic" :src="IC.mine" alt="" />
        <span class="b4-att-tab-tx">我的</span>
      </div>
    </nav>
  </div>
</template>

<script setup>
/* P025 关注列表：数据全部取自原站 2.2s 稳定态 DOM 快照（getMarketList 8 条真实藏品）。
   颜色/字号/坐标全部逐项对齐采集数据；图标沿用原站 static/huotu 资源，零 emoji。 */
const u = (n) => new URL(`../../assets/images/${n}`, import.meta.url).href
const IC = {
  home: u('tab-home-active.png'),
  act: u('tab-activity.png'),
  market: u('tab-market.png'),
  discover: u('tab-discover.png'),
  mine: u('tab-mine.png')
}
const goods = [
  { name: '弥勒-达观祥和', num: '44350份', series: '唐卡系列', price: '36', img: u('b4-goods-1.jpg'), dark: false },
  { name: '今晚吃鸡', num: '1000份', series: '唐卡系列', price: '1500', img: u('b4-goods-2.png'), dark: true },
  { name: '2026豪华舱', num: '12722份', series: '唐卡系列', price: '3', img: u('b4-goods-3.jpg'), dark: false },
  { name: '一飞冲天', num: '3288份', series: '唐卡系列', price: '9', img: u('b4-goods-4.jpg'), dark: true },
  { name: '大吉大利', num: '5000份', series: '唐卡系列', price: '50', img: u('b4-goods-5.jpg'), dark: false },
  { name: '天官赐福', num: '1500份', series: '唐卡系列', price: '50', img: u('b4-goods-6.jpg'), dark: true },
  { name: '熊小七', num: '3000份', series: '唐卡系列', price: '77', img: u('b4-goods-7.jpg'), dark: false },
  { name: '2026青花瓷-龙王', num: '2888份', series: '唐卡系列', price: '177', img: u('b4-goods-8.jpg'), dark: true }
]
</script>

<style scoped>
.b4-att-page {
  --b4-bg: rgb(244, 245, 245);
  --b4-ink: rgb(25, 25, 25);
  --b4-sub: rgb(131, 131, 131);
  --b4-mute: rgb(217, 217, 217);
  --b4-brown: rgb(132, 119, 101);
  --b4-price-dark: rgb(74, 74, 74);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-bg); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
  padding-bottom: 56px; box-sizing: border-box;
}
.b4-att-nav { position: absolute; left: 0; top: 30px; width: 375px; height: 50px; }
.b4-att-nav-jd { position: absolute; left: 17px; top: 8px; font-size: 14px; font-weight: 400; color: var(--b4-sub); }
.b4-att-nav-gz { position: absolute; left: 45px; top: 13px; font-size: 21px; font-weight: 700; line-height: 29px; color: var(--b4-ink); }
.b4-att-nav-dot { position: absolute; left: 86px; top: 19px; width: 7px; height: 7px; border-radius: 50%; background: rgb(225, 218, 199); }
.b4-att-nav-rb { position: absolute; left: 86px; top: 24px; font-size: 14px; font-weight: 400; color: var(--b4-sub); }
.b4-att-filter {
  position: absolute; left: 17px; top: 101px; width: 341px; height: 40px;
  background: #fff; border-radius: 7px; display: flex; align-items: center; padding: 0 10px; box-sizing: border-box; gap: 10px;
}
.b4-att-filter-tab {
  width: 64px; height: 31px; border-radius: 5px; background: rgb(244, 245, 245);
  font-size: 12px; font-weight: 400; color: rgb(175, 175, 175); text-align: center; line-height: 31px;
}
.b4-att-filter-tab.on { background: rgb(49, 49, 49); color: #fff; font-weight: 500; }
.b4-att-card {
  position: absolute; width: 160px; height: 235px; border-radius: 14px; background: #fff; overflow: hidden;
}
.b4-att-card.dark { background: rgb(40, 40, 40); }
.b4-att-img {
  position: absolute; left: 12px; top: 12px; width: 136px; height: 136px;
  border-radius: 8px; object-fit: cover; background: rgb(245, 245, 245);
}
.b4-att-name { position: absolute; left: 12px; font-size: 12px; font-weight: 700; color: var(--b4-ink); }
.b4-att-card.dark .b4-att-name { color: rgb(195, 180, 158); }
.b4-att-fs, .b4-att-num, .b4-att-series, .b4-att-ind { position: absolute; }
.b4-att-fs { font-size: 8px; font-weight: 400; color: var(--b4-mute); }
.b4-att-num { font-size: 8px; font-weight: 400; color: var(--b4-brown); }
.b4-att-series { font-size: 10px; font-weight: 400; color: var(--b4-brown); }
.b4-att-card.dark .b4-att-series { color: rgb(184, 184, 184); }
.b4-att-ind { width: 3px; height: 14px; border-radius: 1px; background: var(--b4-brown); }
.b4-att-card.dark .b4-att-ind { background: rgb(81, 81, 81); }
.b4-att-price {
  position: absolute; top: 194px; display: flex; align-items: baseline; color: var(--b4-ink);
}
.b4-att-card.dark .b4-att-price { color: var(--b4-price-dark); }
.b4-att-cur { font-size: 10px; font-weight: 800; line-height: 14px; }
.b4-att-pnum { font-size: 20px; font-weight: 800; line-height: 27px; font-family: 'Bebas Neue', 'Alibaba PuHuiTi', sans-serif; }
.b4-att-qi { font-size: 8px; font-weight: 400; line-height: 11px; }
.b4-att-tabbar {
  position: fixed; left: 50%; transform: translateX(-50%); bottom: 0; width: 375px; height: 56px;
  background: #fff; display: flex; z-index: 100;
}
.b4-att-tab { width: 75px; height: 55px; display: flex; flex-direction: column; align-items: center; padding-top: 8px; box-sizing: border-box; }
.b4-att-tab-ic { width: 24px; height: 24px; object-fit: contain; }
.b4-att-tab-ic.big { width: 37px; height: 37px; margin-top: -9px; }
.b4-att-tab-tx { margin-top: 3px; font-size: 10px; font-weight: 400; line-height: 14px; color: rgb(196, 200, 208); }
.b4-att-tab-tx.act { color: rgb(217, 52, 55); }
</style>
