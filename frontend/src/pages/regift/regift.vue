<template>
  <div class="rg-page">
    <div class="rg-navbar">
      <img class="rg-nav-back" :src="IMG.back" alt="" @click="goBack" />
      <div class="rg-nav-title">转赠</div>
    </div>

    <div class="rg-body">
      <div class="rg-card">
        <img class="rg-card-img" :src="localImg(product.listimg)" alt="" />
        <div class="rg-card-name">{{ product.name }}</div>
      </div>

      <div class="rg-label">选择用户</div>
      <div class="rg-search">
        <span class="rg-search-ico"></span>
        <input v-model="phone" class="rg-search-input" :placeholder="SEARCH_PH" @input="onInput" />
      </div>

      <div v-if="user" class="rg-user">
        <img class="rg-user-avatar" :src="localImg(user.avatar) || IMG.defAvatar" alt="" />
        <div class="rg-user-name">{{ user.nickname }}</div>
      </div>

      <div class="rg-notice">
        <div class="rg-notice-title">{{ GIFT_TITLE }}</div>
        <div v-for="(n, i) in GIFT_NOTICE" :key="i" class="rg-notice-line">{{ n }}</div>
      </div>
    </div>

    <div class="rg-footer">
      <div class="rg-footer-tip">{{ GIFT_TIP }}</div>
      <div class="rg-btn" @click="onConfirm">确认转赠</div>
    </div>

    <div v-if="showConfirm" class="rg-mask" @click.self="showConfirm = false">
      <div class="rg-popup">
        <div class="rg-popup-title">请输入操作密码</div>
        <div class="rg-code" @click="focusPwd">
          <span v-for="i in 6" :key="i" class="rg-code-cell" :class="{ filled: payPwd.length >= i, caret: payPwd.length === i - 1 }"></span>
          <input ref="pwdInput" v-model="payPwd" class="rg-code-input" type="tel" maxlength="6" @input="onPwd" />
        </div>
        <div class="rg-popup-foot">忘记密码？使用 <span>实名认证</span></div>
      </div>
    </div>

    <div v-if="toast" class="rg-toast">{{ toast }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, localImg, routeQuery, GIFT_TITLE, GIFT_NOTICE, GIFT_TIP, SEARCH_PH } from './demo'
import './common.css'

const router = useRouter()
const q = routeQuery()
const iid = q.get('id') || ''
const product = ref({ name: '', listimg: '' })
const phone = ref('')
const user = ref(null)
const showConfirm = ref(false)
const payPwd = ref('')
const pwdInput = ref(null)
const toast = ref('')
let timer = null

function goBack() { router.back() }
function showToast(msg) {
  toast.value = msg
  clearTimeout(timer)
  timer = setTimeout(() => { toast.value = '' }, 2000)
}

async function load() {
  if (!iid) { setTimeout(() => router.replace('/pages/index/index'), 1500); return }
  try {
    const res = await http.post(`/api/user_collection/user_collection/detail/ids/${iid}`, {})
    if (res && res.code === 1 && res.data && res.data.product) {
      product.value = {
        name: res.data.product.name || '',
        listimg: res.data.product.listimg || res.data.product.main_image || ''
      }
    }
  } catch (e) { /* 保留空态，与原站失败回退一致 */ }
}

function onInput() {
  clearTimeout(timer)
  const val = phone.value.trim()
  if (!val) { user.value = null; return }
  timer = setTimeout(async () => {
    try {
      const res = await http.post('/api/user/ParamsToUser', { phone: val })
      if (res && res.code === 1 && res.data) user.value = res.data
      else { user.value = null; showToast('用户不存在') }
    } catch (e) { user.value = null }
  }, 500)
}

function onConfirm() { showConfirm.value = true }
function focusPwd() { if (pwdInput.value) pwdInput.value.focus() }

async function onPwd() {
  if (payPwd.value.length < 6) return
  if (!user.value) { showConfirm.value = false; payPwd.value = ''; return }
  try {
    const res = await http.post('/api/user_collection/user_collection/reGiftOrder', {
      pay_password: payPwd.value,
      re_user_id: user.value.id,
      user_collection_id: iid
    })
    showToast(res && res.msg ? res.msg : '转赠失败')
  } catch (e) { showToast('转赠失败') }
  payPwd.value = ''
  showConfirm.value = false
}

onMounted(load)
</script>

<style scoped>
.rg-body { padding: 4px 20px 110px; }

.rg-card {
  display: flex; align-items: center; height: 104px;
  padding: 12px; box-sizing: border-box;
  background: var(--rg-card); border-radius: 12px;
}
.rg-card-img { width: 80px; height: 80px; border-radius: 8px; flex-shrink: 0; }
.rg-card-name {
  padding-left: 12px; font-size: 15px; font-weight: 500;
  line-height: 21px; color: var(--rg-dark);
}

.rg-label {
  margin-top: 12px; font-size: 13px; font-weight: 400;
  line-height: 18px; color: var(--rg-muted);
}

.rg-search {
  margin-top: 8px; height: 40px; display: flex; align-items: center;
  padding-left: 14px; box-sizing: border-box;
  background: var(--rg-field); border-radius: 20px;
}
.rg-search-ico {
  position: relative; width: 13px; height: 13px; flex-shrink: 0;
  border: 1.5px solid var(--rg-muted); border-radius: 50%;
}
.rg-search-ico::after {
  content: ''; position: absolute; right: -4px; bottom: -2px;
  width: 5px; height: 1.5px; border-radius: 1px;
  background: var(--rg-muted); transform: rotate(45deg);
}
.rg-search-input {
  flex: 1; height: 100%; margin-left: 2px; border: none; outline: none;
  background: transparent; font-size: 14px; color: var(--rg-dark);
}
.rg-search-input::placeholder { color: var(--rg-placeholder); }

.rg-user {
  margin-top: 12px; height: 76px; display: flex; align-items: center;
  padding: 12px; box-sizing: border-box;
  background: var(--rg-card); border-radius: 12px;
}
.rg-user-avatar { width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0; }
.rg-user-name {
  padding-left: 10px; font-size: 14px; font-weight: 500;
  line-height: 20px; color: var(--rg-dark);
}

.rg-notice { margin-top: 12px; }
.rg-notice-title {
  font-size: 14px; font-weight: 500; line-height: 20px; color: var(--rg-dark);
}
.rg-notice-line {
  margin-top: 6px; font-size: 12px; font-weight: 400;
  line-height: 18px; color: var(--rg-muted);
}
.rg-notice-title + .rg-notice-line { margin-top: 10px; }

.rg-footer {
  position: fixed; left: 0; right: 0; bottom: 0; height: 86px;
  background: var(--rg-white); padding: 0 16px; box-sizing: border-box; z-index: 5;
}
.rg-footer-tip {
  margin-top: 8px; text-align: center;
  font-size: 11px; font-weight: 400; line-height: 16px; color: var(--rg-muted);
}
.rg-btn {
  margin-top: 6px; height: 48px; display: flex; align-items: center; justify-content: center;
  border-radius: 14px; background: var(--rg-red); cursor: pointer;
  font-size: 16px; font-weight: 500; line-height: 22px; color: var(--rg-white);
}

.rg-mask {
  position: fixed; inset: 0; z-index: 20;
  background: rgba(0, 0, 0, 0.5);
}
.rg-popup {
  position: absolute; left: 24px; right: 24px; top: 318px; height: 172px;
  background: var(--rg-card); border-radius: 16px; box-sizing: border-box;
  padding-top: 52px;
}
.rg-popup-title {
  text-align: center; font-size: 16px; font-weight: 700;
  line-height: 22px; color: var(--rg-dark);
}
.rg-code { display: flex; justify-content: center; gap: 8px; margin-top: 8px; position: relative; }
.rg-code-cell {
  position: relative; width: 36px; height: 42px; border-bottom: 1px solid transparent;
  display: flex; align-items: center; justify-content: center;
}
.rg-code-cell.filled::after {
  content: ''; width: 9px; height: 9px; border-radius: 50%; background: var(--rg-black);
}
.rg-code-cell.caret::before {
  content: ''; position: absolute; left: 50%; top: 11px;
  width: 1px; height: 20px; background: var(--rg-dark);
}
.rg-code-input {
  position: absolute; inset: 0; width: 100%; height: 100%;
  opacity: 0; border: none; outline: none;
}
.rg-popup-foot {
  margin-top: 16px; text-align: center;
  font-size: 12px; line-height: 17px; color: var(--rg-muted);
}
.rg-popup-foot span { color: var(--rg-red); }

.rg-toast {
  position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%);
  z-index: 30; padding: 10px 18px; border-radius: 8px;
  background: rgba(0, 0, 0, 0.75); color: var(--rg-white);
  font-size: 14px; line-height: 20px;
}
</style>
