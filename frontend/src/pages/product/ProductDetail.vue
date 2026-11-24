<template>
  <!-- P077/P078 商品详情共享主体：原站两页同构，仅标题、详情标题与底部按钮不同 -->
  <div v-if="failed" class="pd-page pd-fail">
    <p class="pd-fail-text">{{ failMsg }}</p>
  </div>

  <div v-else-if="loaded" class="pd-page">
    <div class="pd-hero-bg">
      <div class="pd-blur" :style="{ backgroundImage: `url(${heroImg})` }"></div>
      <div class="pd-shade"></div>
    </div>
    <div class="pd-main-box"></div>
    <div class="pd-main" :style="{ backgroundImage: `url(${heroImg})` }"></div>

    <div class="pd-navbar">
      <img class="pd-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="pd-nav-title"><span>{{ pageTitle }}</span></div>
    </div>

    <div class="pd-content">
      <div class="pd-tab-card">
        <div class="pd-tabs">
          <div class="pd-tab" @click="onFollow">
            <img class="pd-tab-ico" :src="IMG.icoFollow" alt="" />
            <span class="pd-tab-text">关注</span>
          </div>
          <div class="pd-tab" @click="toNotice">
            <img class="pd-tab-ico" :src="IMG.icoNotice" alt="" />
            <span class="pd-tab-text">公告</span>
          </div>
          <div class="pd-tab" @click="toMarket">
            <img class="pd-tab-ico" :src="IMG.icoMarket" alt="" />
            <span class="pd-tab-text">市场</span>
          </div>
        </div>
        <div class="pd-white-card">
          <div class="pd-name"><span>{{ product.name }}</span></div>
          <div class="pd-stats">
            <div class="pd-stat">
              <div class="pd-stat-label"><span>发行</span></div>
              <div class="pd-stat-val"><span>{{ formatNum(product.total_num) }}</span></div>
            </div>
            <div class="pd-stat">
              <div class="pd-stat-label"><span>发售价</span></div>
              <div class="pd-stat-val"><span class="pd-yen">¥</span><span>{{ product.price }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="pd-author-card">
        <div class="pd-sec-title"><span>作者介绍</span></div>
        <div class="pd-author-row">
          <img class="pd-avatar" :src="IMG.avatar" alt="" />
          <div class="pd-author-info">
            <div class="pd-author-name"><span>{{ publisherName }}</span></div>
            <div class="pd-other"><span>其他作品 --</span></div>
          </div>
        </div>
      </div>

      <div class="pd-detail-card">
        <div class="pd-sec-title"><span>{{ detailTitle }}</span></div>
        <img class="pd-detail-empty" :src="IMG.empty" alt="" />
        <div class="pd-notice-title"><span>购买须知</span></div>
        <div class="pd-notice-text"><span>{{ BUY_NOTICE }}</span></div>
      </div>
    </div>

    <div class="pd-footer" :class="{ 'pd-footer-buy': isBox }">
      <template v-if="isBox">
        <div class="pd-price"><span>¥{{ product.price }}</span></div>
        <div class="pd-btn-buy" @click="onBuy">立即抢购</div>
      </template>
      <div v-else class="pd-btn" @click="toMarket">已售罄，去市场看看</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, routeQuery, isDemo, localImg, formatNum, BUY_NOTICE } from './demo'
import './common.css'

const props = defineProps({ mode: { type: String, default: 'collection' } })
const router = useRouter()
const isBox = computed(() => props.mode === 'box')
const pageTitle = computed(() => (isBox.value ? '盲盒' : '藏品'))
const detailTitle = computed(() => (isBox.value ? '盲盒详情' : '藏品详情'))

const q = routeQuery()
const id = Number(q.get('id') || 0)
const batchId = Number(q.get('batch_id') || 0)
const demo = isDemo()

const loaded = ref(false)
const failed = ref(false)
const failMsg = ref('')
const product = ref({})
const heroImg = computed(() => localImg(product.value.main_image || product.value.listimg))
const publisherName = computed(() => {
  const p = product.value.publisher
  return (p && p.name) || '火兔'
})

function goBack() { router.back() }
function toMarket() { router.replace('/pages/market/market') }
function toNotice() {
  const name = product.value.name || ''
  router.push('/pages/product/notice' + (name ? '?keyword=' + encodeURIComponent(name) : ''))
}
function onFollow() { /* 原站点关注需登录态，MVP 保留占位交互 */ }
function onBuy() { /* 原站弹出下单面板，MVP 保留占位交互 */ }

async function loadCollection() {
  const url = `/api/box/collection/ProductDetail/ids/${id}?batch_id=${batchId}`
  const res = await http.post(url, {})
  if (res && res.code === 1 && res.data) {
    product.value = res.data
    loaded.value = true
  } else {
    fail(res && res.msg)
  }
}

async function loadBox() {
  if (demo) {
    // 注入态：当前原站无在售盲盒，用本项目真实商品快照还原页面结构
    const res = await http.post('/api/box/collection/ProductDetail/ids/33?batch_id=4', {})
    if (res && res.code === 1 && res.data) {
      product.value = Object.assign({}, res.data, { goods_type: 1 })
      loaded.value = true
      return
    }
  }
  const res = await http.post(`/api/box/blind_box/BoxDetail/ids/${id}`, {})
  if (res && res.code === 1 && res.data) {
    product.value = res.data
    loaded.value = true
  } else {
    fail(res && res.msg)
  }
}

function fail(msg) {
  failMsg.value = msg || '找不到该商品，或者已经下架了'
  failed.value = true
  // 原站 getBoxDetails 失败后 1.5s backPage；无上一页时回到首页
  setTimeout(() => router.replace('/'), 1500)
}

onMounted(() => {
  if (isBox.value) loadBox()
  else loadCollection()
})
</script>
