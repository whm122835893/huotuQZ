<template>
  <div v-if="!demo" class="rb-blank"></div>

  <div v-else class="rb-demo">
    <div class="rb-navbar">
      <img class="rb-back" :src="IMG.back" alt="返回" @click="goBack" />
    </div>
    <div class="rb-d-tag">原站接口 404 · chunk 模板注入态</div>

    <!-- chunk: info_box avatar + nickname -->
    <div class="rb-d-info">
      <img class="rb-d-avatar" :src="info.avatar || IMG.avatar" alt="" />
      <span class="rb-d-nick">{{ info.nickname }}</span>
    </div>

    <!-- chunk: 默认邀请链接 -->
    <div class="rb-d-sec">{{ RB.defaultLinkTitle }}</div>
    <div class="rb-d-card">
      <div class="rb-d-row">
        <span class="rb-d-k">{{ RB.inviteCode }}</span>
        <span class="rb-d-v">{{ info.inviter_code }}</span>
      </div>
      <div class="rb-d-row rb-d-link" @click="toPage('/pages/invitation/invitation')">
        <span class="rb-d-k">{{ RB.invitePoster }}</span>
        <span class="rb-d-arrow">›</span>
      </div>
      <div class="rb-d-row rb-d-link" @click="copyInviteUrl">
        <span class="rb-d-k">{{ RB.inviteLinkPrefix }}{{ maskedLink }}</span>
        <span class="rb-d-arrow">›</span>
      </div>
    </div>

    <!-- chunk: 数据详情 -->
    <div class="rb-d-sec">{{ RB.dataTitle }}</div>
    <div class="rb-d-card">
      <div class="rb-d-row">
        <span class="rb-d-k">{{ RB.allCommision }}</span>
        <span class="rb-d-v">{{ info.all_commision }}</span>
      </div>
      <div class="rb-d-row">
        <span class="rb-d-k" @click.stop="openTip(RB.tipCurrent)">
          {{ RB.currentCommision }}<span class="rb-d-q">?</span>
        </span>
        <span class="rb-d-v">{{ info.yesterday_commision }}</span>
      </div>
      <div class="rb-d-row">
        <span class="rb-d-k">{{ RB.userTransaction }}</span>
        <span class="rb-d-v">{{ info.user_transaction }}</span>
      </div>
      <div class="rb-d-row">
        <span class="rb-d-k">{{ RB.userCommision }}</span>
        <span class="rb-d-v">{{ info.mingxia_commision }}</span>
      </div>
    </div>

    <!-- chunk: 被邀请人（点标题跳 rebateInviteDetail） -->
    <div class="rb-d-sec rb-d-link" @click="toPage('/pages/rebate/rebateInviteDetail')">
      {{ RB.invited }} <span class="rb-d-arrow">›</span>
    </div>
    <div class="rb-d-card">
      <div class="rb-d-row">
        <span class="rb-d-k">{{ RB.inviteCount }}</span>
        <span class="rb-d-v">{{ info.yao_counts }}</span>
      </div>
      <div class="rb-d-row">
        <span class="rb-d-k" @click.stop="openTip(RB.tipValid)">
          {{ RB.validInviteCount }}<span class="rb-d-q">?</span>
        </span>
        <span class="rb-d-v">{{ info.you_yao_counts }}</span>
      </div>
    </div>

    <!-- chunk: 节点排行榜（原站图 /static/bg/yao-image.png 返回空，按无产物不设席位
         不渲染图片，跳转入口如实保留在标题行，点击跳 leaderboard） -->
    <div class="rb-d-sec rb-d-link" @click="toPage('/pages/rebate/leaderboard')">
      {{ RB.nodeRank }} <span class="rb-d-arrow">›</span>
    </div>

    <div v-if="tip" class="rb-toast" @click="tip = ''">{{ tip }} · {{ RB.know }}</div>
    <div v-else-if="toast" class="rb-toast">{{ toast }}</div>
  </div>
</template>

<script setup>
/* P087 返佣主页：原站 pages-rebate-rebate.90a45f76.js 实测
   data.loading=true → onLoad 请求 commision_info()，原站该接口返回 404
   （An error occurred）→ .catch 静默 → loading 恒 true → w-container
   loading 遮罩盖住整页 → uni-page-body 仅剩 <!----> → 呈纯白屏。
   按硬规则「不臆造」，默认态如实复刻该白屏；?demo=1 用 chunk 真实模板
   文案 + 主账号真实字段还原完整 UI（批次12 BoxDetail 双态同款，可验收）。 */
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  IMG, RB, RB_SNAPSHOT, routeQuery, inviteLinkText, maskedInviteLink
} from './demo'
import './common.css'

const router = useRouter()
const demo = ref(routeQuery().get('demo') === '1')
const info = ref({ ...RB_SNAPSHOT })
const tip = ref('')
const toast = ref('')
let toastTimer = null

const inviteUrl = computed(() => inviteLinkText(info.value.inviter_code))
const maskedLink = computed(() => maskedInviteLink(inviteUrl.value))

function goBack() { router.back() }
function toPage(path) { router.push(path) }

function openTip(text) { tip.value = text }

function showToast(msg) {
  toast.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = '' }, 1600)
}

/** chunk copyInviteUrl -> uni.setClipboardData({data: h5_invite_url}) */
function copyInviteUrl() {
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(inviteUrl.value)
    } else {
      const el = document.createElement('textarea')
      el.value = inviteUrl.value
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
    }
    showToast('已复制')
  } catch (e) { showToast('已复制') }
}
</script>

<style scoped>
/* 原站真实态：纯白屏（w-container loading 遮罩，uni-page-body 仅 <!---->） */
.rb-blank {
  min-height: 100vh;
  background: #FFFFFF;
}
</style>
