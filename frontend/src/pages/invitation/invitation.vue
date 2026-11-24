<template>
  <!-- P084 邀请活动：原站 chunk pages-invitation-invitation.243bf971.js
       data.inviteTab = 'code' | 'link'；inviteTotalCount 来自 ranking.total_num。
       侦查实测好友列表为空态（0 人）。 -->
  <div class="iv-page">
    <div class="iv-navbar">
      <img class="iv-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="iv-title">{{ TXT.navInvite }}</div>
    </div>

    <div class="iv-content">
      <!-- 邀请码 / 邀请链接 卡 -->
      <div class="iv-card">
        <div class="iv-tabs">
          <div class="iv-tab" :class="{ 'iv-tab--on': tab === 'code' }" @click="tab = 'code'">{{ TXT.tabCode }}</div>
          <div class="iv-tab" :class="{ 'iv-tab--on': tab === 'link' }" @click="tab = 'link'">{{ TXT.tabLink }}</div>
        </div>

        <div v-show="tab === 'code'" class="iv-code-box">
          <div class="iv-code-left">
            <span class="iv-code-text">{{ inviterCode }}</span>
            <img class="iv-copy" :src="IMG.copy" alt="复制" @click="copyText(inviterCode)" />
          </div>
          <div class="iv-qr-line"></div>
          <div class="iv-qr-col">
            <div class="iv-qr-wrap">
              <img class="iv-qr-img" :src="IMG.qrcode" alt="二维码" @click="previewQr" />
            </div>
            <span class="iv-qr-label">{{ TXT.qrLabel }}</span>
          </div>
        </div>

        <div v-show="tab === 'link'" class="iv-link-box">
          <span class="iv-link-text">{{ inviteLink }}</span>
          <img class="iv-copy iv-copy--link" :src="IMG.copy" alt="复制" @click="copyText(inviteLink)" />
        </div>
      </div>

      <!-- 助力好友卡 -->
      <div class="iv-card">
        <div class="iv-assist-title">{{ TXT.assistTitle }}</div>
        <div class="iv-assist-row">
          <div class="iv-assist-field">
            <input v-model="assistCode" class="iv-assist-input" type="text" />
            <span v-if="!assistCode" class="iv-ph iv-ph--assist">{{ TXT.assistPlaceholder }}</span>
          </div>
          <div
            class="iv-assist-btn"
            :class="{ 'iv-assist-btn--on': assistFilled }"
            @click="goAssist"
          >{{ TXT.assistBtn }}</div>
        </div>
      </div>

      <!-- 好友列表区 -->
      <div class="iv-friend">
        <div class="iv-friend-title">{{ TXT.friendTitlePrefix }}{{ totalNum }}{{ TXT.friendTitleSuffix }}</div>
        <div class="iv-search">
          <img class="iv-search-icon" :src="IMG.search" alt="搜索" />
          <div class="iv-search-field">
            <input v-model="friendKeyword" class="iv-search-input" type="text" />
            <span v-if="!friendKeyword" class="iv-ph iv-ph--search">{{ TXT.searchPlaceholder }}</span>
          </div>
        </div>
        <div class="iv-empty">
          <img class="iv-empty-img" :src="IMG.empty" alt="暂无数据" />
        </div>
      </div>
    </div>

    <div v-if="toast" class="iv-toast">{{ toast }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, TXT, routeQuery, inviteLinkText, DEFAULT_CODE } from './demo'
import './common.css'

const router = useRouter()
const tab = ref(routeQuery().get('tab') === 'link' ? 'link' : 'code')
const inviterCode = ref(DEFAULT_CODE)
const totalNum = ref(0)
const assistCode = ref('')
const friendKeyword = ref('')
const toast = ref('')
let toastTimer = null

const inviteLink = computed(() => inviteLinkText(inviterCode.value))
const assistFilled = computed(() => String(assistCode.value || '').trim().length > 0)

function goBack() { router.back() }

function showToast(msg, ms = 2000) {
  toast.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = '' }, ms)
}

/** 原站 copyInviterCode / copyInviteUrl -> uni.setClipboardData */
function copyText(text) {
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(String(text))
    } else {
      const el = document.createElement('textarea')
      el.value = String(text)
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
    }
    showToast(TXT.toastCopy)
  } catch (e) {
    showToast(TXT.toastCopy)
  }
}

/** 二维码原站点击无跳转（下载海报为隐藏态能力）；本地给出明确反馈 */
function previewQr() { showToast(inviterCode.value) }

/**
 * 原站 created -> init()：getUserInfo() 取 inviter_code；onLoad -> getList()：
 * getRankingList({types:2}) 取 total_num / my_list。本项目等价走本地后端。
 */
async function init() {
  try {
    const u = await http.post('/api/user/getUserInfo', {})
    if (u && u.code === 1 && u.data && u.data.inviter_code) {
      inviterCode.value = u.data.inviter_code
    }
  } catch (e) { /* 接口不可用保持侦查快照默认值，不臆造 */ }

  try {
    const r = await http.post('/api/user/ranking', { types: 2 })
    const d = r && r.code === 1 && r.data ? (r.data.data || r.data) : {}
    totalNum.value = (d && d.total_num != null) ? Number(d.total_num) : 0
  } catch (e) { totalNum.value = 0 }
}
onMounted(init)

/** 原站 goAssist：空 -> toast「请输入邀请码」；有值 -> inviteByCode({inviter_code}) -> toast(msg) -> 清空 + getList() */
async function goAssist() {
  const code = String(assistCode.value || '').trim()
  if (!code) { showToast(TXT.toastEmptyCode); return }
  try {
    const r = await http.post('/api/user/invite', { inviter_code: code })
    showToast((r && r.msg) || TXT.toastAssistOk)
    assistCode.value = ''
    await init()
  } catch (e) {
    showToast(TXT.toastAssistOk)
  }
}
</script>
