<template>
  <!-- P041 发表辟谣(sendRumor)：diy导航(取消/发表)+内容输入+图片上传(3张100px)+匿名发表开关 -->
  <div class="send-page">
    <!-- 自定义导航 -->
    <div class="nav">
      <span class="cancel" @click="goBack">取消</span>
      <button class="publish" :disabled="submitting" @click="submit">发表</button>
    </div>

    <div class="body">
      <!-- 内容输入 -->
      <div class="input-box">
        <textarea
          v-model="form.content"
          class="content-input"
          placeholder="请输入内容"
          maxlength="-1"
        ></textarea>
      </div>

      <!-- 图片上传（最多3张 100x100） -->
      <div class="upload-row">
        <div v-for="(img, i) in form.images" :key="'img-' + i" class="upload-item">
          <img class="upload-img" :src="img" alt="" />
          <span class="upload-del" @click="deleteImage(i)">×</span>
        </div>
        <div v-if="form.images.length < 3" class="upload-box" @click="chooseImage">
          <svg v-show="form.images.length === 0" class="up-ic" viewBox="0 0 24 24" fill="none" stroke="#bababa" stroke-width="1.6">
            <rect x="3" y="3" width="18" height="18" rx="3" />
            <circle cx="9" cy="9" r="2" />
            <path d="M3 17l5-5 4 4 3-3 6 6" />
          </svg>
          <svg v-show="form.images.length > 0" class="up-ic" viewBox="0 0 24 24" fill="none" stroke="#8b8b8b" stroke-width="1.8">
            <path d="M12 5v14M5 12h14" />
          </svg>
          <span v-show="form.images.length === 0" class="upload-text">照片</span>
        </div>
      </div>

      <!-- 匿名发表 -->
      <div class="anon-row">
        <span class="anon-label">匿名发表</span>
        <button
          class="switch"
          :class="{ on: form.is_anonymous === 1 }"
          @click="form.is_anonymous = form.is_anonymous === 1 ? 0 : 1"
        >
          <span class="switch-dot"></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import http from '../../api/request'

const ICON = (n) => new URL(`../../assets/images/${n}`, import.meta.url).href

const form = reactive({ content: '', images: [], is_anonymous: 0 })
const submitting = ref(false)

function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = '#/pages/notice/notice'
}

function chooseImage() {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = () => {
    const file = input.files && input.files[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = () => {
      if (form.images.length < 3) form.images.push(reader.result)
    }
    reader.readAsDataURL(file)
  }
  input.click()
}
function deleteImage(i) {
  form.images.splice(i, 1)
}

async function submit() {
  if (submitting.value) return
  if (!form.content.trim()) return
  submitting.value = true
  try {
    await http.post('/api/discover/rumor_wall/release', {
      content: form.content,
      images: form.images,
      is_anonymous: form.is_anonymous
    })
    goBack()
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.send-page {
  min-height: 100vh;
  background: #fff;
}

/* ===== 自定义导航 ===== */
.nav {
  height: 44px;
  padding: 0 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.cancel {
  font-size: 14px;
  color: #212121;
  cursor: pointer;
}
.publish {
  min-width: 52px;
  height: 28px;
  border: none;
  border-radius: 14px;
  background: #313131;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
}
.publish:disabled {
  opacity: 0.5;
}

.body {
  padding: 0 16px;
}

/* 输入框：y44 343x78 #F4F6F8，field 内边距4 文本7px #606266 */
.input-box {
  height: 78px;
  background: #f4f6f8;
}
.content-input {
  display: block;
  width: 100%;
  height: 70px;
  border: none;
  outline: none;
  background: transparent;
  padding: 4px;
  box-sizing: border-box;
  font-size: 12px;
  color: #606266;
  resize: none;
}
.content-input::placeholder {
  color: #c0c4cc;
  font-size: 8px;
}

/* 图片上传：容器 y122，盒子 mt16 100x100 #F4F6F8 */
.upload-row {
  display: flex;
  gap: 8px;
}
.upload-box {
  margin-top: 16px;
  width: 100px;
  height: 100px;
  background: #f4f6f8;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.up-ic {
  width: 32px;
  height: 32px;
}
.upload-text {
  margin-top: 4px;
  font-size: 10px;
  color: #bababa;
}
.upload-item {
  position: relative;
  margin-top: 16px;
  width: 100px;
  height: 100px;
  background: #f4f6f8;
}
.upload-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.upload-del {
  position: absolute;
  top: -6px;
  right: -6px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  font-size: 13px;
  line-height: 18px;
  text-align: center;
  cursor: pointer;
}

/* 匿名发表：y238 pt32 行高53 */
.anon-row {
  padding-top: 32px;
  display: flex;
  align-items: center;
  box-sizing: content-box;
}
.anon-label {
  font-size: 14px;
  color: #7a7a7c;
  margin-right: 12px;
}
.switch {
  position: relative;
  width: 34px;
  height: 18px;
  border: none;
  border-radius: 9px;
  background: rgb(230, 230, 230);
  cursor: pointer;
  transition: background 0.2s;
  padding: 0;
}
.switch.on {
  background: #333;
}
.switch-dot {
  position: absolute;
  top: 1px;
  left: 1px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  transition: left 0.2s;
}
.switch.on .switch-dot {
  left: 17px;
}
</style>
