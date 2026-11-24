<template>
  <!-- P070 我的寄售：原站该账号 total=0 恒为空态；?demo=1 展示列表项结构 -->
  <div class="cs-page mc-page">
    <div class="cs-nav">
      <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
      <i class="nav-title">我的寄售</i>
    </div>

    <!-- 空态 -->
    <div v-if="!list.length" class="cs-empty mc-empty">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>

    <!-- 有数据态（原站 myConsignmentsItem 组件结构，demo 结构预览） -->
    <div v-else class="mc-list">
      <div v-for="it in list" :key="it.id" class="mc-item">
        <div class="mc-item-top">
          <img class="mc-cover" :src="localImg(it.goods_cover_image)" alt="" />
          <div class="mc-item-info">
            <i class="mc-item-name">{{ it.goods_name }}</i>
            <div class="mc-item-sub">
              <span class="mc-tag">{{ it.status === 1 ? '寄售中' : '已售出' }}</span>
              <span v-if="it.goods_type === 2" class="mc-num">#{{ it.user_collection.collection_code }}/{{ it.user_collection.total_num }}</span>
            </div>
          </div>
        </div>
        <div class="mc-item-bottom">
          <span class="mc-count">共 1 件寄售</span>
          <span class="mc-price">¥{{ it.price }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, localImg, DEMO_CONSIGNMENT } from './demo'
import './common.css'

const router = useRouter()
const list = ref([])
function goBack() { router.back() }

async function load() {
  if (isDemo()) { list.value = DEMO_CONSIGNMENT; return }
  try {
    const res = await http.post('/api/order/order/myConsignment', { status: '', goods_type: 2, page: 1 })
    if (res.code === 1 && res.data) list.value = res.data.data || []
  } catch (e) { list.value = [] }
}
load()
</script>

<style scoped>
.mc-page { background: #FFFFFF; }
.mc-empty { padding-top: 100px; }
.mc-list { padding: 12px 20px; }
.mc-item { background: #FFFFFF; border-radius: 12px; padding: 12px; margin-bottom: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
.mc-item-top { display: flex; align-items: center; }
.mc-cover { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
.mc-item-info { margin-left: 12px; }
.mc-item-name { font-size: 14px; font-weight: 500; color: var(--cs-ink); font-style: normal; }
.mc-item-sub { margin-top: 6px; display: flex; align-items: center; gap: 8px; }
.mc-tag { font-size: 11px; color: var(--cs-red); }
.mc-num { font-size: 11px; color: #999999; }
.mc-item-bottom { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; }
.mc-count { font-size: 11px; color: #999999; }
.mc-price { font-size: 15px; font-weight: 600; color: var(--cs-red); }
</style>
