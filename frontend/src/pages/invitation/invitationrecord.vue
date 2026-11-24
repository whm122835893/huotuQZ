<template>
  <!-- P085 邀请记录：原站 chunk pages-invitation-invitationrecord.2d8b2c65.js
       displayTotalNum 来自 ranking.total_num；list 为空时渲染 empty-data 空态图。 -->
  <div class="iv-page">
    <div class="iv-navbar">
      <img class="iv-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="iv-title">{{ TXT.navRecord }}</div>
    </div>

    <div class="iv-content">
      <!-- 统计卡：0 总人数 -->
      <div class="iv-stat">
        <span class="iv-stat-num">{{ totalNum }}</span>
        <span class="iv-stat-label">{{ TXT.totalLabel }}</span>
      </div>

      <!-- 好友列表 -->
      <div class="iv-friend">
        <div class="iv-friend-title">{{ TXT.friendTitlePrefix }}{{ totalNum }}{{ TXT.friendTitleSuffix }}</div>
        <div v-if="list.length" class="iv-list">
          <div v-for="(it, i) in list" :key="it.id || i" class="iv-row">
            <img class="iv-row-avatar" :src="friendAvatar(it)" alt="" />
            <div class="iv-row-meta">
              <div class="iv-row-nick">{{ it.nickname || '--' }}</div>
              <div class="iv-row-sub">Cell: {{ friendMobile(it) }}</div>
              <div class="iv-row-sub">{{ recordTime(it) }}</div>
            </div>
            <div class="iv-row-tags">
              <span class="iv-tag" :class="{ 'iv-tag--on': isRealName(it) }">{{ isRealName(it) ? realNameOn : realNameOff }}</span>
              <span class="iv-tag" :class="{ 'iv-tag--on': hasPurchase(it) }">{{ hasPurchase(it) ? purchaseOn : purchaseOff }}</span>
            </div>
          </div>
        </div>
        <div v-else class="iv-empty">
          <img class="iv-empty-img" :src="IMG.empty" alt="暂无数据" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, TXT } from './demo'
import './common.css'

const router = useRouter()
const totalNum = ref(0)
const list = ref([])

// 原站 chunk 硬编码标签文案
const realNameOn = '已实名', realNameOff = '未实名'
const purchaseOn = '已购买', purchaseOff = '未购买'

function goBack() { router.back() }

/** 原站 displayFriendAvatar：默认头像走本地静态图 */
function friendAvatar(it) { return (it && it.avatar) ? it.avatar : IMG.empty }
/** 原站 displayFriendMobile：手机号脱敏 */
function friendMobile(it) {
  const m = it && it.mobile
  return m ? String(m).replace(/^(\d{3})\d{4}(\d{4})$/, '$1****$2') : '--'
}
/** 原站 displayRecordTime：share_success_time 格式化，无值返回 -- */
function recordTime(it) {
  const t = it && it.share_success_time
  if (!t) return '--'
  const d = new Date(Number(t) * 1000)
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}
function isRealName(it) { const n = Number(it && it.real_name_authentication); return !Number.isNaN(n) && n !== 0 }
function hasPurchase(it) { const n = Number(it && it.first_buy_time); return !Number.isNaN(n) && n !== 0 }

/** 原站 onLoad -> getList()：getRankingList({types:2}) -> data.total_num / data.my_list */
async function getList() {
  try {
    const r = await http.post('/api/user/ranking', { types: 2 })
    const d = r && r.code === 1 && r.data ? (r.data.data || r.data) : {}
    totalNum.value = (d && d.total_num != null) ? Number(d.total_num) : 0
    list.value = Array.isArray(d && d.my_list) ? d.my_list : (Array.isArray(d) ? d : [])
  } catch (e) {
    // 原站 getList catch -> data = { my_list: [], total_num: 0 }
    totalNum.value = 0
    list.value = []
  }
}
onMounted(getList)
</script>
