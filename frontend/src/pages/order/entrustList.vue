<template>
  <!-- P058 我的委托：原站当前为空数据态；?demo=1 展示列表结构 -->
  <TradeList
    nav-title="我的委托" label="委托"
    :tabs="['全部', '委托中', '已成交', '已取消']" :list="list"
  />
</template>

<script setup>
import { ref } from 'vue'
import http from '../../api/request'
import TradeList from './TradeList.vue'
import { isDemo, DEMO_BEG_LIST } from './demo'

const list = ref([])
if (isDemo()) list.value = DEMO_BEG_LIST
else {
  http.post('/api/market/Prepayment/myOrderlist', { page: 1 }).then((res) => {
    list.value = res.data?.data || []
  }).catch(() => {})
}
</script>
