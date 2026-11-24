<template>
  <!-- P068 藏品搜索：圆形输入框 + likeCollection 联想列表 + 空态 -->
  <div class="mc-page se-page">
    <div class="mc-nav se-nav">
      <img class="nav-back" src="../../assets/images/back_back_white.png" alt="返回" @click="goBack" />
    </div>

    <div class="se-bar">
      <div class="se-input">
        <svg class="se-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="8.6" cy="8.6" r="5.6" stroke="#9AA0AB" stroke-width="1.6" />
          <line x1="12.8" y1="12.8" x2="17.4" y2="17.4" stroke="#9AA0AB" stroke-width="1.6" stroke-linecap="round" />
        </svg>
        <input v-model="keyword" class="se-field" type="text" placeholder="搜索内容" @input="onInput" />
        <span v-if="keyword" class="se-clear" @click="clear">
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="1.5" y1="1.5" x2="8.5" y2="8.5" stroke="#C6C7CB" stroke-width="1.4" stroke-linecap="round" />
            <line x1="8.5" y1="1.5" x2="1.5" y2="8.5" stroke="#C6C7CB" stroke-width="1.4" stroke-linecap="round" />
          </svg>
        </span>
      </div>
    </div>

    <div v-if="searched" class="se-list">
      <div v-for="it in suggests" :key="it.collection_id" class="se-item" @click="pick(it)">{{ it.name }}</div>
      <div class="mc-empty se-empty">
        <img src="../../assets/images/empty-data.png" alt="" />
        <span>目前没有任何数据</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_SEARCH } from './demo'
import './common.css'

const router = useRouter()
const keyword = ref('')
const suggests = ref([])
const searched = ref(false)
let timer = null

function goBack() { router.back() }

function onInput() {
  clearTimeout(timer)
  const kw = keyword.value.trim()
  if (!kw) { searched.value = false; suggests.value = []; return }
  timer = setTimeout(() => load(kw), 300)
}

async function load(kw) {
  searched.value = true
  if (isDemo()) {
    suggests.value = DEMO_SEARCH.filter((x) => x.name.includes(kw))
    return
  }
  try {
    const res = await http.post('/api/user_collection/user_collection/likeCollection', { keyword: kw })
    suggests.value = res.data || []
  } catch (e) { suggests.value = [] }
}

function pick(it) {
  // 原站实测：点击联想词 URL 不变，仅把名称回填输入框
  keyword.value = it.name
  load(it.name)
}
function clear() { keyword.value = ''; searched.value = false; suggests.value = [] }
</script>

<style scoped>
.se-page { background: #F7F9FC; }
.se-nav { background: #F7F9FC; }
.se-nav .nav-back { top: 15px; left: 6px; width: 8px; height: 14px; }

/* 圆形搜索框 343×36 @(16,64) br50 */
.se-bar { padding: 20px 16px 0; }
.se-input {
  position: relative; width: 100%; height: 36px; border-radius: 50px;
  background: #FFFFFF; display: flex; align-items: center;
}
.se-icon { position: absolute; left: 12px; top: 8px; }
.se-field {
  width: 100%; height: 100%; border: none; outline: none; background: transparent;
  padding: 0 34px 0 38px; font-size: 14px; color: #212121;
}
.se-field::placeholder { color: #B8BBC2; font-size: 14px; }
.se-clear { position: absolute; right: 12px; top: 13px; width: 10px; height: 10px; cursor: pointer; display: flex; }

/* 联想列表：每行 50px，14px #4A4A4A，x=16 起 */
.se-list { position: relative; padding-top: 16px; min-height: 500px; }
.se-item {
  height: 50px; display: flex; align-items: center; padding: 0 16px;
  font-size: 14px; color: #4A4A4A; cursor: pointer;
}
.se-empty { position: absolute; top: 296px; left: 0; }
</style>
