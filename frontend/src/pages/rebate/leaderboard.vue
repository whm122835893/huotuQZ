<template>
  <div class="rb-page">
    <div class="rb-navbar">
      <img class="rb-back" :src="IMG.back" alt="返回" @click="goBack" />
    </div>

    <div class="rb-tabs">
      <div
        v-for="(t, i) in LB.tabs"
        :key="t"
        class="rb-tab"
        :class="{ 'rb-tab--on': active === i }"
        @click="switchTab(i)"
      >
        <span class="rb-tab-txt">{{ t }}</span>
      </div>
    </div>

    <div class="rb-lb-head">
      <span class="rb-lb-h1">{{ LB.heads[0] }}</span>
      <span class="rb-lb-h2">{{ LB.heads[1] }}</span>
      <span class="rb-lb-h3">{{ LB.heads[2] }}</span>
    </div>

    <div class="rb-empty">
      <img class="rb-empty-img" :src="IMG.empty" alt="暂无数据" />
      <span class="rb-empty-txt">{{ LB.empty }}</span>
    </div>
  </div>
</template>

<script setup>
/* P086 返佣排行榜：原站 pages-rebate-leaderboard chunk 实测
   u-tabs 日/周/月排行 + 表头(排名/用户/返佣金额) + 空态。
   commison_rank 原站返回 404（An error occurred），三 Tab 均无数据。 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, LB, routeQuery } from './demo'
import './common.css'

const router = useRouter()
const active = ref(0)

const q = routeQuery()
const tabMap = { day: 0, week: 1, month: 2 }
if (q.get('tab') && tabMap[q.get('tab')] != null) active.value = tabMap[q.get('tab')]

function goBack() { router.back() }

/** 原站 three Tab 均请求 commison_rank，本项目等价走本地后端（404 空态）；
 *  切 Tab 不臆造榜单数据，仅切换选中态。 */
async function loadRank(types) {
  try {
    await http.post('/api/user/commison_rank', { types })
  } catch (e) { /* 原站 404，空态如实保持 */ }
}

function switchTab(i) {
  if (active.value === i) return
  active.value = i
  loadRank(i + 1)
}

onMounted(() => loadRank(active.value + 1))
</script>
