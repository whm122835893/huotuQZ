<template>
  <!-- P075 盲盒结果（一键开启）：storage(blindBoxResultList) 数组；原站空态仅一行提示 + 底部双按钮 -->
  <div class="bb-page bbs-page">
    <div class="bb-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">盲盒结果</span>
    </div>

    <div class="bbs-content">
      <div v-for="(it, i) in list" :key="i" class="bbs-item">
        <img class="bbs-thumb bb-ph" :src="localImg(it.listimg)" alt="" />
        <div class="bbs-info">
          <div class="bbs-name">{{ it.name }}</div>
          <div class="bbs-code">藏品编号: <span>#{{ it.collection_code }}</span></div>
        </div>
        <img class="bbs-rarity" :src="localImg(it.rarity_icon)" alt="" />
      </div>
      <div class="bbs-hint">商品陆续从链上转入个人中心，如有延迟请耐心等待</div>
    </div>

    <div class="bb-footer bbs-footer">
      <div class="fb-btn fb-light" @click="toMyCollection">我的数字藏品</div>
      <div class="fb-btn fb-dark" @click="toHome">继续购买</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { isDemo, readStore, localImg, DEMO_RESULT_LIST } from './demo'
import './common.css'

const router = useRouter()
const list = ref(readStore('blindBoxResultList', []))
if (isDemo() && !list.value.length) list.value = DEMO_RESULT_LIST
if (!Array.isArray(list.value)) list.value = []

function goBack() { router.back() }
function toMyCollection() { router.back() }
function toHome() { router.replace('/') }
</script>

<style scoped>
.bbs-page { background: var(--bb-card); }
.bbs-content { width: 345px; margin: 0 auto; padding: 13px 13px 100px; border-radius: 9px; }
.bbs-item {
  width: 100%; padding: 8px; margin-bottom: 12px;
  background: var(--bb-card); border-radius: 8px;
  display: flex; align-items: center;
}
.bbs-thumb { width: 59px; height: 59px; border-radius: 2px; object-fit: cover; flex-shrink: 0; }
.bbs-info { flex: 1; min-width: 0; margin-left: 8px; }
.bbs-name { font-size: 13px; font-weight: 500; color: var(--bb-black); line-height: 22px; }
.bbs-code { margin-top: 12px; font-size: 12px; line-height: 18px; color: var(--bb-black); }
.bbs-code span { color: #999999; font-weight: 500; font-family: 'Bebas Neue', 'Arial Narrow', sans-serif; }
.bbs-rarity { width: 59px; height: 55px; object-fit: contain; flex-shrink: 0; }
.bbs-hint { width: 100%; text-align: center; font-size: 10px; color: #7A7A7A; }
.bbs-footer { background: #F4F6FA; }
</style>
