<template>
  <!-- P082 我的钱包：引导中转页。原站 chunk f410 的 walletList 只有 1 项「汇付天下」 -->
  <div class="wl-page">
    <div class="wl-navbar">
      <img class="wl-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="wl-title">{{ TXT.navTitle }}</div>
    </div>

    <div class="wl-body">
      <div v-for="w in walletList" :key="w.key" class="wl-row">
        <img class="wl-card" :src="IMG.card" alt="汇付天下钱包卡" @click="action(w)" />
        <img class="wl-add" :src="IMG.add" alt="开通钱包" @click="action(w)" />
      </div>
    </div>

    <div class="wl-footer">
      <div class="wl-btn" @click="onActivateTap">{{ activateButtonText }}</div>
    </div>

    <!-- 交互结果：展示本地接口返回的真实快照，不自动跳第三方外链 -->
    <div v-if="panel" class="wl-mask" @click.self="panel = null">
      <div class="wl-panel">
        <div class="wl-panel-title">{{ panel.title }}</div>
        <div v-for="row in panel.rows" :key="row.k" class="wl-panel-line">
          <span class="wl-panel-k">{{ row.k }}</span>
          <span class="wl-panel-v">{{ row.v }}</span>
        </div>
        <a v-if="panel.url" class="wl-panel-link" :href="panel.url" target="_blank" rel="noopener noreferrer">{{ panel.linkText }}</a>
        <div class="wl-panel-close" @click="panel = null">关闭</div>
      </div>
    </div>

    <div v-if="toast" class="wl-toast">{{ toast }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, WALLET_LIST, TXT, isUnopenDemo } from './demo'
import './common.css'

const router = useRouter()
const walletList = ref(WALLET_LIST.map((w) => ({ ...w })))
const currentIndex = ref(0)
const panel = ref(null)
const toast = ref('')
let toastTimer = null

/** 原站 computed：currentWallet.openstatus ? '进入钱包' : '开通此钱包' */
const currentWallet = computed(() => walletList.value[currentIndex.value] || walletList.value[0] || null)
const activateButtonText = computed(() =>
  currentWallet.value && currentWallet.value.openstatus ? TXT.btnEnter : TXT.btnOpen
)

function goBack() { router.back() }

function showToast(msg, ms = 2000) {
  toast.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = '' }, ms)
}

/**
 * 原站 onShow -> initInfo(): getUserInfo() 后
 *   walletList = walletList.map(w => (w.openstatus = !!res[w.authKey], w))
 * 本项目 getUserInfo 由增量 SQL 追加 huifu_account 列后自动带出，零改动已完成控制器。
 */
async function initInfo() {
  try {
    const res = await http.post('/api/user/getUserInfo', {})
    const u = res && res.code === 1 && res.data ? res.data : {}
    const unopen = isUnopenDemo()
    walletList.value = walletList.value.map((w) => ({
      ...w,
      openstatus: unopen ? false : !!Number(u[w.authKey] ?? 0)
    }))
  } catch (e) {
    // 接口不可用时保持未开户文案，不臆造已开户态
    walletList.value = walletList.value.map((w) => ({ ...w, openstatus: false }))
  }
}
onMounted(initInfo)

/** 原站 methods.action：已开户 -> toWalletPage；汇付未开户 -> openHuiFuWallet；其他 -> 敬请期待对接中 */
function action(w) {
  const i = walletList.value.findIndex((x) => x.key === w.key)
  if (i >= 0) currentIndex.value = i
  if (w.openstatus) toWalletPage(w)
  else if (w.key === 'huifu') openHuiFuWallet()
  else showToast(TXT.comingSoon)
}

function onActivateTap() {
  const w = currentWallet.value
  if (w) action(w)
}

/** 已开户：原站 getHuiFuInfo() -> data.info.redirect_url -> toWeb(window.location.href) */
async function toWalletPage() {
  showToast(TXT.loading, 900)
  try {
    const res = await http.post('/api/wallet/huifu/wallet', {})
    if (res && res.code === 1 && res.data) {
      const info = res.data.info || {}
      panel.value = {
        title: '汇付天下钱包',
        rows: [
          { k: '开户状态', v: res.data.openstatus ? '已开通' : '未开通' },
          { k: '响应结果', v: info.resp_desc || '-' },
          { k: '响应码', v: info.resp_code || '-' },
          { k: '商户号', v: info.mer_cust_id || '-' }
        ],
        url: info.redirect_url || '',
        linkText: '前往汇付天下钱包'
      }
      toast.value = ''
    } else {
      showToast((res && res.msg) || TXT.docking)
    }
  } catch (e) {
    showToast(TXT.docking)
  }
}

/** 未开户：原站 openHuiFu() -> 已存在商户号则跳 redirect_url，否则弹服务费确认 */
async function openHuiFuWallet() {
  showToast(TXT.loading, 900)
  try {
    const res = await http.post('/api/wallet/huifu/openAccount', {})
    // 原站该账号实测返回 code:0「您已经开户了」，如实展示，不臆造成功路径
    showToast((res && res.msg) || TXT.comingSoon, 2200)
  } catch (e) {
    showToast(TXT.docking)
  }
}
</script>
