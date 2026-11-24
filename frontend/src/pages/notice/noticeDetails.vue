<template>
  <!-- P039 公告详情：与原站 noticeDetails 逐像素对齐（白底/导航44/标题18/头图343r8/时间10px/富文本14px lh26） -->
  <div class="detail-page">
    <div class="nav">
      <img class="back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">官方公告</span>
    </div>

    <div v-if="info.title" class="body">
      <h1 class="title">{{ info.title }}</h1>

      <img v-if="bannerSrc" class="banner" :src="bannerSrc" alt="" />

      <div class="time">{{ fmtTime(info.publish_time) }}</div>

      <!-- 富文本正文 -->
      <div class="rich" v-html="content"></div>

      <!-- 关联藏品（原站 afficheDetail.collection，本项目暂无数据） -->
      <div v-for="g in info.collection" :key="g.id" class="good-item">
        <div class="good-left">
          <img class="good-img" :src="g.listimg" alt="" />
          <span class="good-name">{{ g.name }}</span>
        </div>
        <button class="good-btn">立即购买</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import http from '../../api/request'

const info = ref({})
const category = ref('')

const content = computed(() => info.value.content || '')

/* 头图映射与原站一致：先按分类 id，再按类型名关键词 */
const bannerById = { 1: 'guanfang', 2: 'yunying', 3: 'huodong', 4: 'jishou', 5: 'kongtou', 7: 'xianjia', 8: 'hecheng' }
const bannerByKeys = [
  { keys: ['空投'], file: 'kongtou' },
  { keys: ['寄售'], file: 'jishou' },
  { keys: ['限价'], file: 'xianjia' },
  { keys: ['合成', '上新'], file: 'hecheng' },
  { keys: ['活动'], file: 'huodong' },
  { keys: ['运营'], file: 'yunying' },
  { keys: ['官方'], file: 'guanfang' }
]
const bannerSrc = computed(() => {
  const cat = String(category.value || info.value.affiche_type || '').trim()
  if (!cat) return ''
  const banner = (n) => new URL(`../../assets/images/notice-banner-${n}.png`, import.meta.url).href
  if (bannerById[cat]) return banner(bannerById[cat])
  for (const g of bannerByKeys) {
    for (const k of g.keys) {
      if (cat.indexOf(k) > -1) return banner(g.file)
    }
  }
  return ''
})

function fmtTime(ts) {
  if (!ts) return ''
  const d = new Date(Number(ts) * 1000)
  const p = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
}

function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = '#/pages/notice/notice'
}

async function load(id) {
  const res = await http.post(`/api/index/afficheDetail/ids/${id}`)
  info.value = res.data || {}
}

{
  const q = new URLSearchParams(window.location.hash.split('?')[1] || '')
  category.value = q.get('category') || ''
  load(q.get('id') || 0)
}
</script>

<style scoped>
.detail-page {
  min-height: 100vh;
  background: #fff;
}

/* ===== 导航栏 44 ===== */
.nav {
  position: sticky;
  top: 0;
  z-index: 5;
  height: 44px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}
.back {
  position: absolute;
  left: 19px;
  top: 50%;
  transform: translateY(-50%);
  width: 7px;
  height: 13px;
  cursor: pointer;
}
.nav-title {
  font-size: 17px;
  line-height: 23px;
  color: #000;
}

.body {
  padding: 0 16px;
}

/* 标题：y62 fs18 SourceHanSansCN-Medium #161616 lh26 */
.title {
  margin: 18px 0 0;
  font-family: 'SourceHanSansCN-Medium', 'Alibaba-PuHuiTi', sans-serif;
  font-size: 18px;
  line-height: 26px;
  font-weight: 500;
  color: #161616;
}

/* 头图：mt12 w343 r8 */
.banner {
  display: block;
  margin-top: 12px;
  width: 343px;
  border-radius: 8px;
}

/* 时间：mt12 fs10 w300 #847765 lh22 pb */
.time {
  margin-top: 12px;
  padding-bottom: 11px;
  font-family: 'SourceHanSansCN', sans-serif;
  font-size: 10px;
  line-height: 22px;
  font-weight: 300;
  color: #847765;
}

/* 富文本：mt20 fs14 #333 lh26 字距1px */
.rich {
  margin-top: 20px;
  font-family: 'SourceHanSansCN-Regular', sans-serif;
  font-size: 14px;
  font-weight: 400;
  color: #333;
  line-height: 26px;
  letter-spacing: 1px;
  word-break: break-all;
}
.rich :deep(p) {
  margin: 0;
}
.rich :deep(img) {
  max-width: 100%;
}

/* 关联藏品行 */
.good-item {
  margin-top: 16px;
  padding: 12px 16px;
  background: #f7f9fc;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.good-left {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #333;
}
.good-img {
  width: 75px;
  height: 75px;
  border-radius: 10px;
  margin-right: 9px;
  object-fit: cover;
}
.good-btn {
  height: 28px;
  padding: 0 16px;
  border: none;
  border-radius: 14px;
  background: #d93437;
  color: #fff;
  font-size: 12px;
  cursor: pointer;
}
</style>
