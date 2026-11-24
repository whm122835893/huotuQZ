<template>
  <!-- 我的求购/我的委托列表共享骨架：导航 + 圆角Tab条 + 空态/列表 -->
  <div class="ord-page tl-page">
    <OrderNav :title="navTitle" />
    <div class="tl-tabs">
      <div v-for="(t, i) in tabs" :key="i" class="tl-tab" :class="{ on: idx === i }" @click="idx = i">
        {{ t }}<i v-if="idx === i"></i>
      </div>
    </div>
    <div v-if="list.length" class="tl-list">
      <div v-for="o in list" :key="o.id" class="ord-panel tl-card">
        <img class="tl-img" :src="o.local_image || o.goods_cover_image" alt="" />
        <div class="tl-info">
          <div class="tl-name">{{ o.goods_name }}</div>
          <div class="tl-sub">{{ label }}数量: {{ o.num }}</div>
          <div class="tl-sub">{{ o.createtime_text }}</div>
        </div>
        <div class="tl-price">¥{{ o.price }}</div>
      </div>
      <div class="ord-nomore">已经到底了~</div>
    </div>
    <div v-else class="ord-empty tl-empty">
      <img src="../../assets/images/empty-data.png" alt="" />
      <span>目前没有任何数据</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import OrderNav from './OrderNav.vue'
import './common.css'
const props = defineProps({
  navTitle: { type: String, default: '' },
  tabs: { type: Array, default: () => ['全部', '求购中', '已成交', '已取消'] },
  label: { type: String, default: '求购' },
  list: { type: Array, default: () => [] },
})
const idx = ref(0)
</script>

<style scoped>
.tl-page { background: #F7F9FC; }
.tl-tabs {
  display: flex; margin: 0 17px; height: 40px; border: 1px solid #E5E5E5;
  border-radius: 10px; background: #FFFFFF; overflow: hidden;
}
.tl-tab { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; font-size: 11px; color: #999999; }
.tl-tab.on { font-weight: 700; color: #212121; }
.tl-tab i { position: absolute; bottom: 6px; width: 16px; height: 3px; border-radius: 2px; background: #212121; }
.tl-empty { position: absolute; top: 166px; left: 0; }
.tl-list { padding: 14px 17px; }
.tl-card { display: flex; align-items: center; padding: 12px; margin-bottom: 10px; }
.tl-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; background: #F5F5F5; }
.tl-info { flex: 1; min-width: 0; padding-left: 12px; }
.tl-name { font-size: 14px; color: #212121; }
.tl-sub { font-size: 11px; color: #9AA0AB; margin-top: 4px; }
.tl-price { font-size: 14px; color: #D93437; }
</style>
