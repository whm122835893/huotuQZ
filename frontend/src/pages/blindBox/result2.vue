<template>
  <!-- P076 盲盒结果（逐个拆开）：storage(blindBoxResult) 单对象；原站空态仅「藏品编号： #」 -->
  <div class="bb-page bbt-page">
    <div class="bb-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">盲盒结果</span>
    </div>

    <div class="bbt-content">
      <template v-if="info.name">
        <img class="bbt-rarity" :src="localImg(info.rarity_icon)" alt="" />
        <img class="bbt-main bb-ph" :src="localImg(info.main_image)" alt="" />
        <div class="bbt-name">{{ info.name }}</div>
      </template>
      <div class="bbt-code">藏品编号：<span>#{{ info.collection_code }}</span></div>
    </div>

    <div class="bb-footer bbt-footer">
      <div class="fb-btn fb-light" @click="toMyCollection">我的数字藏品</div>
      <div class="fb-btn fb-dark" @click="toHome">继续购买</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { isDemo, readStore, localImg, DEMO_RESULT } from './demo'
import './common.css'

const router = useRouter()
let info = ref(readStore('blindBoxResult', {}))
if (isDemo() && !info.value?.name) info.value = { ...DEMO_RESULT }
if (!info.value || typeof info.value !== 'object') info.value = {}

function goBack() { router.back() }
function toMyCollection() { router.replace('/pages/myCollection/myCollection') }
function toHome() { router.replace('/') }
</script>

<style scoped>
.bbt-page { background: var(--bb-card); }
/* 原站 content：flex column center，rarity 80×59 / 主图 200×200 / 名称 16px / 编号 12px */
.bbt-content { display: flex; flex-direction: column; align-items: center; padding-top: 52px; }
.bbt-rarity { width: 80px; height: 59px; object-fit: contain; }
.bbt-main { width: 200px; height: 200px; border-radius: 5px; object-fit: contain; margin: 16px 0; }
.bbt-name { margin-top: 6px; font-size: 16px; font-weight: 500; color: #555555; line-height: 17px; }
.bbt-code { margin-top: 8px; font-size: 12px; color: #999999; }
.bbt-code span { font-weight: 500; font-family: 'Bebas Neue', 'Arial Narrow', sans-serif; }
/* 原站 result2 footer 白底、按钮间距 10px */
.bbt-footer { background: var(--bb-card); gap: 10px; }
</style>
