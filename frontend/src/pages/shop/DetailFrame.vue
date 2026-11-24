<template>
  <!-- P047/P048/P049 详情页共享骨架：默认（原站无在售商品）1.5s 后退回；?demo=1 预览完整结构 -->
  <div class="shop-page detail-frame">
    <div class="shop-nav is-top">
      <img class="nav-back is-dark" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
    </div>

    <!-- 默认态：原站 info 返回「商品不存在」后 backPage，期间仅加载指示 -->
    <div v-if="!demo && !loaded" class="loading"><span class="spin"></span></div>

    <template v-else-if="demo">
      <!-- 商品详情（实物）：原站为顶部白卡「商品详情」+ 大块留白 -->
      <template v-if="kind === 'goods'">
        <div class="goods-title-card"><span class="title-mark"></span>商品详情</div>
        <div class="goods-blank"></div>
      </template>

      <!-- 藏品 / 盲盒详情：主图 + 黑色信息卡 + STORY + 购买须知 -->
      <template v-else>
        <div class="main-image"><img :src="info.main_image" alt="" /></div>
        <div class="black-card">
          <div class="bc-name">{{ info.goods_name }}</div>
          <div class="bc-tags">
            <span class="bc-tag">发行 <em>{{ info.limited }}</em> 份</span>
            <span class="bc-tag">流通量 <em>{{ info.stock }}</em> 份</span>
          </div>
        </div>
        <div class="story-card">
          <div class="story-sub">STORY</div>
          <div class="story-title"><i></i>藏品故事<i></i></div>
        </div>
        <div v-if="kind === 'box'" class="box-extra">
          <div class="box-tip">您可能抽到以下作品中的 1 个</div>
          <div class="box-line"><span>总量：</span><span>{{ info.limited }}</span><span>概率：</span><span>--</span></div>
          <div class="box-desc">数字藏品描述</div>
        </div>
        <div class="notice">
          <div class="notice-title">购买须知</div>
          <p v-for="(t, i) in notices" :key="i">{{ t }}</p>
        </div>
      </template>

      <div class="shop-footer">
        <span class="amount"><i class="crystal-dot"></i>{{ info.crystal_number }}</span>
        <button class="btn">水晶不足</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import http from '../../api/request'
import { isDemo, DEMO_INFO } from './demo'
import './common.css'

defineProps({ kind: { type: String, required: true } })
const router = useRouter()
const route = useRoute()
const demo = isDemo()
const loaded = ref(false)
const info = DEMO_INFO

const notices = [
  '1.为更好服务平台的各位用户，特此公告数字藏品的二次交易细则。鉴于不同属性的数字艺术的二次交易细则各不相同，请各位用户务必仔细阅读并同意相关规则后进行相应操作，以免造成不必要的损失',
  '2.依照我国法律要求，特定条件下的数字藏品的二次交易不支持任何形式的变相炒作，我们坚决反对数字藏品炒作、场外交易、欺诈，或以任何其他非法、侵权方式使用的违规行为，请大家注意甄别上述涉嫌网络欺诈的行为及其相关风险。',
  '3.数字藏品为虚拟数字商品，仅限18周岁以上，60周岁以下且实名认证的中国大陆用户购买。3.本商品一经售出不支持退货。'
]

if (!demo) loadInfo()

async function loadInfo() {
  try {
    const params = {
      goods_id: route.query.id || route.query.goods_id || '',
      id: route.query.collection_id || route.query.blind_box_id || '',
      batch_id: route.query.batch_id || ''
    }
    const res = await http.post('/api/crystal_goods/info', params)
    if (res.code !== 1 || !res.data) {
      // 与原站 getDetails catch 一致：1.5s 后返回
      setTimeout(() => goBack(), 1500)
    } else {
      loaded.value = true
    }
  } catch (e) {
    setTimeout(() => goBack(), 1500)
  }
}

function goBack() {
  if (window.history.length > 1) router.back()
  else router.push('/')
}
</script>

<style scoped>
.detail-frame { background: #F7F9FC; padding-bottom: 90px; }
.shop-nav.is-top { position: absolute; top: 0; left: 0; width: 100%; }
.loading { display: flex; align-items: center; justify-content: center; height: 100vh; }
.spin { width: 24px; height: 24px; border: 2px solid #DDDDDD; border-top-color: #999999; border-radius: 50%; animation: r .8s linear infinite; }
@keyframes r { to { transform: rotate(360deg) } }

.goods-title-card {
  margin: 16px 16px 0; padding: 14px 16px; height: 60px;
  background: #FFFFFF; border-radius: 12px;
  font-size: 16px; font-weight: 700; color: #212121;
  display: flex; align-items: center; gap: 6px;
}
.title-mark { width: 12px; height: 12px; background: #D93437; border-radius: 3px; display: inline-block; }
.goods-blank { min-height: 500px; }

.main-image {
  width: 288px; height: 344px; margin: 60px auto 0;
  border: 3px solid #7A7A7A; border-radius: 2px;
  display: flex; align-items: center; justify-content: center; background: #FFFFFF;
}
.main-image img { width: 240px; height: 240px; object-fit: contain; }
.black-card {
  margin: 30px 0 0; padding: 24px 0 22px;
  background: #2B2B2B; color: #FFFFFF; text-align: center;
  border-radius: 50% 50% 0 0 / 20px 20px 0 0;
}
.bc-name { font-size: 18px; font-weight: 600; }
.bc-tags { display: flex; justify-content: center; gap: 12px; margin-top: 12px; }
.bc-tag { padding: 3px 8px; border-radius: 4px; background: #F3E4C8; color: #2B2B2B; font-size: 12px; }
.bc-tag em { font-style: normal; }
.story-card { margin: 20px 16px 0; padding: 24px 0; background: #2B2B2B; text-align: center; border-radius: 10px; }
.story-sub { font-size: 10px; color: #9A9A9A; letter-spacing: 2px; }
.story-title { margin-top: 10px; font-size: 18px; color: #FFFFFF; display: flex; align-items: center; justify-content: center; gap: 12px; }
.story-title i { width: 40px; height: 1px; background: #9A9A9A; display: inline-block; }
.box-extra { margin: 16px; padding: 16px; background: #FFFFFF; border-radius: 12px; }
.box-tip { font-size: 14px; color: #212121; }
.box-line { display: flex; gap: 8px; margin-top: 10px; font-size: 13px; color: #787F8D; }
.box-desc { margin-top: 10px; font-size: 13px; color: #787F8D; }
.notice { margin: 20px 16px 0; padding: 16px; background: #FFFFFF; border-radius: 12px; }
.notice-title { font-size: 15px; font-weight: 600; color: #212121; margin-bottom: 10px; }
.notice p { font-size: 12px; line-height: 20px; color: #787F8D; margin: 0 0 8px; }
.amount { display: flex; align-items: center; gap: 6px; }
.crystal-dot { width: 14px; height: 14px; background: #F5C842; border-radius: 50% 50% 40% 40%; display: inline-block; }
</style>
