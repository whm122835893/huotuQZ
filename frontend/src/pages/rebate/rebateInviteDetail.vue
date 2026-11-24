<template>
  <div class="rb-page">
    <div class="rb-navbar">
      <img class="rb-back" :src="IMG.back" alt="返回" @click="goBack" />
      <div class="rb-nav-title">{{ RD.title }}</div>
    </div>

    <div class="rb-rd-head">
      <div class="rb-rd-cell rb-rd-c1">{{ RD.heads[0] }}</div>
      <div class="rb-rd-cell rb-rd-c2">{{ RD.heads[1] }}</div>
      <div class="rb-rd-cell rb-rd-c3">{{ RD.heads[2] }}</div>
      <div class="rb-rd-cell rb-rd-c4">{{ RD.heads[3] }}</div>
      <div class="rb-rd-cell rb-rd-c5">{{ RD.heads[4] }}</div>
    </div>

    <div class="rb-empty">
      <img class="rb-empty-img" :src="IMG.empty" alt="暂无数据" />
      <span class="rb-empty-txt">{{ RD.empty }}</span>
    </div>
  </div>
</template>

<script setup>
/* P088 被邀请人清单：原站实测 navbar 标题「被邀请人清单」+ 5 列表头
   （昵称/加入时间/首次交易日期/总交易金额/本自然月交易金额）+ 空态。
   bei_yao_list 原站返回 404，列表无数据 → 粉色空箱插画。 */
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { IMG, RD } from './demo'
import './common.css'

const router = useRouter()
function goBack() { router.back() }

/** 原站 onLoad -> bei_yao_list()，本项目等价走本地后端（404 空态），不臆造记录 */
onMounted(async () => {
  try {
    await http.post('/api/user/bei_yao_list', { page: 1 })
  } catch (e) { /* 原站 404，空态如实保持 */ }
})
</script>
