<template>
  <div class="register-page">
    <!-- 顶部返回登录 -->
    <header class="page-header">
      <text class="back-link" @click="goLogin">登录</text>
    </header>

    <div class="form-area">
      <div class="input-pill">
        <input v-model.trim="form.mobile" type="tel" maxlength="11" placeholder="请输入手机号" class="pill-input" />
      </div>

      <div class="input-pill">
        <input v-model="form.code" type="tel" inputmode="numeric" maxlength="6" placeholder="请输入验证码" class="pill-input" />
        <button class="get-code" :disabled="counting" @click="onGetCode">
          {{ counting ? seconds + 's' : '获取验证码' }}
        </button>
      </div>

      <div class="input-pill">
        <input v-model="form.password" type="password" placeholder="请输入密码" class="pill-input" />
      </div>

      <div class="input-pill">
        <input v-model.trim="form.inviterCode" type="text" placeholder="请输入邀请码" class="pill-input" />
      </div>

      <button class="submit-btn" :class="{ disabled: !canSubmit }" @click="onRegister">注册</button>

      <!-- 协议行 -->
      <div class="agree-row">
        <span class="agree-check" :class="{ on: agreed }" @click="agreed = !agreed">
          <svg v-if="agreed" viewBox="0 0 16 16" width="9" height="9">
            <path d="M3 8.2 6.4 11.5 13 5" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
        <text class="agree-text">我已阅读</text>
        <text class="agree-link" @click="openAgreement('user')">《用户协议》</text>
        <text class="agree-text">和</text>
        <text class="agree-link" @click="openAgreement('privacy')">《隐私保护协议》</text>
      </div>
    </div>

    <!-- 底部去登录 -->
    <div class="login-link" @click="goLogin">已有账号，去登录</div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { showToast, useSmsCode } from '../../composables/sms'

const router = useRouter()
const form = reactive({ mobile: '', code: '', password: '', inviterCode: '' })
const agreed = ref(true)

const { counting, seconds, send } = useSmsCode('register')

const canSubmit = computed(() =>
  form.mobile.length === 11 && form.code.length > 0 && form.password.length > 0
)

function onGetCode() {
  send(form.mobile)
}

async function onRegister() {
  if (!canSubmit.value) return
  if (!agreed.value) {
    showToast('请先阅读并同意用户协议和隐私保护协议')
    return
  }
  try {
    const res = await http.post('/api/user/register', {
      account: form.mobile,
      password: form.password,
      mobile_code: form.code,
      inviter_code: form.inviterCode
    })
    if (res.code === 1) {
      showToast('注册成功')
      setTimeout(() => router.replace('/pages/login/login'), 1200)
    } else {
      showToast(res.msg || '注册失败')
    }
  } catch (e) {
    showToast('网络异常，请重试')
  }
}

function goLogin() {
  router.replace('/pages/login/login')
}
function openAgreement() {}
</script>

<style scoped>
.register-page {
  position: relative;
  min-height: 100vh;
  background: #fff url('../../assets/images/register-bg.png') no-repeat top center;
  background-size: 100% 100%;
  overflow: hidden;
}

.page-header {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 51px;
  z-index: 2;
}
.back-link {
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
  top: 288px;
  left: 0;
  right: 0;
}

.input-pill {
  margin: 0 24px;
  height: 48px;
  border-radius: 24px;
  background: #ecf0f4;
  display: flex;
  align-items: center;
  padding: 0 20px;
}
.input-pill + .input-pill {
  margin-top: 12px;
}
.pill-input {
  flex: 1;
  min-width: 0;
  height: 20px;
  font-size: 14px;
  line-height: 20px;
  color: #202020;
}

/* 获取验证码：黑底白字胶囊 w87 h36 r18 右侧内缩6 */
.get-code {
  flex-shrink: 0;
  width: 87px;
  height: 36px;
  border-radius: 18px;
  background: #000;
  color: #fff;
  font-size: 12px;
  line-height: 36px;
  text-align: center;
  padding: 0;
  white-space: nowrap;
}
.get-code:disabled {
  opacity: 0.6;
}

/* 注册按钮：y536 h48 r24 启用#D93437 禁用#E8D0D2 */
.submit-btn {
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
.submit-btn.disabled {
  background: #e8d0d2;
}

/* 协议行：y596 居中 fs11 */
.agree-row {
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  line-height: 16px;
}
.agree-check {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 1px solid #d83434;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 6px;
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

/* 底部去登录：y752 居中 fs14 红色下划线 */
.login-link {
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
