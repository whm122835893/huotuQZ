import { ref } from 'vue'
import http from '../api/request'

/** 轻提示（全站统一深色圆角块） */
let toastTimer = null
export function showToast(msg) {
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

/** 手机号脱敏 175****1293 */
export function maskMobile(m) {
  if (/^1\d{10}$/.test(m)) return m.slice(0, 3) + '****' + m.slice(7)
  return m
}

/**
 * 获取验证码 + 60 秒倒计时
 * @param {string} event register | login | resetpwd
 */
export function useSmsCode(event) {
  const counting = ref(false)
  const seconds = ref(0)
  let timer = null

  async function send(mobile, { silent = false } = {}) {
    if (counting.value) return null
    if (!/^1\d{10}$/.test(mobile)) {
      showToast('请输入正确的手机号')
      return null
    }
    try {
      const res = await http.post('/api/sms/send', { mobile, event })
      if (res.code === 1) {
        if (!silent) showToast('验证码已发送')
        // 沙箱无真实短信通道：后端将验证码回传用于测试（原站为真实短信）
        if (res.data?.mock_code) showToast('验证码已发送：' + res.data.mock_code)
        counting.value = true
        seconds.value = 60
        timer = setInterval(() => {
          seconds.value--
          if (seconds.value <= 0) {
            clearInterval(timer)
            counting.value = false
          }
        }, 1000)
        return res.data?.mock_code || null
      }
      showToast(res.msg || '发送失败')
    } catch (e) {
      showToast('网络异常，请重试')
    }
    return null
  }

  return { counting, seconds, send }
}
