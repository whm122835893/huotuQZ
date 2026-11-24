import { defineStore } from 'pinia'

const TOKEN_KEY = 'ht_token'
const USER_KEY = 'ht_userinfo'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: localStorage.getItem(TOKEN_KEY) || '',
    userinfo: JSON.parse(localStorage.getItem(USER_KEY) || 'null')
  }),
  getters: {
    isLogin: (s) => !!s.token
  },
  actions: {
    setLogin(data) {
      this.token = data.token
      this.userinfo = data
      localStorage.setItem(TOKEN_KEY, data.token)
      localStorage.setItem(USER_KEY, JSON.stringify(data))
    },
    logout() {
      this.token = ''
      this.userinfo = null
      localStorage.removeItem(TOKEN_KEY)
      localStorage.removeItem(USER_KEY)
    }
  }
})
