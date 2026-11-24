<template>
  <!-- P067 我的藏品：藏品/盲盒 胶囊 Tab + 持有卡片列表（原站实测 3 项） -->
  <div class="mc-page coll-page">
    <div class="mc-nav coll-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <div class="seg">
        <div class="seg-on" :style="{ transform: tab === 0 ? 'translateX(0)' : 'translateX(67px)' }"></div>
        <div class="seg-item" :class="{ on: tab === 0 }" @click="switchTab(0)"><span>藏品</span></div>
        <div class="seg-item" :class="{ on: tab === 1 }" @click="switchTab(1)"><span>盲盒</span></div>
      </div>
    </div>

    <div v-if="list.length" class="coll-list">
      <div v-for="(it, i) in list" :key="it.collection_id" class="coll-card" @click="toHoldings(it)">
        <img class="cc-img" :src="imgOf(it.collection)" alt="" />
        <div class="cc-info">
          <div class="cc-name">{{ it.collection.name }}</div>
          <div class="cc-stock">库存：{{ it.num }}份</div>
        </div>
        <img class="mc-arrow-r cc-arrow" src="../../assets/images/icon-right.png" alt="" />
      </div>
    </div>
    <div v-else class="mc-empty coll-empty">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_LIST, localImg } from './demo'
import './common.css'

const router = useRouter()
const tab = ref(0)
const list = ref([])

const imgOf = (c) => localImg(c?.main_image || c?.listimg)

function goBack() { router.back() }

function switchTab(i) {
  tab.value = i
  load()
}

async function load() {
  if (isDemo()) {
    list.value = tab.value === 0 ? DEMO_LIST : []
    return
  }
  try {
    const types = tab.value === 0 ? 2 : 1
    const res = await http.post('/api/user_collection/user_collection/antMycollection', { types, page: 1, collection_id: '' })
    const rows = res.data?.data || []
    list.value = rows.map((r) => ({ ...r, collection: { ...r.collection } }))
  } catch (e) { list.value = [] }
}

function toHoldings(it) {
  router.push(`/pages/myCollection/holdingsDetail?id=${it.collection_id}&types=2`)
}

load()
</script>

<style scoped>
.coll-page { background: #F7F9FC; }
.coll-nav { background: #F7F9FC; }
/* 胶囊 131×32 @(122,6)，选中红块 64×28 r14 */
.seg {
  position: relative; width: 131px; height: 32px; border-radius: 16px;
  background: #ECEEF2; display: flex; align-items: center;
}
.seg-on {
  position: absolute; left: 2px; top: 2px; width: 64px; height: 28px;
  border-radius: 14px; background: #D93437; transition: transform .2s ease;
}
.seg-item {
  position: relative; z-index: 1; flex: 1 1 0; text-align: center;
  font-size: 14px; font-weight: 500; color: #9AA0AB; line-height: 20px; cursor: pointer;
}
.seg-item.on { color: #FFFFFF; }

.coll-list { padding: 24px 20px 0; }
.coll-card {
  position: relative; display: flex; align-items: center;
  background: #FFFFFF; border-radius: 12px; height: 96px;
  padding: 8px 12px 8px 12px; margin-bottom: 20px; cursor: pointer;
}
.cc-img { width: 80px; height: 80px; border-radius: 8px; background: #F5F5F5; object-fit: cover; display: block; flex: none; }
.cc-info { flex: 1; min-width: 0; padding-left: 12px; }
.cc-name { font-size: 15px; font-weight: 500; color: #212121; line-height: 21px; }
.cc-stock { font-size: 12px; color: #9AA0AB; line-height: 17px; margin-top: 4px; }
.cc-arrow { flex: none; }
.coll-empty { position: absolute; top: 260px; left: 0; }
</style>
