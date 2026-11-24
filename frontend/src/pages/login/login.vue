<template>
  <div class="login-page">
    <!-- 顶部注册入口 -->
    <header class="login-header">
      <text class="reg-link" @click="goRegister">注册</text>
    </header>

    <!-- 表单区（与原站 375 视口逐像素对齐） -->
    <div class="form-area">
      <div class="input-pill">
        <input
          v-model.trim="form.account"
          type="tel"
          maxlength="11"
          placeholder="请输入手机号"
          class="pill-input"
        />
      </div>

      <div class="input-pill pwd-pill">
        <input
          v-model="form.password"
          :type="showPwd ? 'text' : 'password'"
          placeholder="请输入密码"
          class="pill-input"
        />
        <text class="forgot" @click="goForget">忘记密码?</text>
      </div>

      <button
        class="login-btn"
        :class="{ disabled: !canSubmit }"
        :disabled="loading"
        @click="onLogin"
      >
        登录
      </button>

      <!-- 协议行 -->
      <div class="agree-row">
        <span class="agree-check" :class="{ on: agreed }" @click="agreed = !agreed">
          <svg v-if="agreed" viewBox="0 0 16 16" width="10" height="10">
            <path
              d="M3 8.2 6.4 11.5 13 5"
              fill="none"
              stroke="#fff"
              stroke-width="2.2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </span>
        <text class="agree-text">已阅读并同意</text>
        <text class="agree-link" @click="openAgreement('user')">《用户协议》</text>
        <text class="agree-text">和</text>
        <text class="agree-link" @click="openAgreement('privacy')">《隐私政策》</text>
      </div>
    </div>

    <!-- 底部验证码登录 -->
    <div class="captcha-login" @click="goCaptchaLogin">验证码登录</div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { useUserStore } from '../../stores/user'

const router = useRouter()
const user = useUserStore()

const form = reactive({ account: '', password: '' })
const agreed = ref(true) // 原站默认勾选
const showPwd = ref(false)
const loading = ref(false)

const canSubmit = computed(() => form.account.length > 0 && form.password.length > 0)

/** 登录：POST /api/user/login —— 与原站同构响应 {code:1,msg,data:{userinfo:{...token}}} */
async function onLogin() {
  if (!canSubmit.value || loading.value) return
  if (!agreed.value) {
    showToast('请先阅读并同意用户协议和隐私政策')
    return
  }
  loading.value = true
  try {
    const res = await http.post('/api/user/login', {
      account: form.account,
      password: form.password
    })
    if (res.code === 1 && res.data?.userinfo?.token) {
      user.setLogin(res.data.userinfo)
      // 首页已交付（P008），登录成功后与原站一致跳回首页
      showToast('登录成功')
      setTimeout(() => router.replace('/'), 800)
    } else {
      showToast(res.msg || '登录失败')
    }
  } catch (e) {
    showToast(e?.message || '网络异常，请重试')
  } finally {
    loading.value = false
  }
}

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

/* 登录域各页面已完成交付，接通全部跳转 */
function goRegister() {
  router.push('/pages/login/register')
}
function goForget() {
  router.push('/pages/login/forgetPwd')
}
function goCaptchaLogin() {
  router.push('/pages/login/captchaLogin')
}
function openAgreement() {}
</script>

<style scoped>
.login-page {
  position: relative;
  min-height: 100vh;
  background: #fff url('../../assets/images/login-bg.png') no-repeat top center;
  background-size: 100% auto;
  overflow: hidden;
}

/* 顶部注册：right 24 / top 31 / fs14 / #D83434 */
.login-header {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 51px;
  z-index: 2;
}
.reg-link {
  position: absolute;
  top: 31px;
  right: 24px;
  font-size: 14px;
  line-height: 20px;
  color: #d83434;
  cursor: pointer;
}

.form-area {
  position: absolute;
  top: 439px;
  left: 0;
  right: 0;
}

/* 输入胶囊：x24 w327 h48 r24 bg#ECF0F4，内边距左20 */
.input-pill {
  margin: 0 24px;
  height: 48px;
  border-radius: 24px;
  background: #ecf0f4;
  display: flex;
  align-items: center;
  padding: 0 20px;
}
.pwd-pill {
  margin-top: 12px;
  padding-right: 20px;
}
.pill-input {
  flex: 1;
  min-width: 0;
  height: 20px;
  font-size: 14px;
  line-height: 20px;
  color: #202020;
  letter-spacing: 0.2px;
}

/* 忘记密码：fs12 #F09CA0，右置于密码胶囊内 */
.forgot {
  flex-shrink: 0;
  margin-left: 8px;
  white-space: nowrap;
  font-size: 12px;
  line-height: 17px;
  color: #f09ca0;
  cursor: pointer;
}

/* 登录按钮：y567(距胶囊20) h48 r24 启用#D93437 禁用#E8D0D2 文字白 fs15 w500 */
.login-btn {
  display: block;
  margin: 20px 24px 0;
  width: 327px;
  height: 48px;
  border-radius: 24px;
  background: #d93437;
  color: #fff;
  font-size: 15px;
  font-weight: 500;
  line-height: 48px;
  text-align: center;
}
.login-btn.disabled {
  background: #e8d0d2;
}

/* 协议行：y627（按钮下12）居中 fs11 */
.agree-row {
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  line-height: 16px;
}
.agree-check {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1px solid #d83434;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 4px;
  cursor: pointer;
}
.agree-check.on {
  background: #d83434;
}
.agree-text {
  color: #787c8c;
}
.agree-link {
  color: #d83434;
  cursor: pointer;
}

/* 验证码登录：距视口底约40 居中 fs14 #D83434 下划线 */
.captcha-login {
  position: absolute;
  bottom: 40px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: 14px;
  line-height: 20px;
  color: #d83434;
  text-decoration: underline;
  cursor: pointer;
}
</style>

<style>
/* 全局轻提示（复用原站 toast 观感：深色圆角块居中） */
.ht-toast {
  display: none;
  position: fixed;
  left: 50%;
  bottom: 25%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.75);
  color: #fff;
  font-size: 13px;
  line-height: 1.5;
  padding: 9px 16px;
  border-radius: 8px;
  z-index: 9999;
  max-width: 70vw;
  text-align: center;
}
</style>
