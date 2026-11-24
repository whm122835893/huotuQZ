<template>
  <!-- P043 合成 Tab：与原站 compound 逐像素对齐（导航44标题17/卡片335x140 r12/图80x112 r8/标签10px/倒计时BebasNeue/GO图50x24） -->
  <div class="compound-page">
    <div class="nav">
      <span class="nav-title">合成</span>
    </div>

    <!-- 活动卡片列表（themeList.play） -->
    <div v-if="plays.length" class="list">
      <div v-for="a in plays" :key="a.id" class="card">
        <img class="cover" :src="a.image" alt="" />
        <div class="right">
          <div class="title">{{ a.title }}</div>
          <div class="tags">
            <span v-if="a.description" class="tag tag-desc">{{ a.description }}</span>
            <span class="tag" :style="statusTagStyle(a)">{{ statusText(a) }}</span>
          </div>
          <div class="bottom">
            <!-- 倒计时（未结束：未开始灰盒/进行中红盒，与原站 countdownParts 一致） -->
            <div v-if="!isEnded(a)" class="countdown">
              <template v-for="(p, i) in countdownParts(a)" :key="i">
                <span class="cd-box" :class="{ 'cd-in': isInProgress(a) }">{{ p.value }}</span>
                <span v-if="p.suffix" class="cd-suffix">{{ p.suffix }}</span>
              </template>
            </div>
            <img class="go" src="../../assets/images/home-notice-go.png" alt="GO" @click="confim(a)" />
          </div>
        </div>
      </div>
    </div>

    <!-- 空态（原站 empty-data 组件同款） -->
    <div v-else class="empty">
      <img class="empty-img" src="../../assets/images/empty-data.png" alt="" />
      <div class="empty-text">活动暂未开启</div>
    </div>

    <TabBar />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import TabBar from '../../components/TabBar.vue'

const router = useRouter()
const plays = ref([])
let timer = null
const now = ref(Date.now())

onMounted(() => {
  load()
  timer = setInterval(() => (now.value = Date.now()), 1000)
})
onUnmounted(() => clearInterval(timer))

async function load() {
  const res = await http.post('/api/synthesis/themeList', {})
  const themes = res.data || []
  plays.value = themes.flatMap((t) => t.play || [])
}

/* ===== 状态判断（与原站 getStatus/checkTimeBetween 逻辑一致） ===== */
function timeStatus(a) {
  const s = Number(a.real_start_time)
  const e = Number(a.real_end_time)
  if (!s || !e) return 2
  if (now.value < s) return 0
  if (now.value > e) return 2
  return 1
}
function isEnded(a) {
  return a.stock === 0 || timeStatus(a) === 2
}
function isInProgress(a) {
  return timeStatus(a) === 1
}
function statusText(a) {
  return isEnded(a) ? '已结束' : isInProgress(a) ? '进行中' : '即将开始'
}
function statusTagStyle(a) {
  if (isEnded(a)) return { background: '#eeeeee', color: '#9aa0ab' }
  if (isInProgress(a)) return { background: '#e8f3ff', color: '#2d8cff' }
  return { background: '#e8f8ef', color: '#00b578' }
}
function countdownTarget(a) {
  return timeStatus(a) === 0 ? a.real_start_time : a.real_end_time
}
function pad2(n) {
  return String(n).padStart(2, '0')
}
function countdownParts(a) {
  const target = Number(countdownTarget(a))
  let t = Math.max(0, target - now.value)
  const d = Math.floor(t / 864e5)
  t %= 864e5
  const h = Math.floor(t / 36e5)
  t %= 36e5
  const m = Math.floor(t / 6e4)
  const s = Math.floor((t % 6e4) / 1e3)
  const parts = [
    { value: pad2(d), suffix: '天' },
    { value: pad2(h), suffix: ':' },
    { value: pad2(m), suffix: ':' },
    { value: pad2(s), suffix: '' }
  ]
  return parts
}

/* ===== GO 点击（与原站 confim 一致：仅进行中响应） ===== */
let toastTimer = null
function showToast(msg) {
  let el = document.querySelector('.ht-toast')
  if (!el) {
    el = document.createElement('div')
    el.className = 'ht-toast'
    document.body.appendChild(el)
  }
  el.textContent = msg
  el.style.display = 'block'
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (el.style.display = 'none'), 2000)
}

async function confim(a) {
  if (!isInProgress(a) || a.stock === 0) return
  try {
    const res = await http.post('/api/synthesis/material', { play_id: a.id, classify: a.classify })
    if (res.code === 1) {
      showToast('请选择合成材料')
    }
  } catch (err) {
    const code = err && err.code
    if (code === 4003) {
      showToast('您未获得合成资格')
    } else if (code === 3003) {
      router.push({ path: '/pages/compound/schemeDetails', query: { play_id: a.id, classify: a.classify || '', grp: '' } })
    }
  }
}
</script>

<style scoped>
.compound-page {
  min-height: 100vh;
  background: #f7f9fc;
  padding-bottom: 56px;
}

/* ===== 导航 44：标题居中 ===== */
.nav {
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.nav-title {
  font-size: 17px;
  font-weight: 700;
  color: #000;
  font-family: 'PuHuiTi';
}

/* ===== 活动卡片 ===== */
.list {
  padding: 12px 20px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.card {
  width: 335px;
  min-height: 140px;
  background: #fff;
  border-radius: 12px;
  padding: 14px 12px;
  box-sizing: border-box;
  display: flex;
  align-items: flex-start;
}
.cover {
  flex-shrink: 0;
  width: 80px;
  height: 112px;
  border-radius: 8px;
  object-fit: cover;
  display: block;
  background: #f0f3f6;
}
.right {
  flex: 1;
  min-width: 0;
  height: 112px;
  margin-left: 10px;
  display: flex;
  flex-direction: column;
}
.title {
  font-family: 'PuHuiTi';
  font-weight: 500;
  font-size: 14px;
  color: #212121;
  line-height: 20px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.tags {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 4px;
}
.tag {
  padding: 2px 6px;
  font-size: 10px;
  line-height: 14px;
  border-radius: 3px;
}
.tag-desc {
  background: #ffe8e8;
  color: #d93437;
  margin-right: 4px;
}
.bottom {
  margin-top: auto;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}
.countdown {
  display: flex;
  align-items: center;
  min-width: 0;
}
.cd-box {
  min-width: 20px;
  height: 20px;
  padding: 0 3px;
  border-radius: 4px;
  font-family: 'Bebas Neue', 'PuHuiTi', sans-serif;
  font-size: 12px;
  line-height: 20px;
  text-align: center;
  background: #eef1f5;
  color: #212121;
}
.cd-box.cd-in {
  background: #ffe8e8;
  color: #d93437;
}
.cd-suffix {
  margin: 0 3px;
  font-size: 10px;
  color: #9aa0ab;
  line-height: 14px;
}
.go {
  width: 50px;
  height: 24px;
  flex-shrink: 0;
  margin-left: 6px;
  margin-left: auto;
  cursor: pointer;
}

/* ===== 空态 ===== */
.empty {
  padding-top: 90px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.empty-img {
  width: 142px;
  height: 142px;
}
.empty-text {
  margin-top: 14px;
  font-size: 13px;
  color: #999;
}
</style>
