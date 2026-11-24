<template>
  <div class="set-pwd-page">
    <!-- 返回（黑色箭头） -->
    <header class="page-header">
      <img class="back-icon" src="../../assets/images/back_btn.png" alt="" @click="goBack" />
    </header>

    <div class="form-area">
      <div class="page-title">设置新密码</div>

      <div class="input-pill">
        <input v-model="form.password" type="password" placeholder="请输入新密码" class="pill-input" />
      </div>

      <div class="input-pill second">
        <input v-model="form.password2" type="password" placeholder="请再次输入新密码" class="pill-input" />
      </div>

      <div class="hint">密码内需同时包含数字、字母，长度在6-12之间</div>

      <button class="submit-btn" @click="onConfirm">确认</button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../../api/request'
import { showToast } from '../../composables/sms'

const route = useRoute()
const router = useRouter()
const form = reactive({ password: '', password2: '' })

async function onConfirm() {
  if (form.password !== form.password2) {
    showToast('两次输入的密码不一致')
    return
  }
  if (!/(?=.*\d)(?=.*[a-zA-Z])/.test(form.password) || form.password.length < 6 || form.password.length > 12) {
    showToast('密码内需同时包含数字、字母，长度在6-12之间')
    return
  }
  const token = String(route.query.token || '')
  if (!token) {
    showToast('验证已过期，请重新验证')
    return
  }
  try {
    const res = await http.post('/api/user/resetpwd', {
      confirm_token: token,
      mobile: String(route.query.mobile || ''),
      password: form.password
    })
    if (res.code === 1) {
      showToast('密码重置成功')
      setTimeout(() => router.replace('/pages/login/login'), 1200)
    } else {
      showToast(res.msg || '设置失败')
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
.set-pwd-page {
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
  top: 14px;
  left: 22px;
  width: 11px;
  height: 19px;
  cursor: pointer;
}

.form-area {
  position: absolute;
  top: 70px;
  left: 0;
  right: 0;
}

/* 标题：x40 y70 fs18 加粗黑 居中 */
.page-title {
  margin: 0 40px;
  font-size: 18px;
  line-height: 26px;
  color: #000;
  font-weight: 600;
  text-align: center;
}

/* 黑色胶囊：x40 y238 w295 h50 r18 深黑 */
.input-pill {
  margin: 142px 40px 0;
  height: 50px;
  border-radius: 18px;
  background: #1e1e1e;
  display: flex;
  align-items: center;
  padding: 0 20px;
}
.input-pill.second {
  margin-top: 20px;
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

/* 提示：y358 fs10 灰（紧贴第二胶囊下方） */
.hint {
  margin: 0 40px;
  font-size: 10px;
  line-height: 20px;
  color: #7a7a7a;
}

/* 确认按钮：y446 x40 w295 h46 r12 深灰黑 */
.submit-btn {
  display: block;
  margin: 68px 40px 0;
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
