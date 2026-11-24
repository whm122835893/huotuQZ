<template>
  <div class="st-page">
    <div class="st-navbar">
      <img class="st-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="st-title">设置</div>
    </div>

    <div class="st-group-label">基本信息</div>
    <div class="st-card st-base">
      <img class="st-avatar" :src="avatar" alt="" />
      <span class="st-nick">{{ nickname }}</span>
      <img class="st-edit" :src="IMG.edit" alt="编辑" @click="toast('编辑个人信息')" />
    </div>

    <div class="st-group-label">账户</div>
    <div class="st-card">
      <div class="st-row" @click="toast('手机绑定')">
        <img class="st-ic" :src="IMG.phone" alt="" />
        <span class="st-row-txt">手机绑定</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
      <div class="st-row" @click="toast('实名认证')">
        <img class="st-ic" :src="IMG.cert" alt="" />
        <span class="st-row-txt">实名认证</span>
        <span class="st-row-hint">(当前已实名)</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
      <div class="st-row" @click="toast('操作密码')">
        <img class="st-ic" :src="IMG.pwd" alt="" />
        <span class="st-row-txt">操作密码</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
      <div class="st-row" @click="toast('隐私设置')">
        <img class="st-ic" :src="IMG.pwd" alt="" />
        <span class="st-row-txt">隐私设置</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
    </div>

    <div class="st-group-label">其他</div>
    <div class="st-card">
      <div class="st-row" @click="toast('版本更新')">
        <img class="st-ic" :src="IMG.update" alt="" />
        <span class="st-row-txt">版本更新</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
      <div class="st-row" @click="toast('注销账户')">
        <img class="st-ic" :src="IMG.logout" alt="" />
        <span class="st-row-txt">注销账户</span>
        <img class="st-arrow" :src="IMG.arrow" alt="" />
      </div>
    </div>

    <div class="st-footer"></div>
    <div class="st-signout" @click="signOut">退出登录</div>

    <div v-if="toastMsg" class="st-toast">{{ toastMsg }}</div>
  </div>
</template>

<script setup>
/* 批次3 占位落地页：设置（P023）
   原站「我的」页「设置」图标 /「编辑个人信息」按钮实测均跳 #/pages/my/setting。
   分组：基本信息 / 账户（手机绑定·实名认证·操作密码·隐私设置）/ 其他（版本更新·注销账户）
   + 底部「退出登录」。图标全部为原站 PNG 本地化。 */
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { useUserStore } from '../../stores/user'

const IMG = {
  back: new URL('../../assets/images/b17-back-btn.png', import.meta.url).href,
  edit: new URL('../../assets/images/b17-setting-edit.png', import.meta.url).href,
  phone: new URL('../../assets/images/b17-setting-phone-bind.png', import.meta.url).href,
  cert: new URL('../../assets/images/b17-setting-cert.png', import.meta.url).href,
  pwd: new URL('../../assets/images/b17-setting-change-password.png', import.meta.url).href,
  update: new URL('../../assets/images/b17-setting-update.png', import.meta.url).href,
  logout: new URL('../../assets/images/b17-setting-logout.png', import.meta.url).href,
  arrow: new URL('../../assets/images/icon-right.png', import.meta.url).href,
  avatarDefault: new URL('../../assets/images/mine-default-avatar.png', import.meta.url).href
}

const router = useRouter()
const user = useUserStore()
const info = ref({})
const toastMsg = ref('')
let toastTimer = null

const nickname = computed(() => info.value?.nickname || '175****1293')
const avatar = computed(() => IMG.avatarDefault)

function goBack() { router.back() }

function toast(msg) {
  toastMsg.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastMsg.value = '' }, 1600)
}

/** 退出登录：原站清空 token 回登录页，本项目等价走 store.logout + replace */
function signOut() {
  try { user.logout() } catch (e) { /* 静默 */ }
  router.replace('/pages/login/login')
}

onMounted(async () => {
  try {
    const r = await http.post('/api/user/getUserInfo', {})
    if (r && r.code === 1 && r.data) info.value = r.data
  } catch (e) { /* 401 由拦截器跳登录 */ }
})
</script>

<style scoped>
.st-page {
  --st-bg: #F4F6FA;
  --st-white: #FFFFFF;
  --st-dark: #212121;
  --st-sub: #787F8D;
  --st-line: rgba(33, 33, 33, 0.06);
  --st-black: #000000;
  min-height: 100vh;
  background: var(--st-bg);
  font-family: var(--ht-font);
  box-sizing: border-box;
  padding-bottom: 24px;
}
.st-navbar { height: 44px; position: relative; }
.st-back {
  position: absolute; left: 19px; top: 16px;
  width: 7px; height: 13px; cursor: pointer; z-index: 2;
}
.st-title {
  position: absolute; left: 0; right: 0; top: 11px; height: 23px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px; font-weight: 700; line-height: 23px; color: var(--st-black);
}
/* 原站实测：navbar 44 → 分组标签文字 y=56 → 卡片 y=81；
   后续标签距上一卡片结束 16px，标签行盒 17px + 下间距 8px = 25px 到卡片。
   行容器 y=190/247/304/361 与 458/515，行高 57px（本页原本即 57，无需改）。 */
.st-group-label {
  padding: 16px 0 8px 20px;
  font-size: 12px; line-height: 17px; color: var(--st-sub);
}
.st-navbar + .st-group-label { padding-top: 12px; }
.st-card {
  margin: 0 20px;
  background: var(--st-white); border-radius: 12px;
  box-sizing: border-box;
}
/* 基本信息卡原站 335×68 @(20,81)：头像 44×44 @(32,93)、昵称 y=105、编辑 20×20 @(323,105) */
.st-base {
  height: 68px; display: flex; align-items: center;
  padding: 0 12px;
}
.st-avatar { width: 44px; height: 44px; border-radius: 0; object-fit: cover; }
.st-nick {
  margin-left: 10px;
  font-size: 14px; font-weight: 500; line-height: 20px; color: var(--st-dark);
}
.st-edit { margin-left: auto; width: 20px; height: 20px; cursor: pointer; }
.st-row {
  height: 57px; display: flex; align-items: center;
  padding: 0 12px; cursor: pointer;
  border-bottom: 1px solid var(--st-line);
}
.st-row:last-child { border-bottom: none; }
.st-ic { width: 22px; height: 22px; object-fit: contain; flex-shrink: 0; }
.st-row-txt {
  margin-left: 8px;
  font-size: 14px; line-height: 20px; color: var(--st-dark);
  white-space: nowrap;
}
.st-row-hint {
  margin-left: 4px;
  font-size: 12px; line-height: 16px; color: var(--st-sub);
  white-space: nowrap;
}
.st-arrow { margin-left: auto; width: 10px; height: 10px; object-fit: contain; }
/* 原站底栏 @(0,744) 375×68 白，按钮 @(20,756) 335×44，文字 y=768。
   fixed 定位避免受上方分组高度累积影响。 */
.st-footer {
  position: fixed; left: 0; right: 0; bottom: 0; height: 68px;
  background: var(--st-white); z-index: 1;
}
.st-signout {
  position: fixed; left: 20px; right: 20px; bottom: 12px; z-index: 2;
  height: 44px; border-radius: 12px;
  background: var(--st-dark);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; line-height: 20px; color: var(--st-white);
  cursor: pointer; user-select: none;
}
.st-toast {
  position: fixed; left: 50%; bottom: 90px; transform: translateX(-50%);
  max-width: 300px; padding: 9px 16px; border-radius: 8px;
  background: rgba(33, 33, 33, 0.92); color: var(--st-white);
  font-size: 13px; line-height: 18px; text-align: center; z-index: 60;
}
</style>
