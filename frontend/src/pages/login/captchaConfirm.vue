<template>
  <div class="captcha-confirm-page">
    <!-- 底部白色圆角面板 -->
    <div class="sheet">
      <div class="sheet-title">输入短信验证码</div>
      <div class="sent-to">验证码已发送至 +86 {{ maskMobile(mobile) }}</div>

      <!-- 6 位验证码方块 -->
      <div class="code-boxes" @click="focusInput">
        <div
          v-for="i in 6"
          :key="i"
          class="code-box"
          :class="{ active: code.length === i - 1 }"
        >
          {{ code[i - 1] || '' }}
        </div>
      </div>
      <input
        ref="hiddenInput"
        v-model="code"
        type="tel"
        maxlength="6"
        class="hidden-input"
        @input="onInput"
      />

      <div class="resend" @click="onResend">
        {{ counting ? seconds + 's后重新发送' : '重新发送' }}
      </div>

      <button class="login-btn" @click="onLogin">立即登录</button>

      <div class="pwd-login-link" @click="goPwdLogin">密码登录</div>
    </div>

    <!-- 协议行 -->
    <div class="agree-row">
      <span class="agree-check" :class="{ on: agreed }" @click="agreed = !agreed">
        <svg v-if="agreed" viewBox="0 0 16 16" width="9" height="9">
          <path d="M3 8.2 6.4 11.5 13 5" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </span>
      <text class="agree-text">我已阅读</text>
      <text class="agree-link">《用户协议》</text>
      <text class="agree-text">和</text>
      <text class="agree-link">《隐私政策》</text>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../../api/request'
import { useUserStore } from '../../stores/user'
import { showToast, useSmsCode, maskMobile } from '../../composables/sms'

const route = useRoute()
const router = useRouter()
const user = useUserStore()

const mobile = ref(String(route.query.mobile || ''))
const code = ref('')
const agreed = ref(true)
const loading = ref(false)
const hiddenInput = ref(null)

const { counting, seconds, send } = useSmsCode('login')

onMounted(() => {
  if (!mobile.value) router.replace('/pages/login/captchaLogin')
})

function onInput(e) {
  code.value = e.target.value.replace(/\D/g, '').slice(0, 6)
}
function focusInput() {
  hiddenInput.value && hiddenInput.value.focus()
}

function onResend() {
  send(mobile.value)
}

async function onLogin() {
  if (code.value.length < 6 || loading.value) return
  if (!agreed.value) {
    showToast('请先阅读并同意用户协议和隐私政策')
    return
  }
  loading.value = true
  try {
    const res = await http.post('/api/user/loginBySms', {
      mobile: mobile.value,
      mobile_code: code.value
    })
    if (res.code === 1 && res.data?.userinfo?.token) {
      user.setLogin(res.data.userinfo)
      showToast('登录成功')
    } else {
      showToast(res.msg || '登录失败')
    }
  } catch (e) {
    showToast('网络异常，请重试')
  } finally {
    loading.value = false
  }
}

function goPwdLogin() {
  router.replace('/pages/login/login')
}
</script>

<style scoped>
.captcha-confirm-page {
  position: relative;
  min-height: 100vh;
  background: #fff;
  overflow: hidden;
}

/* 白色面板：y245 起，顶部圆角25 */
.sheet {
  position: absolute;
  top: 245px;
  left: 0;
  right: 0;
  bottom: 0;
  background: #fff;
  border-radius: 25px 25px 0 0;
}

.sheet-title {
  margin: 26px 28px 0;
  font-size: 20px;
  line-height: 29px;
  color: #000;
  font-weight: 400;
}

.sent-to {
  margin: 12px 28px 0;
  font-size: 11px;
  line-height: 16px;
  color: #6a6a6a;
}

/* 验证码方块：6个 w44 h46 r14 黑边1px 间距10 */
.code-boxes {
  display: flex;
  justify-content: space-between;
  margin: 32px 28px 0;
  cursor: text;
}
.code-box {
  width: 44px;
  height: 46px;
  border-radius: 14px;
  border: 1px solid #000;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #1a1a1a;
}
.code-box.active {
  border-color: #000;
}
.hidden-input {
  position: absolute;
  left: -9999px;
  opacity: 0;
}

/* 重新发送：y418 fs11 */
.resend {
  margin: 12px 28px 0;
  font-size: 11px;
  line-height: 16px;
  color: #6a6a6a;
  cursor: pointer;
}

/* 立即登录：紫底 x99 w177 h34 r9 */
.login-btn {
  display: block;
  margin: 32px auto 0;
  width: 177px;
  height: 34px;
  border-radius: 9px;
  background: #9b77ec;
  color: #fff;
  font-size: 15px;
  line-height: 34px;
  text-align: center;
  padding: 0;
}

/* 密码登录：y512 */
.pwd-login-link {
  margin-top: 12px;
  text-align: center;
  font-size: 12px;
  line-height: 17px;
  color: #807f7f;
  cursor: pointer;
}

/* 协议行：y603 蓝勾灰字 */
.agree-row {
  position: absolute;
  top: 603px;
  left: 0;
  right: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  line-height: 17px;
}
.agree-check {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 1px solid #2979ff;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 5px;
  cursor: pointer;
}
.agree-check.on {
  background: #2979ff;
  border-color: #2979ff;
}
.agree-text,
.agree-link {
  color: #7a7a7a;
}
.agree-link {
  cursor: pointer;
}
</style>
