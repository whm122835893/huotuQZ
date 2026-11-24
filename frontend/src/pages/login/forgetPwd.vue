<template>
  <div class="forget-pwd-page">
    <!-- 顶部：返回 + 登录 -->
    <header class="page-header">
      <img class="back-icon" src="../../assets/images/common-modal-close.png" alt="" @click="goBack" />
      <text class="login-link" @click="goLogin">登录</text>
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
        <input v-model="form.password" type="password" placeholder="请输入新密码" class="pill-input" />
      </div>

      <div class="input-pill">
        <input v-model="form.password2" type="password" placeholder="请再次输入新密码" class="pill-input" />
      </div>

      <button class="submit-btn" :class="{ disabled: !canSubmit }" @click="onConfirm">确认</button>

      <!-- 协议文字行 -->
      <div class="agree-row">
        <text class="agree-text">登录即同意</text>
        <text class="agree-link">用户协议</text>
        <text class="agree-text">和</text>
        <text class="agree-link">隐私协议</text>
      </div>
    </div>

    <!-- 底部去登录 -->
    <div class="bottom-link" @click="goLogin">已有账户?去登录</div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { showToast, useSmsCode } from '../../composables/sms'

const router = useRouter()
const form = reactive({ mobile: '', code: '', password: '', password2: '' })
const { counting, seconds, send } = useSmsCode('resetpwd')

const canSubmit = computed(() =>
  form.mobile.length === 11 && form.code.length > 0 && form.password.length > 0 && form.password2.length > 0
)

function onGetCode() {
  send(form.mobile)
}

async function onConfirm() {
  if (!canSubmit.value) return
  if (form.password !== form.password2) {
    showToast('两次输入的密码不一致')
    return
  }
  if (!/(?=.*\d)(?=.*[a-zA-Z])/.test(form.password) || form.password.length < 6 || form.password.length > 12) {
    showToast('密码内需同时包含数字、字母，长度在6-12之间')
    return
  }
  try {
    const res = await http.post('/api/user/resetpwd', {
      mobile: form.mobile,
      mobile_code: form.code,
      password: form.password
    })
    if (res.code === 1) {
      showToast('密码重置成功')
      setTimeout(() => router.replace('/pages/login/login'), 1200)
    } else {
      showToast(res.msg || '重置失败')
    }
  } catch (e) {
    showToast('网络异常，请重试')
  }
}

function goLogin() {
  router.replace('/pages/login/login')
}
function goBack() {
  router.back()
}
</script>

<style scoped>
.forget-pwd-page {
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
.back-icon {
  position: absolute;
  top: 31px;
  left: 24px;
  width: 24px;
  height: 24px;
  cursor: pointer;
}
.login-link {
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
  top: 292px;
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

/* 确认按钮：y540 h48 r24 */
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

/* 协议文字行：y712 居中 fs11 */
.agree-row {
  margin-top: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  line-height: 16px;
}
.agree-text {
  color: #787c8c;
}
.agree-link {
  color: #d83434;
  cursor: pointer;
}

/* 底部去登录：y752 */
.bottom-link {
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
