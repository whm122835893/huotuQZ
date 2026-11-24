import axios from 'axios'
import { useUserStore } from '../stores/user'
import router from '../router'

/**
 * 独立后端请求封装 —— 仅调用本项目自有 ThinkPHP 后端（/api 前缀经 vite 代理），
 * 不依赖原站任何接口（项目铁律第 9 条）。
 */
const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE || '/',
  timeout: 15000,
  headers: { version: '1.0.0' }
})

http.interceptors.request.use((config) => {
  const user = useUserStore()
  if (user.token) {
    config.headers.token = user.token
    /* 发布域名代理层会剥离自定义请求头，token 同时写入请求体兜底 */
    if (config.data && typeof config.data === 'object') {
      config.data.token = user.token
    } else {
      config.data = { token: user.token }
    }
  }
  return config
})

http.interceptors.response.use(
  (res) => {
    const body = res.data
    if (body && typeof body.code !== 'undefined') {
      if (body.code === 401) {
        const user = useUserStore()
        user.logout()
        router.replace('/pages/login/login')
        return Promise.reject(new Error('请先登录'))
      }
      return body
    }
    return body
  },
  (err) => Promise.reject(err)
)

export default http
