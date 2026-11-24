<template>
  <div class="captcha-login-page">
    <div class="form-area">
      <div class="page-title">输入你的手机号</div>

      <div class="phone-pill">
        <span class="prefix">+86</span>
        <input v-model.trim="mobile" type="tel" maxlength="11" placeholder="请输入手机号" class="pill-input" />
      </div>

      <button class="get-code-btn" :disabled="counting" @click="onGetCode">
        {{ counting ? seconds + 's后重新获取' : '获取验证码' }}
      </button>

      <div class="pwd-login-link" @click="goPwdLogin">密码登录</div>
    </div>

    <!-- 协议行（蓝勾样式，与原站一致） -->
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

    <div class="skip-link" @click="goHome">暂不登录？</div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { showToast, useSmsCode } from '../../composables/sms'

const router = useRouter()
const mobile = ref('')
const agreed = ref(true)
const { counting, seconds, send } = useSmsCode('login')

async function onGetCode() {
  if (!agreed.value) {
    showToast('请先阅读并同意用户协议和隐私政策')
    return
  }
  const mock = await send(mobile.value)
  if (mock !== null) {
    router.push({ path: '/pages/login/captchaConfirm', query: { mobile: mobile.value } })
  }
}
function goPwdLogin() {
  router.replace('/pages/login/login')
}
function goHome() {
  router.replace('/')
}
</script>

<style scoped>
.captcha-login-page {
  position: relative;
  min-height: 100vh;
  background: #fff;
  overflow: hidden;
}

.form-area {
  position: absolute;
  top: 320px;
  left: 0;
  right: 0;
}

/* 标题：居中 fs12 浅灰 */
.page-title {
  text-align: center;
  font-size: 12px;
  line-height: 17px;
  color: #a5a5a5;
}

/* 手机号胶囊：x28 w320 h37 r18 白底黑边 */
.phone-pill {
  margin: 16px 28px 0;
  height: 37px;
  border-radius: 18px;
  border: 1px solid #1a1a1a;
  background: #fff;
  display: flex;
  align-items: center;
  padding: 0 6px 0 6px;
}
.prefix {
  flex-shrink: 0;
  width: 55px;
  height: 28px;
  border-radius: 14px;
  background: #000;
  color: #fff;
  font-size: 15px;
  line-height: 28px;
  text-align: center;
}
.pill-input {
  flex: 1;
  min-width: 0;
  margin-left: 12px;
  height: 17px;
  font-size: 12px;
  line-height: 17px;
  color: #171717;
}

/* 获取验证码：紫色按钮 x99 w177 h34 r9 */
.get-code-btn {
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
.get-code-btn:disabled {
  opacity: 0.7;
}

/* 密码登录：y468 居中 fs12 */
.pwd-login-link {
  margin-top: 20px;
  text-align: center;
  font-size: 12px;
  line-height: 17px;
  color: #807f7f;
  cursor: pointer;
}

/* 协议行：y559 居中 fs12（原站该页协议文字与链接同为灰色、蓝勾） */
.agree-row {
  position: absolute;
  top: 559px;
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

/* 暂不登录？：y600 居中 fs10 */
.skip-link {
  position: absolute;
  top: 600px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: 10px;
  line-height: 15px;
  color: #6c6c6c;
  cursor: pointer;
}
</style>
