<template>
  <!-- P046 合成详情：与原站 schemeDetails 逐像素对齐（CSS渐变#E89294→#F3B6B8 28%→#F7F9FC 62%→#FFF/透明白字导航/通道tab h36 bg白45%/圆台375/白面板/确认合成48黑钮） -->
  <div class="scheme-page">
    <div class="content">
      <!-- 导航：透明底白字 + 白返回 -->
      <div class="nav">
        <img class="back" src="../../assets/images/back_back_white.png" alt="返回" @click="goBack" />
        <span class="nav-title">合成详情</span>
      </div>

      <!-- 通道 tab（原站静态渲染：通道A 选中红底，通道B/C 不显示文字） -->
      <div class="channel-wrap">
        <div
          v-for="(c, i) in channelTabs"
          :key="i"
          class="channel-item"
          :style="{ backgroundColor: i === 0 ? '#e60012' : 'transparent' }"
        >
          <span v-if="i === 0" class="channel-name">{{ c.name }}</span>
        </div>
      </div>

      <!-- 圆台展示区 -->
      <div class="stage">
        <img class="stage-base" src="../../assets/images/compound-image-base.png" alt="" />
        <div class="goods-box">
          <img v-if="image" class="goods-img" :src="image" alt="" />
        </div>
      </div>

      <!-- 倒计时（活动未结束时显示） -->
      <div v-if="countdown" class="countdown">
        <span class="cd-label">{{ countdown.label }}</span>
        <template v-for="(p, i) in countdown.parts" :key="i">
          <span class="cd-box">{{ p.value }}</span>
          <span v-if="p.suffix" class="cd-suffix">{{ p.suffix }}</span>
        </template>
      </div>
    </div>

    <!-- 底部白色信息面板 -->
    <div class="panel">
      <div class="row">
        <img class="row-icon" src="../../assets/images/compound-remaining.png" alt="" />
        <span class="row-title">剩余数量</span>
      </div>
      <div class="stock-num">{{ userMax || '--' }}份</div>

      <div class="row row-mt">
        <img class="row-icon" src="../../assets/images/compound-shares.png" alt="" />
        <span class="row-title">活动材料</span>
      </div>

      <!-- 材料方案（playInfo.rule_text，当前与原站一致为空） -->
      <div v-for="(r, i) in ruleText" :key="i" class="material-row">
        <span class="material-name">{{ r.name }} x{{ r.number }}</span>
        <span class="material-count">已选择 {{ selectedCount(r) }}/{{ r.number }}</span>
      </div>
    </div>

    <!-- 底部固定按钮 -->
    <div class="footer">
      <div class="confirm-btn" :class="{ disabled: isDisabled }" @click="handleButtonClick">确认合成</div>
    </div>

    <!-- 合成确认弹窗 -->
    <div v-if="confimPop" class="pop-mask" @click.self="confimPop = false">
      <div class="pop">
        <div class="pop-content">{{ popContent }}</div>
        <div class="pop-actions">
          <button class="pop-btn pop-cancel" @click="confimPop = false">再想想</button>
          <button class="pop-btn pop-ok" @click="submit">确认</button>
        </div>
      </div>
    </div>

    <!-- 合成结果弹窗（showResultPop） -->
    <div v-if="showResultPop" class="result-mask">
      <div class="result-modal">
        <img class="rm-bg" src="../../assets/images/compound-modal-bg.png" alt="" />
        <div class="rm-close" @click="closeResultPop">×</div>
        <div class="rm-cover-box">
          <img v-if="resultCover" class="rm-cover" :src="resultCover" alt="" />
        </div>
        <div class="rm-confirm-zone" @click="closeResultPop"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../../api/request'

const route = useRoute()
const router = useRouter()

const param = ref({ play_id: route.query.play_id || '', grp: 'A', classify: route.query.classify || '' })
const image = ref('')
const title = ref('')
const description = ref('')
const ruleText = ref([])
const start_time = ref(0)
const end_time = ref(0)
const userMax = ref('')
const selected = ref({})

const confimPop = ref(false)
const showResultPop = ref(false)
const resultList = ref([])

const channelTabs = [{ name: '通道A' }, { name: '通道B' }, { name: '通道C' }]

let timer = null
const now = ref(Date.now())
onMounted(() => {
  getDetails()
  timer = setInterval(() => (now.value = Date.now()), 1000)
})
onUnmounted(() => clearInterval(timer))

async function getDetails() {
  try {
    const res = await http.post('/api/synthesis/playInfo', param.value)
    const d = res.data || {}
    image.value = d.image || ''
    title.value = d.title || ''
    description.value = d.description || ''
    ruleText.value = d.rule_text || []
    start_time.value = Number(d.start_time || 0)
    end_time.value = Number(d.end_time || 0)
    userMax.value = d.user_max || ''
  } catch (e) {
    /* 活动已结束/玩法不存在：与原站一致保留骨架渲染 */
  }
}

/* ===== 倒计时（与原站 countdownInfo 一致：结束态不显示） ===== */
function timeStatus() {
  const s = start_time.value
  const e = end_time.value
  if (!s || !e) return 2
  if (now.value < s) return 0
  if (now.value > e) return 2
  return 1
}
const countdown = computed(() => {
  const s = start_time.value
  const e = end_time.value
  if (!s || !e) return null
  const st = timeStatus()
  if (st === 2) return null
  const target = st === 0 ? s : e
  const label = st === 0 ? '距离开始' : '距离结束'
  let t = Math.max(0, target - now.value)
  const d = Math.floor(t / 864e5)
  t %= 864e5
  const h = Math.floor(t / 36e5)
  t %= 36e5
  const m = Math.floor(t / 6e4)
  const sec = Math.floor((t % 6e4) / 1e3)
  const f = (n) => String(n).padStart(2, '0')
  return {
    label,
    parts: [
      { value: f(d), suffix: '天' },
      { value: f(h), suffix: ':' },
      { value: f(m), suffix: ':' },
      { value: f(sec), suffix: '' }
    ]
  }
})

/* ===== 材料选择 ===== */
function selectedCount(r) {
  return selected.value[r.name] ? selected.value[r.name].length : 0
}
const isDisabled = computed(() => {
  /* 与原站一致：材料方案为空 → 全部已满足（0==0）→ 按钮可用态 */
  return ruleText.value.some((r) => selectedCount(r) !== r.number)
})
const popContent = computed(() => {
  const total = ruleText.value.reduce((s, r) => s + r.number, 0)
  return `您选择了${total}个藏品，合成成功后将进行销毁/回收/保留，确认合成？`
})

let toastTimer = null
function showToast(msg) {
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

function handleButtonClick() {
  if (isDisabled.value) return
  confimPop.value = true
}

async function submit() {
  confimPop.value = false
  try {
    const res = await http.post('/api/synthesis/synthesizeNow', param.value)
    resultList.value = (res.data && res.data.resultList) || []
    showResultPop.value = true
  } catch (e) {
    showToast((e && e.msg) || '合成材料不足')
  }
}

const resultCover = computed(() => {
  const first = (resultList.value && resultList.value[0]) || {}
  return first.image || first.main_image || ''
})
function closeResultPop() {
  showResultPop.value = false
}

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/pages/compound/compound')
}
</script>

<style scoped>
.scheme-page {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  padding-bottom: 80px;
  background: linear-gradient(#e89294, #f3b6b8 28%, #f7f9fc 62%, #ffffff);
}

.content {
  position: relative;
  z-index: 1;
}

/* ===== 导航：透明底白字 ===== */
.nav {
  height: 44px;
  display: flex;
  align-items: center;
  position: relative;
}
.back {
  width: 8px;
  height: 14px;
  margin-left: 6px;
  cursor: pointer;
}
.nav-title {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  font-family: 'PuHuiTi';
  font-size: 17px;
  font-weight: 500;
  color: #fff;
}

/* ===== 通道 tab ===== */
.channel-wrap {
  margin: 8px 20px 0;
  height: 36px;
  padding: 3px;
  box-sizing: border-box;
  background: rgba(255, 255, 255, 0.45);
  border-radius: 10px;
  display: flex;
}
.channel-item {
  flex: 1;
  height: 30px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.channel-name {
  font-family: 'PuHuiTi';
  font-size: 13px;
  color: #fff;
  line-height: 18px;
}

/* ===== 圆台展示区 ===== */
.stage {
  position: relative;
  width: 375px;
  height: 375px;
  margin-top: 16px;
}
.stage-base {
  position: absolute;
  left: 0;
  top: 40px;
  width: 375px;
  height: 375px;
  z-index: 1;
}
.goods-box {
  position: absolute;
  left: 117px;
  top: 80px;
  z-index: 2;
  width: 140px;
  height: 140px;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
}
.goods-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ===== 倒计时 ===== */
.countdown {
  margin-top: 4px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.cd-label {
  font-family: 'PuHuiTi';
  font-size: 12px;
  color: #666;
  line-height: 17px;
  margin-right: 8px;
}
.cd-box {
  min-width: 22px;
  height: 22px;
  padding: 0 4px;
  border-radius: 4px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  font-family: 'Bebas Neue', 'PuHuiTi', sans-serif;
  font-size: 16px;
  color: #333;
  line-height: 16px;
  text-align: center;
}
.cd-suffix {
  margin: 0 5px;
  font-family: 'PuHuiTi';
  font-size: 12px;
  color: #666;
  line-height: 17px;
}

/* ===== 底部白色信息面板 ===== */
.panel {
  background: #fff;
  border-radius: 20px 20px 0 0;
  padding: 16px 20px 20px;
  box-sizing: border-box;
}
.row {
  display: flex;
  align-items: center;
}
.row-mt {
  margin-top: 16px;
}
.row-icon {
  width: 18px;
  height: 18px;
  margin-right: 4px;
}
.row-title {
  font-family: 'PuHuiTi';
  font-size: 14px;
  color: #212121;
  line-height: 20px;
}
.stock-num {
  display: block;
  margin-top: 4px;
  margin-left: 22px;
  font-family: 'PuHuiTi';
  font-size: 16px;
  color: #212121;
  line-height: 22px;
}
.material-row {
  margin-top: 10px;
  padding: 10px 12px;
  background: #f4f6fa;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.material-name {
  font-size: 13px;
  color: #212121;
  font-weight: 500;
}
.material-count {
  font-size: 12px;
  color: #999;
}

/* ===== 底部固定按钮 ===== */
.footer {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 10;
  padding: 10px 20px;
  padding-bottom: calc(10px + env(safe-area-inset-bottom));
  background: #fff;
  box-sizing: border-box;
}
.confirm-btn {
  width: 100%;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #212121;
  color: #fff;
  font-family: 'PuHuiTi';
  font-weight: 500;
  font-size: 16px;
  cursor: pointer;
}
.confirm-btn.disabled {
  background: #e8ebf0;
  cursor: default;
}

/* ===== 合成确认弹窗 ===== */
.pop-mask {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1500;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pop {
  width: 335px;
  background: #fff;
  border-radius: 10px;
  padding: 24px 20px 16px;
  box-sizing: border-box;
}
.pop-content {
  font-size: 14px;
  color: #212121;
  line-height: 22px;
  text-align: center;
}
.pop-actions {
  margin-top: 20px;
  display: flex;
  gap: 12px;
}
.pop-btn {
  flex: 1;
  height: 42px;
  border: none;
  border-radius: 21px;
  font-size: 15px;
  cursor: pointer;
}
.pop-cancel {
  background: #dadada;
  color: #2e2e2e;
}
.pop-ok {
  background: #313131;
  color: #fff;
}

/* ===== 合成结果弹窗 ===== */
.result-mask {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(33, 33, 33, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
}
.result-modal {
  position: relative;
  width: 264px;
  height: 340px;
}
.rm-bg {
  position: absolute;
  left: 0;
  top: 0;
  width: 264px;
  height: 340px;
  display: block;
}
.rm-close {
  position: absolute;
  right: 8px;
  top: 8px;
  z-index: 2;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 1px solid #fff;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  line-height: 14px;
  color: #fff;
  cursor: pointer;
  user-select: none;
}
.rm-cover-box {
  position: absolute;
  left: 82px;
  top: 124px;
  z-index: 1;
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
}
.rm-cover {
  width: 100px;
  height: 100px;
  object-fit: cover;
  display: block;
}
.rm-confirm-zone {
  position: absolute;
  left: 42px;
  bottom: 24px;
  z-index: 2;
  width: 180px;
  height: 44px;
  cursor: pointer;
}
</style>
