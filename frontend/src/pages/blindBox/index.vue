<template>
  <!-- P073 我的盲盒：storage(blindBoxInfo) 驱动；原站默认空态 = 灰占位方块 + 按钮群 -->
  <div class="bb-page bbi-page">
    <div class="bb-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">我的盲盒</span>
    </div>

    <div class="bbi-main">
      <div class="bbi-box">
        <img v-if="info.listimg" class="bbi-cover bb-ph" :src="localImg(info.listimg)" alt="" />
        <div v-else class="bbi-cover bb-ph"></div>
      </div>
      <div class="bbi-count">
        <span>共有</span><b v-if="info.num !== undefined && info.num !== ''">{{ info.num }}</b><span>个</span>
      </div>
      <div class="bbi-open" @click="onOpenAll"><span>一键开启</span></div>
      <div class="bbi-tip">一次最多打开10个盲盒</div>
    </div>

    <div class="bbi-footer">
      <div class="bbi-fb bbi-consignment" @click="onConsign"><span>立即寄售</span></div>
      <div class="bbi-fb bbi-apart" @click="onTakeApart"><span>逐个拆开</span></div>
    </div>

    <!-- 逐个拆开：原站 5 秒黑色遮罩动画 -->
    <div v-if="showPop" class="bbi-pop"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, readStore, writeStore, localImg, DEMO_BOX_INFO, DEMO_RESULT_LIST, DEMO_RESULT } from './demo'
import './common.css'

const router = useRouter()
const showPop = ref(false)
const info = ref(readStore('blindBoxInfo', {}))
if (isDemo()) info.value = { ...DEMO_BOX_INFO }

function goBack() { router.back() }
function onConsign() {
  router.push({ path: '/pages/consignments/consignments', query: { id: info.value.id, pageType: 1 } })
}

/* 原站：一键开启 -> openBoxs(blindBoxId) -> setStorage('blindBoxResultList', data) -> result */
async function onOpenAll() {
  if (isDemo()) {
    writeStore('blindBoxResultList', DEMO_RESULT_LIST)
    router.replace('/pages/blindBox/result?demo=1')
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/openBoxs', { openid: info.value.blindBoxId })
    if (res && res.code === 1 && res.data) {
      writeStore('blindBoxResultList', res.data)
      router.replace('/pages/blindBox/result')
    }
  } catch (e) { /* 原站 code:0 静默 */ }
}

/* 原站：逐个拆开 -> openBox -> 黑遮罩 5s -> setStorage('blindBoxResult', data) -> result2 */
async function onTakeApart() {
  if (isDemo()) {
    showPop.value = true
    setTimeout(() => {
      showPop.value = false
      writeStore('blindBoxResult', DEMO_RESULT)
      router.replace('/pages/blindBox/result2?demo=1')
    }, 5000)
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/openbox', { openid: info.value.blindBoxId })
    if (res && res.code === 1 && res.data) {
      showPop.value = true
      setTimeout(() => {
        showPop.value = false
        writeStore('blindBoxResult', res.data)
        router.replace('/pages/blindBox/result2')
      }, 5000)
    }
  } catch (e) { /* 原站 code:0 静默 */ }
}
</script>

<style scoped>
/* 主区 flex 居中，padding 24px 20px 100px，列间距 16px（原站 gap-pf-y-32） */
.bbi-main {
  flex: 1;
  min-height: 634px;
  padding: 24px 20px 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
}
.bbi-box {
  width: 240px; height: 240px;
  border: 4px solid var(--bb-card);
  border-radius: 20px;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
  background: var(--bb-card);
  overflow: hidden;
}
.bbi-cover { display: block; width: 232px; height: 232px; border-radius: 16px; object-fit: cover; }
.bbi-count {
  height: 28px; padding: 0 16px;
  background: var(--bb-card); border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; line-height: 17px; color: var(--bb-gray);
}
.bbi-count b {
  font-family: 'Bebas Neue', 'Arial Narrow', sans-serif;
  font-size: 18px; font-weight: 400; line-height: 21px; color: var(--bb-red);
}
.bbi-open {
  width: 250px; height: 44px;
  background: var(--bb-red); border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  color: var(--bb-card); font-size: 16px; font-weight: 500; cursor: pointer;
}
.bbi-tip { font-size: 11px; line-height: 16px; color: var(--bb-sub); }

/* 底部固定栏 62px，两按钮 flex:1 高 44，间距 10px（gap-pf-x-20） */
.bbi-footer {
  position: fixed; left: 0; right: 0; bottom: 0; z-index: 10;
  max-width: 750px; margin: 0 auto;
  height: 62px; padding: 9px 16px;
  background: var(--bb-card);
  display: flex; align-items: center; gap: 10px;
}
.bbi-fb {
  flex: 1; height: 44px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; cursor: pointer;
}
.bbi-consignment { background: var(--bb-card); border: 1px solid var(--bb-ink); color: var(--bb-ink); }
.bbi-apart { background: var(--bb-red); color: var(--bb-card); }

.bbi-pop { position: fixed; inset: 0; z-index: 20; background: var(--bb-black); }
</style>
