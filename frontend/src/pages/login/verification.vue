<template>
  <div class="verification-page">
    <!-- 返回 -->
    <header class="page-header">
      <img class="back-icon" src="../../assets/images/common-modal-close.png" alt="" @click="goBack" />
    </header>

    <div class="form-area">
      <div class="page-title">请输入验证码</div>
      <div class="sent-to">已发送至+86 {{ maskMobile(mobile) }}</div>

      <div class="input-pill">
        <input v-model="code" type="tel" inputmode="numeric" maxlength="6" placeholder="请输入验证码" class="pill-input" />
        <span class="get-code" :class="{ disabled: counting }" @click="onGetCode">
          {{ counting ? seconds + 's' : '获取验证码' }}
        </span>
      </div>

      <button class="submit-btn" @click="onNext">下一步</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../../api/request'
import { showToast, useSmsCode, maskMobile } from '../../composables/sms'

const route = useRoute()
const router = useRouter()

const mobile = ref(String(route.query.mobile || ''))
const code = ref('')
const { counting, seconds, send } = useSmsCode('resetpwd')

function onGetCode() {
  send(mobile.value)
}

async function onNext() {
  if (code.value.length < 6) {
    showToast('请输入验证码')
    return
  }
  try {
    const res = await http.post('/api/user/checkSmsCode', {
      mobile: mobile.value,
      mobile_code: code.value
    })
    if (res.code === 1 && res.data?.confirm_token) {
      router.push({
        path: '/pages/login/setPwd',
        query: { mobile: mobile.value, token: res.data.confirm_token }
      })
    } else {
      showToast(res.msg || '验证失败')
    }
  } catch (e) {
    showToast('网络异常，请重试')
  }
}

function goBack() {
  router.back()
}
</script>

<style scoped>
.verification-page {
  position: relative;
  min-height: 100vh;
  background: #fff;
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

.form-area {
  position: absolute;
  top: 70px;
  left: 0;
  right: 0;
}

/* 标题：x40 y70 fs18 加粗黑 */
.page-title {
  margin: 0 40px;
  font-size: 18px;
  line-height: 26px;
  color: #000;
  font-weight: 600;
  text-align: center;
}

/* 已发送至：y96 fs12 灰 */
.sent-to {
  margin: 0 40px;
  font-size: 12px;
  line-height: 30px;
  color: #7a7a7a;
  text-align: center;
}

/* 黑色胶囊：x40 y268 w295 h50 r18 深黑 */
.input-pill {
  margin: 142px 40px 0;
  height: 50px;
  border-radius: 18px;
  background: #1e1e1e;
  display: flex;
  align-items: center;
  padding: 0 20px;
}
.pill-input {
  flex: 1;
  min-width: 0;
  height: 20px;
  font-size: 14px;
  line-height: 20px;
  color: #fff;
}
.pill-input::placeholder {
  color: #8c8c8c;
}

/* 获取验证码：黑胶囊内右侧 灰字 fs12 */
.get-code {
  flex-shrink: 0;
  margin-left: 8px;
  white-space: nowrap;
  font-size: 12px;
  line-height: 17px;
  color: #555;
  cursor: pointer;
}
.get-code.disabled {
  color: #444;
}

/* 下一步：y366 x40 w295 h46 r12 深灰黑 */
.submit-btn {
  display: block;
  margin: 48px 40px 0;
  width: 295px;
  height: 46px;
  border-radius: 12px;
  background: #313131;
  color: #fff;
  font-size: 15px;
  font-weight: 600;
  line-height: 46px;
  text-align: center;
  padding: 0;
}
</style>
