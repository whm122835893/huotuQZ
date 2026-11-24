<template>
  <div class="sg-page">
    <img class="sg-bg" :src="IMG.bg" alt="" />

    <div class="sg-content">
      <div class="sg-navbar">
        <img class="sg-back" :src="IMG.back" alt="返回" @click="goBack" />
      </div>

      <div class="sg-hero">
        <div class="sg-hero-title">{{ TXT.title }}</div>
        <div class="sg-hero-sub">{{ TXT.subtitle }}</div>
        <div class="sg-rule" @click="showRules">{{ TXT.rule }}</div>
      </div>

      <div class="sg-user">
        <div class="sg-user-left">
          <div class="sg-user-top">
            <div class="sg-avatar-wrap"><img class="sg-avatar" :src="avatarSrc" alt="" /></div>
            <div class="sg-user-meta">
              <div class="sg-nickname">{{ nickname }}</div>
              <div class="sg-uid">{{ TXT.uidPrefix }}{{ uid }}</div>
            </div>
          </div>
          <div class="sg-streak">
            <span class="sg-streak-txt">{{ TXT.streakPrefix }}</span>
            <span class="sg-streak-num">{{ successions }}</span>
            <span class="sg-streak-txt">{{ TXT.streakSuffix }}</span>
          </div>
        </div>
        <div class="sg-user-right">
          <div class="sg-state" :class="{ 'sg-state--signed': hasSignedToday }">
            {{ hasSignedToday ? TXT.signedTitle : TXT.unsignedTitle }}
          </div>
          <div class="sg-state-sub">{{ hasSignedToday ? TXT.signedSub : TXT.unsignedSub }}</div>
        </div>
      </div>

      <div class="sg-cal">
        <div class="sg-cal-head">
          <img class="sg-arrow" :src="IMG.arrowL" alt="上月" @click="prevMonth" />
          <div class="sg-cal-title">{{ monthTitle }}</div>
          <img
            class="sg-arrow"
            :class="{ 'sg-arrow--disabled': !canGoNext }"
            :src="IMG.arrowR"
            alt="下月"
            @click="nextMonth"
          />
        </div>
        <div class="sg-weeks">
          <div class="sg-week" v-for="w in WEEKS" :key="w">{{ w }}</div>
        </div>
        <div class="sg-grid">
          <div v-for="(it, i) in items" :key="i" class="sg-cell" :class="cellClass(it)">
            <template v-if="it.day > 0">
              <span class="sg-cell-num">{{ it.isToday ? TXT.today : it.day }}</span>
              <span v-if="statusText(it)" class="sg-cell-status">{{ statusText(it) }}</span>
            </template>
          </div>
        </div>
      </div>

      <div class="sg-cta-wrap">
        <div class="sg-cta" :class="{ 'sg-cta--signed': hasSignedToday }" @click="onSign">
          {{ hasSignedToday ? TXT.btnSigned : TXT.btnSign }}
        </div>
      </div>
    </div>

    <div v-if="successVisible" class="sg-modal" @click.stop>
      <div class="sg-modal-card" @click.stop>
        <div class="sg-modal-title">{{ TXT.successTitle }}</div>
        <div class="sg-modal-btn" @click="hideSuccess">{{ TXT.successBtn }}</div>
      </div>
      <div class="sg-modal-close" @click="hideSuccess">
        <img :src="IMG.close" alt="关闭" />
      </div>
    </div>

    <div v-if="toastMsg" class="sg-toast">{{ toastMsg }}</div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import './common.css'
import {
  IMG, TXT, WEEKS, SNAPSHOT,
  buildCalendarItems, cellClass, statusText,
  shiftMonth, canGoNextMonth, parseSignedDays
} from './demo'

const router = useRouter()

const nickname = ref('')
const uid = ref('')
const avatar = ref('')
const config = ref({})
const signedDays = ref([])
const display = ref({ year: SNAPSHOT.year, month: SNAPSHOT.month })
const localSigned = ref(false)
const signing = ref(false)
const successVisible = ref(false)
const toastMsg = ref('')
let toastTimer = null

const avatarSrc = computed(() => {
  const a = avatar.value
  return a && !a.includes('defautAvatar') ? a : IMG.avatar
})
const successions = computed(() => {
  const c = config.value
  return c.successions != null ? Number(c.successions) || 0 : 0
})
const hasSignedToday = computed(() => {
  if (localSigned.value) return true
  const v = config.value.is_signin
  return v === true || v === 1 || v === '1'
})
const monthTitle = computed(() => `${display.value.year}年${display.value.month}月`)
const canGoNext = computed(() => canGoNextMonth(display.value.year, display.value.month))
const items = computed(() => buildCalendarItems(display.value.year, display.value.month, signedDays.value))

function toast(msg) {
  toastMsg.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (toastMsg.value = ''), 2000)
}
function hideToast() {
  clearTimeout(toastTimer)
  toastMsg.value = ''
}
function goBack() {
  router.back()
}
function showRules() {
  const t = config.value.rule || config.value.signin_rule || ''
  if (typeof t === 'string' && t) toast(t)
  else toast(TXT.ruleEmpty)
}

async function fetchMonthRecord(monthText) {
  try {
    const data = monthText ? { month: monthText } : {}
    const r = await http.post('/addons/signin/api.index/monthSign', data)
    if (r && r.code === 1) signedDays.value = parseSignedDays(r.data)
    else toast(TXT.recordFail)
  } catch (e) {
    toast(TXT.recordFail)
  }
}

async function loadPageData() {
  try {
    const u = await http.post('/api/user/getUserInfo', {})
    if (u && u.code === 1 && u.data) {
      nickname.value = u.data.nickname || ''
      uid.value = u.data.origin_uid || u.data.id || u.data.user_id || ''
      avatar.value = u.data.avatar || ''
    }
  } catch (e) { /* 401 由拦截器跳登录 */ }
  try {
    const r = await http.post('/addons/signin/api.index/index', {})
    if (r && r.code === 1) config.value = r.data || {}
  } catch (e) { /* 静默，保留快照态 */ }
  await fetchMonthRecord()
}

function prevMonth() {
  const d = shiftMonth(display.value.year, display.value.month, -1)
  display.value = { year: d.year, month: d.month }
  fetchMonthRecord(d.text)
}
function nextMonth() {
  if (!canGoNext.value) return
  const d = shiftMonth(display.value.year, display.value.month, 1)
  display.value = { year: d.year, month: d.month }
  fetchMonthRecord(d.text)
}

async function onSign() {
  if (hasSignedToday.value || signing.value) return
  signing.value = true
  toast(TXT.loading)
  try {
    const r = await http.post('/addons/signin/api.index/dosign', {})
    signing.value = false
    hideToast()
    if (r && r.code === 1) {
      successVisible.value = true
      localSigned.value = true
      if (display.value.year === SNAPSHOT.year && display.value.month === SNAPSHOT.month &&
          !signedDays.value.includes(SNAPSHOT.day)) {
        signedDays.value = [...signedDays.value, SNAPSHOT.day]
      }
    } else {
      toast((r && r.msg) || TXT.signFail)
    }
  } catch (e) {
    signing.value = false
    hideToast()
    toast(TXT.signFail)
  }
}
function hideSuccess() {
  successVisible.value = false
}

onMounted(loadPageData)
</script>
