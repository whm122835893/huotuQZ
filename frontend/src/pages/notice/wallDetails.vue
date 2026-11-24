<template>
  <!-- P042 辟谣详情(wallDetails)：导航「辟谣详情」+ 卡片(头像32/昵称14/时间8/内容14/九图格) + 评论区 -->
  <div class="wall-page">
    <!-- 原站行为：rumor 不存在时整页仅渲染居中加载态（无导航栏） -->
    <template v-if="info.id">
      <div class="nav">
        <img class="back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
        <span class="nav-title">辟谣详情</span>
      </div>

      <div class="body">
      <!-- 谣言卡片 -->
      <div class="card">
        <div class="card-head">
          <img class="avatar" :src="avatarUrl" alt="" />
          <div class="head-info">
            <span class="nick">{{ info.profile.nickname }}</span>
            <span class="ctime">{{ info.create_time }}</span>
          </div>
        </div>
        <div class="content">{{ info.content }}</div>

        <!-- 图片：1张343x257 / 2张171 / 3张115 -->
        <div v-if="images.length" class="imgs" :class="'imgs-' + images.length">
          <img
            v-for="(img, i) in images"
            :key="'im-' + i"
            class="img-item"
            :src="img"
            alt=""
            @click="preview(img)"
          />
        </div>
      </div>

      <!-- 评论 -->
      <div class="comments">
        <div v-for="c in commentList" :key="c.id" class="comment-item">
          <img class="c-avatar" :src="safeAvatar(c.profile.avatar)" alt="" />
          <div class="c-main">
            <span class="c-nick">{{ c.profile.nickname }}</span>
            <span class="c-content">{{ c.content }}</span>
            <span class="c-time">{{ c.create_time }}</span>
          </div>
        </div>
        <div v-if="!commentList.length" class="c-empty">暂无评论</div>
      </div>
    </div>
    </template>

    <!-- 加载中（与原站空数据态一致：整页仅居中加载指示） -->
    <div v-else class="loading-wrap">
      <span class="loading-spin"></span>
      <span class="loading-text">加载中...</span>
    </div>

    <!-- 底部评论输入 -->
    <div v-if="info.id" class="comment-bar">
      <input v-model="commentText" class="cb-input" placeholder="请输入评论内容" @keyup.enter="sendComment" />
      <button class="cb-btn" @click="sendComment">回复</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import http from '../../api/request'

const defaultAvatar = new URL('../../assets/images/mine-default-avatar.png', import.meta.url).href

const info = ref({ profile: {} })
const commentList = ref([])
const commentText = ref('')

const images = computed(() => {
  const s = info.value.images || ''
  return s ? s.split(',').filter(Boolean) : []
})
function safeAvatar(a) {
  return a && /^https?:\/\//.test(a) ? a : defaultAvatar
}
const avatarUrl = computed(() => safeAvatar(info.value.profile?.avatar))

function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = '#/pages/notice/notice'
}
function preview(src) {
  window.open(src, '_blank')
}

async function load(id) {
  const res = await http.post('/api/discover/rumor_wall/info', { id })
  info.value = res.data || {}
  if (info.value && info.value.id) loadComments(info.value.id)
}

async function loadComments(rumorId) {
  const res = await http.post('/api/discover/rumor_wall/getcomment', { rumor_id: rumorId, page: 1, list_rows: 20 })
  const d = res.data || {}
  commentList.value = d.data || []
}

async function sendComment() {
  const text = commentText.value.trim()
  if (!text) return
  await http.post('/api/discover/rumor_wall/comment', { rumor_id: info.value.id, content: text })
  commentText.value = ''
  loadComments(info.value.id)
}

{
  const q = new URLSearchParams(window.location.hash.split('?')[1] || '')
  load(q.get('id') || 0)
}
</script>

<style scoped>
.wall-page {
  min-height: 100vh;
  background: #f7f9fc;
  padding-bottom: 70px;
}

.nav {
  position: sticky;
  top: 0;
  z-index: 5;
  height: 44px;
  background: #f7f9fc;
  display: flex;
  align-items: center;
  justify-content: center;
}
.back {
  position: absolute;
  left: 19px;
  top: 50%;
  transform: translateY(-50%);
  width: 7px;
  height: 13px;
  cursor: pointer;
}
.nav-title {
  font-size: 17px;
  line-height: 23px;
  color: #000;
}

.body {
  padding: 0 16px;
}

.card {
  background: #fff;
  border-radius: 16px;
  padding: 12px 16px;
}
.card-head {
  display: flex;
  align-items: center;
}
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}
.head-info {
  margin-left: 8px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.nick {
  font-size: 14px;
  color: #212121;
}
.ctime {
  font-size: 8px;
  color: #9aa0ab;
}
.content {
  margin-top: 12px;
  font-size: 14px;
  color: #999;
  line-height: 22px;
  word-break: break-all;
}

.imgs {
  margin-top: 8px;
  display: flex;
}
.imgs-1 .img-item {
  width: 343px;
  height: 257px;
  object-fit: cover;
  cursor: pointer;
}
.imgs-2 .img-item {
  width: 171px;
  height: 171px;
  object-fit: cover;
  cursor: pointer;
}
.imgs-3 .img-item {
  width: 115px;
  height: 115px;
  object-fit: cover;
  cursor: pointer;
}

/* 评论 */
.comments {
  margin-top: 12px;
  background: #fff;
  border-radius: 16px;
  padding: 12px 16px;
}
.comment-item {
  display: flex;
  padding: 8px 0;
}
.c-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.c-main {
  margin-left: 8px;
  display: flex;
  flex-direction: column;
}
.c-nick {
  font-size: 12px;
  color: #9aa0ab;
}
.c-content {
  margin-top: 2px;
  font-size: 14px;
  color: #212121;
  line-height: 20px;
}
.c-time {
  margin-top: 2px;
  font-size: 10px;
  color: #c2c5cc;
}
.c-empty {
  text-align: center;
  font-size: 12px;
  color: #9aa0ab;
  padding: 16px 0;
}

/* 加载态（原站空数据时停留的样式：spinner+文字居中 y~245） */
.loading-wrap {
  padding-top: 232px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.loading-spin {
  width: 16px;
  height: 16px;
  border: 2px solid #ddd;
  border-top-color: #999;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.loading-text {
  margin-top: 8px;
  font-size: 12px;
  color: #9aa0ab;
}

/* 底部评论输入 */
.comment-bar {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: 56px;
  background: #fff;
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 10px;
}
.cb-input {
  flex: 1;
  height: 34px;
  background: #f5f6f8;
  border: none;
  outline: none;
  border-radius: 17px;
  padding: 0 14px;
  font-size: 13px;
  color: #212121;
}
.cb-btn {
  height: 30px;
  padding: 0 16px;
  white-space: nowrap;
  flex-shrink: 0;
  border: none;
  border-radius: 15px;
  background: #212121;
  color: #fff;
  font-size: 12px;
  cursor: pointer;
}
</style>
