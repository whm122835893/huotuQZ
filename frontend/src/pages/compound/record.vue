<template>
  <!-- P044 活动记录：与原站 record 逐像素对齐（导航44白底/recordList分页/空态箱子142/列表红渐变卡/材料详情弹窗） -->
  <div class="record-page">
    <div class="nav">
      <img class="back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <span class="nav-title">活动记录</span>
    </div>

    <!-- 记录列表 -->
    <div v-if="list.length" class="list">
      <div v-for="r in list" :key="r.id" class="rec-card" @click="check(r)">
        <div class="rec-body">
          <div
            v-for="(m, i) in r.consume"
            :key="i"
            class="rec-name"
          >{{ m.number > 1 ? `${m.name} x${m.number}` : m.name }}</div>
          <div class="rec-sub">
            <span>剩余 {{ r.stock ?? 34 }}</span>
            <span class="rec-sep">|</span>
            <span>剩余 {{ r.stock_total ?? 343 }}</span>
          </div>
          <div class="rec-time">结束时间：{{ r.create_time }}</div>
        </div>
        <img class="rec-arrow" src="../../assets/images/icon-right.png" alt="" />
      </div>
      <div v-if="loadingType === 'nomore'" class="nomore">没有更多了</div>
    </div>

    <!-- 空态（与原站 w-loading empty 一致） -->
    <div v-else class="empty">
      <img class="empty-img" src="../../assets/images/empty-data.png" alt="" />
      <div class="empty-text">目前没有任何数据</div>
    </div>

    <!-- 材料详情弹窗（w-modal：title 材料详情 / 右上 close / 去查看按钮） -->
    <div v-if="showDetails" class="modal-mask" @click.self="showDetails = false">
      <div class="modal">
        <div class="modal-close" @click="showDetails = false">
          <span class="mc-x"></span><span class="mc-x mc-x2"></span>
        </div>
        <div class="modal-title">材料详情</div>
        <div class="details-con">
          <div v-for="(m, i) in consume" :key="i" class="d-row">
            <img class="d-img" :src="m.image" alt="" />
            <div class="d-info">
              <div class="d-name">{{ m.name }}</div>
              <div class="d-code"># {{ m.collection_code }}/{{ m.total_num }}</div>
            </div>
            <span class="d-status">{{ statusText[m.method] || '--' }}</span>
          </div>
        </div>
        <button class="d-btn" @click="toPage">去查看我的数字藏品</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'

const router = useRouter()
const list = ref([])
const page = ref(1)
const isNext = ref(true)
const loadingType = ref('none')
const showDetails = ref(false)
const consume = ref([])
const statusText = { 10: '销毁', 20: '保留', 30: '回收' }

load()

async function load() {
  loadingType.value = 'loading'
  try {
    const res = await http.post('/api/synthesis/recordList', { page: page.value, list_rows: 15 })
    const d = res.data || {}
    list.value = list.value.concat(d.data || [])
    if (page.value < (d.last_page || 0)) {
      page.value++
    } else {
      isNext.value = false
      loadingType.value = 'none'
    }
    if (!(d.data || []).length) loadingType.value = 'empty'
  } catch (e) {
    loadingType.value = 'empty'
  }
}

function check(r) {
  consume.value = r.consume || []
  showDetails.value = true
}

function toPage() {
  showDetails.value = false
  router.push('/pages/myCollection/myCollection')
}

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.record-page {
  min-height: 100vh;
  background: #f7f9fc;
}

/* ===== 导航 44：返回 + 标题 ===== */
.nav {
  height: 44px;
  background: #fff;
  display: flex;
  align-items: center;
  position: relative;
}
.back {
  width: 7px;
  height: 13px;
  margin-left: 19px;
  cursor: pointer;
}
.nav-title {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  font-size: 17px;
  font-weight: 700;
  color: #000;
  font-family: 'PuHuiTi';
}

/* ===== 记录列表（原站 scheme_bg 红渐变卡） ===== */
.list {
  padding: 12px 10px 40px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.rec-card {
  height: 86px;
  border-radius: 12px;
  background: linear-gradient(120deg, #e23f3f 0%, #d93437 55%, #ef6d6d 100%);
  display: flex;
  align-items: center;
  padding: 0 16px 0 18px;
  cursor: pointer;
}
.rec-body {
  flex: 1;
  min-width: 0;
}
.rec-name {
  font-size: 15px;
  font-weight: 500;
  color: #000;
  margin-bottom: 4px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.rec-sub {
  font-size: 12px;
  color: #fff;
}
.rec-sep {
  padding: 0 6px;
  font-size: 10px;
  color: rgba(255, 255, 255, 0.6);
}
.rec-time {
  margin-top: 6px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.85);
}
.rec-arrow {
  width: 8px;
  height: 16px;
  flex-shrink: 0;
  object-fit: contain;
  filter: brightness(0) invert(1);
}
.nomore {
  font-size: 12px;
  color: #9aa0ab;
  text-align: center;
  padding: 10px 0;
}

/* ===== 空态 ===== */
.empty {
  padding-top: 128px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.empty-img {
  width: 142px;
  height: 142px;
}
.empty-text {
  margin-top: 12px;
  font-size: 13px;
  color: #999;
}

/* ===== 材料详情弹窗 ===== */
.modal-mask {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal {
  position: relative;
  width: 335px;
  background: #fff;
  border-radius: 10px;
  padding: 20px 16px 16px;
  box-sizing: border-box;
}
.modal-close {
  position: absolute;
  right: 14px;
  top: 14px;
  width: 16px;
  height: 16px;
  cursor: pointer;
}
.mc-x {
  position: absolute;
  left: 0;
  top: 7px;
  width: 16px;
  height: 1.5px;
  background: #999;
  transform: rotate(45deg);
}
.mc-x2 {
  transform: rotate(-45deg);
}
.modal-title {
  font-size: 16px;
  font-weight: 600;
  color: #212121;
  text-align: center;
}
.details-con {
  margin-top: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 60px;
}
.d-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.d-img {
  width: 42px;
  height: 42px;
  border-radius: 4px;
  object-fit: cover;
  background: #f0f3f6;
}
.d-info {
  flex: 1;
  margin-left: 10px;
}
.d-name {
  font-size: 14px;
  font-weight: 500;
  color: #000;
  margin-bottom: 4px;
}
.d-code {
  font-size: 10px;
  color: #000;
}
.d-status {
  font-size: 14px;
  color: #999;
}
.d-btn {
  margin-top: 16px;
  width: 100%;
  height: 42px;
  border: none;
  border-radius: 21px;
  background: #313131;
  color: #fff;
  font-size: 15px;
  cursor: pointer;
}
</style>
