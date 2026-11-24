<template>
  <!-- P053 龙晶商城：原站实测 横幅375x255 / 我的龙晶40px / 双入口白卡309x64 r11 / 空态142 -->
  <div class="shop-page shop-index">
    <div class="shop-nav is-white">
      <img class="nav-back" src="../../assets/images/back_back_white.png" alt="返回" @click="goBack" />
      <span class="nav-title">龙晶商城</span>
    </div>

    <div class="banner">
      <div class="my-row">
        <img class="my-icon" src="../../assets/images/shop-lj-wode.png" alt="" />
        <span class="my-label">我的龙晶</span>
      </div>
      <div class="my-count">{{ crystal }}</div>
      <div class="rule-box" @click="goRule">龙晶规则</div>
    </div>

    <div class="entry-card">
      <div class="entry" @click="router.push('/pages/shop/record')">
        <img src="../../assets/images/shop-lj-huoqu.png" alt="" />
        <span>兑换记录</span>
      </div>
      <div class="entry" @click="router.push('/pages/shop/detailList')">
        <img src="../../assets/images/shop-lj-xiangqing.png" alt="" />
        <span>龙晶详情</span>
      </div>
    </div>

    <!-- 有数据：两列商品卡；无数据：原站同款空态 -->
    <div v-if="goods.length" class="goods-list">
      <div v-for="g in goods" :key="g.id" class="goods-item" @click="toDetail(g)">
        <div class="goods-img" :style="{ backgroundImage: `url(${g.list_image})` }">
          <div class="goods-status">{{ statusText(g) }}</div>
          <div class="goods-remain">剩余 {{ g.stock }}</div>
        </div>
        <div class="goods-name">{{ g.goods_name }}</div>
        <div class="goods-meta">
          <span class="tag-limited">限量</span>
          <span class="limited-num">{{ g.limited }}份</span>
          <span class="goods-price">{{ g.crystal_number }}</span>
        </div>
      </div>
      <div class="shop-nomore" style="flex-basis:100%">已经到底了~</div>
    </div>
    <div v-else class="shop-empty empty-pos">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>暂无数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_GOODS } from './demo'
import './common.css'

const router = useRouter()
const crystal = ref(0)
const goods = ref([])

if (isDemo()) {
  goods.value = DEMO_GOODS
} else {
  loadGoods()
  loadBalance()
}

async function loadBalance() {
  try {
    const res = await http.post('/api/user/getUserInfo', {})
    const u = res.data || {}
    crystal.value = (u.crystal_yellow || 0) + (u.crystal_purple || 0) + (u.crystal_blue || 0)
  } catch (e) { crystal.value = 0 }
}

async function loadGoods() {
  try {
    const res = await http.post('/api/crystal_goods/list', { type: '10', page: 1, crystal_type: '' })
    goods.value = res.data?.data || []
  } catch (e) { goods.value = [] }
}

function statusText(g) {
  const m = { 0: '敬请期待', 1: '倒计时', 2: '兑换中', 4: '已兑完', 5: '水晶不足' }
  return m[g.exchange_status] || '兑换中'
}

function toDetail(g) {
  const t = Number(g.type)
  if (t === 20) router.push(`/pages/shop/detail/box?id=${g.id}&blind_box_id=${g.id}`)
  else if (t === 30) router.push(`/pages/shop/detail/goods?id=${g.id}`)
  else router.push(`/pages/shop/detail/collection?id=${g.id}&collection_id=${g.id}&batch_id=${g.id}`)
}

function goRule() { router.push('/pages/rules/rules?type=3') }
function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.shop-index { background: #F5F5F5; padding-bottom: 40px; }
.banner {
  position: relative;
  width: 375px; height: 255px;
  background: url('../../assets/images/shop-lj-bg.png') no-repeat 0 0 / 100% 100%;
  overflow: hidden;
}
.my-row { position: absolute; top: 112px; left: 33px; display: flex; align-items: center; height: 22px; }
.my-icon { width: 21px; height: 22px; display: block; }
.my-label { font-size: 15px; color: #FFFFFF; line-height: 16px; }
.my-count { position: absolute; top: 134px; left: 33px; width: 309px; height: 47px; font-size: 40px; line-height: 47px; color: #FFFFFF; }
.rule-box {
  position: absolute; top: 122px; right: 0;
  width: 29px; height: 82px;
  background: rgba(0, 0, 0, .52);
  border-radius: 8px 0 0 8px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
}
.rule-box { writing-mode: vertical-rl; font-size: 12px; color: #FFFFFF; letter-spacing: 0; }
.entry-card {
  position: relative;
  width: 309px; height: 64px;
  margin: -58px auto 0;
  background: #FFFFFF; border-radius: 11px;
  display: flex; align-items: center;
}
.entry { width: 154.5px; flex: none; box-sizing: border-box; display: flex; align-items: center; padding-left: 12px; cursor: pointer; }
.entry:last-child { padding-left: 0; }
.entry img { width: 41px; height: 41px; margin-right: 6px; display: block; }
.entry span { font-size: 14px; color: #0A0A0A; line-height: 15px; }
.empty-pos { position: absolute; top: 283px; left: 0; padding-top: 50px; }
.goods-list { display: flex; flex-wrap: wrap; justify-content: space-between; padding: 22px 16px 0; gap: 16px 12px; }
.goods-item { width: 165px; }
.goods-img {
  position: relative; width: 165px; height: 165px;
  background-size: cover; background-position: center; border-radius: 10px;
  overflow: hidden;
}
.goods-status {
  position: absolute; top: 8px; left: 8px;
  padding: 3px 8px; border-radius: 10px;
  background: rgba(0, 0, 0, .45); color: #FFFFFF; font-size: 11px;
}
.goods-remain {
  position: absolute; bottom: 0; left: 0; right: 0; height: 22px;
  background: rgba(0, 0, 0, .4); color: #FFFFFF;
  font-size: 12px; text-align: center; line-height: 22px;
}
.goods-name { margin-top: 8px; font-size: 14px; color: #212121; }
.goods-meta { display: flex; align-items: center; margin-top: 6px; font-size: 12px; color: #212121; }
.tag-limited { padding: 2px 5px; border-radius: 3px; background: #212121; color: #FFFFFF; font-size: 11px; }
.limited-num { margin-left: 6px; }
.goods-price { margin-left: auto; font-size: 14px; }
</style>
