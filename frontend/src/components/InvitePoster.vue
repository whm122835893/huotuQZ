<template>
  <div class="ip-mask" @click.self="close">
    <div class="ip-wrap">
      <div class="ip-card">
        <img class="ip-bg" :src="IMG.posterBg" alt="邀请海报" />
        <!-- 左上：头像 + 昵称 + ID（原站 @(72,218)/(110,218)/(110,236)） -->
        <div class="ip-user">
          <img class="ip-avatar" :src="IMG.avatar" alt="" />
          <div class="ip-user-meta">
            <span class="ip-nick">{{ nickname }}</span>
            <span class="ip-uid">ID:{{ uid }}</span>
          </div>
        </div>
        <!-- 左下：邀请码（原站 HT173634 @(118,486)） -->
        <div class="ip-code-box">
          <span class="ip-code">{{ code }}</span>
        </div>
        <!-- 右下：二维码（原站 60×60 @(245,428)） -->
        <div class="ip-qr-wrap">
          <img class="ip-qr" :src="IMG.qrcode" alt="二维码" />
        </div>
      </div>

      <div class="ip-actions">
        <div class="ip-btn ip-btn--copy" @click="copyLink">复制链接</div>
        <div class="ip-btn ip-btn--save" @click="savePoster">保存海报</div>
      </div>

      <img class="ip-close" :src="IMG.close" alt="关闭" @click="close" />

      <div v-if="toast" class="ip-toast">{{ toast }}</div>
    </div>
  </div>
</template>

<script setup>
/* 「我的」页「邀请海报」按钮：原站实测点击后不跳转，在 my 页内弹出海报浮层
   （OPEN-DECISIONS 第 3 条，用户「把批次三的占位做掉」授权补做）。
   背景遮罩 + 海报卡（原站完整插画 mine-invite-modal-bg.png）+ 动态头像/昵称/ID、
   邀请码 HT173634、二维码、「复制链接」「保存海报」按钮、关闭 X。 */
import { ref, onMounted } from 'vue'
import http from '../api/request'
import { IMG, POSTER, inviteLinkText } from '../pages/rebate/demo'

const emit = defineEmits(['close'])
const nickname = ref('175****1293')
const uid = ref('173634')
const code = ref(POSTER.code)
const toast = ref('')
let toastTimer = null

function close() { emit('close') }

function showToast(msg) {
  toast.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = '' }, 1600)
}

/** 原站「复制链接」-> uni.setClipboardData(h5_invite_url)；串如实为 undefined&content=HT173634 */
function copyLink() {
  const url = inviteLinkText(code.value)
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url)
    } else {
      const el = document.createElement('textarea')
      el.value = url
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
    }
    showToast(POSTER.toastCopy)
  } catch (e) { showToast(POSTER.toastCopy) }
}

/** 原站「保存海报」为 canvas 绘制后写相册；本地 H5 无相册写权限，
 *  按批次14 钱包同款处理：给出明确结果反馈，不臆造下载能力。 */
function savePoster() { showToast(POSTER.toastSave) }

onMounted(async () => {
  try {
    const r = await http.post('/api/user/getUserInfo', {})
    if (r && r.code === 1 && r.data) {
      if (r.data.nickname) nickname.value = r.data.nickname
      if (r.data.origin_uid) uid.value = r.data.origin_uid
      else if (r.data.id) uid.value = r.data.id
      if (r.data.inviter_code) code.value = r.data.inviter_code
    }
  } catch (e) { /* 接口不可用保持原站侦查快照默认值，不臆造 */ }
})
</script>

<style scoped>
.ip-mask {
  /* 颜色全部 Token 化（取值来自原站实测），组件内不出现裸十六进制色值 */
  --ip-dark: #212121;
  --ip-sub: #787F8D;
  --ip-muted: #9AA0AB;
  --ip-red: #D93437;
  --ip-white: #FFFFFF;
  position: fixed; inset: 0; z-index: 100;
  background: rgba(0, 0, 0, 0.7);
  display: flex; align-items: flex-start; justify-content: center;
}
/* 原站 popup @(30,162) 315×488：海报卡 388 + 按钮行 44 + 关闭 36。
   flex-start + 精确 margin-top，避免整块随内容高度浮动导致 y 偏移。 */
.ip-wrap {
  position: relative; margin-top: 162px;
  width: 315px;
  display: flex; flex-direction: column; align-items: center;
}
/* 海报卡：原站 315×388 @(30,162)，背景为完整插画 */
/* 原站卡片 br=0 / bg 透明：海报 PNG 四角本身带透明圆角，
   不能加白底与 border-radius，否则四角会多出一圈白边（整图 MAE 偏高）。 */
.ip-card {
  position: relative;
  width: 315px; height: 388px;
  border-radius: 0; overflow: hidden;
  background: transparent;
}
.ip-bg { width: 315px; height: 388px; display: block; }
/* 头像区 @(72,218) 109×32：昵称 @(110,218) 12px/500、ID @(110,236) 10px */
.ip-user {
  position: absolute; left: 42px; top: 56px;
  display: flex; align-items: center;
}
.ip-avatar { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }
.ip-user-meta {
  margin-left: 6px; display: flex; flex-direction: column;
}
.ip-nick { font-size: 12px; line-height: 16px; font-weight: 500; color: var(--ip-dark); }
.ip-uid { margin-top: 2px; font-size: 10px; line-height: 14px; color: var(--ip-muted); }
/* 原站该处仅纯文字 HT173634 @(118,486) 12px/500 灰字，
   无「邀请码」标签、无底色胶囊；按不臆造去掉此前多加的标签与白底。 */
.ip-code-box {
  position: absolute; left: 88px; top: 320px;
}
.ip-code {
  font-size: 12px; line-height: 17px; font-weight: 500; color: var(--ip-sub);
}
.ip-qr-wrap {
  position: absolute; right: 20px; top: 266px;
  width: 60px; height: 60px; border-radius: 8px;
  background: var(--ip-white); overflow: hidden;
}
.ip-qr { width: 60px; height: 60px; display: block; }

/* 原站按钮行 @(30,550) 315×44：红按钮 @(48,550) 136×44 br=10px、
   深色按钮 @(192,550) 136×44；文字「复制链接」@(88,562)、「保存海报」@(232,562) 14px/500 */
.ip-actions {
  margin-top: 0; width: 315px; height: 44px;
  display: flex; justify-content: center; gap: 8px;
}
.ip-btn {
  width: 136px; flex: 0 0 136px; height: 44px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; line-height: 20px; font-weight: 500; color: var(--ip-white);
  cursor: pointer; user-select: none;
}
.ip-btn--copy { background: var(--ip-red); }
.ip-btn--save { background: var(--ip-dark); }

/* 关闭 X：原站 36×36 @(170,614) */
.ip-close {
  margin-top: 20px;
  width: 36px; height: 36px; cursor: pointer;
}
.ip-toast {
  position: fixed; left: 50%; bottom: 90px; transform: translateX(-50%);
  padding: 9px 16px; border-radius: 8px;
  background: rgba(33, 33, 33, 0.92); color: var(--ip-white);
  font-size: 13px; line-height: 18px; z-index: 110;
}
</style>
