<template>
  <!-- P055 我的求购：原站当前为空数据态；?demo=1 展示列表结构 -->
  <TradeList
    nav-title="我的求购" label="求购"
    :tabs="['全部', '求购中', '已成交', '已取消']" :list="list"
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
  http.post('/api/market/trade/begBuyOrderList', { page: 1 }).then((res) => {
    list.value = res.data?.data || []
  }).catch(() => {})
}
</script>
