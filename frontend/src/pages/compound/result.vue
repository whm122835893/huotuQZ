<template>
  <!-- P045 参与结果：与原站 result 逐像素对齐（全屏遮罩rgba(0,0,0,0.55)/弹窗264x340 compound-modal-bg/X钮24白圈/cover图100x100 r8/确认热区180x44） -->
  <div class="result-page">
    <div class="modal-wrap">
      <div class="mask"></div>
      <div class="modal">
        <img class="modal-bg" src="../../assets/images/compound-modal-bg.png" alt="" />

        <!-- 关闭（右上白圈 ×） -->
        <div class="close" @click="onClose">×</div>

        <!-- 结果封面 -->
        <div class="cover-box">
          <img v-if="coverImage" class="cover" :src="coverImage" alt="" />
        </div>

        <!-- 确认热区（图内确认按钮区域） -->
        <div class="confirm-zone" @click="onConfirm"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../../api/request'

const route = useRoute()
const router = useRouter()
const result = ref([])

const firstItem = computed(() => (result.value && result.value[0]) || {})
const coverImage = computed(() => firstItem.value.image || firstItem.value.main_image || '')

const recordId = route.query.id || ''
if (recordId) {
  getDetails()
} else {
  showToast('缺少合成记录ID')
}

async function getDetails() {
  try {
    const res = await http.post('/api/synthesis/recordInfo', { record_id: recordId })
    result.value = (res.data && res.data.result) || []
  } catch (e) {
    showToast('获取合成结果失败')
  }
}

function showToast(msg) {
  let el = document.querySelector('.ht-toast')
  if (!el) {
    el = document.createElement('div')
    el.className = 'ht-toast'
    document.body.appendChild(el)
  }
  el.textContent = msg
  el.style.display = 'block'
  setTimeout(() => (el.style.display = 'none'), 2000)
}

function onConfirm() {
  onClose()
}
function onClose() {
  if (window.history.length > 1) router.back()
  else router.push('/pages/compound/compound')
}
</script>

<style scoped>
.result-page {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mask {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
}
.modal {
  position: relative;
  z-index: 1;
  width: 264px;
  height: 340px;
}
.modal-bg {
  position: absolute;
  left: 0;
  top: 0;
  width: 264px;
  height: 340px;
  display: block;
}
.close {
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
.cover-box {
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
.cover {
  width: 100px;
  height: 100px;
  object-fit: cover;
  display: block;
}
.confirm-zone {
  position: absolute;
  left: 42px;
  bottom: 24px;
  z-index: 2;
  width: 180px;
  height: 44px;
  cursor: pointer;
}
</style>
