<template>
  <!-- P072 盲盒详情：复用 detail/ids/<id>，原站无 id 白屏；比藏品详情多「流通」徽标与作品列表区块 -->
  <div v-if="loaded && !failed" class="bb-page bbd-page">
    <div class="bbd-hero">
      <div class="bbd-blur" :style="{ backgroundImage: `url(${heroImg})` }"></div>
      <div class="bbd-shade"></div>
      <div class="bbd-nav">
        <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
        <span class="bbd-extra" @click="toRegiftRecord">转赠记录</span>
      </div>
      <div class="bbd-title"><span>{{ product.name }}</span></div>
      <img class="bbd-main bb-ph" :src="heroImg" alt="" />
      <div class="bbd-badges">
        <div class="bbd-badge">
          <span class="bd-label bd-issue"><i>发行</i></span>
          <span class="bd-num bd-issue-n"><i>{{ formatNum(product.total_num) }}</i></span>
        </div>
        <div class="bbd-badge">
          <span class="bd-label bd-flux"><i>流通</i></span>
          <span class="bd-num bd-flux-n"><i>{{ formatNum(product.flux) }}</i></span>
        </div>
      </div>
    </div>

    <div class="bbd-body">
      <div class="bbd-card bbd-holder">
        <span>藏品持有者</span><span>{{ holder }}</span>
      </div>
      <div class="bbd-card bbd-creator">
        <div class="bd-row"><span>创作者</span><span>{{ creator }}</span></div>
        <div class="bd-row"><span>认证时间</span><span>--</span></div>
      </div>

      <!-- 盲盒作品列表：原站 product.collectionlist 有值才渲染，快照为空 -->
      <div v-if="collectionList.length" class="bbd-card bbd-works">
        <div class="bd-works-title">数字藏品描述</div>
        <div class="bd-works-sub">您可能抽到以下作品中的 1 个</div>
        <div v-for="w in collectionList" :key="w.id" class="bd-work">
          <img class="bd-work-img bb-ph" :src="localImg(w.listimg || w.main_image)" alt="" />
          <div class="bd-work-info">
            <div class="bd-work-name">{{ w.name }}</div>
            <div class="bd-work-meta">
              <span>概率：<i>{{ w.probability }}%</i></span>
              <span>总量：<i>{{ w.show_total_num || w.total_num || 0 }}份</i></span>
            </div>
          </div>
          <img v-if="w.rarity_icon" class="bd-work-rarity" :src="localImg(w.rarity_icon)" alt="" />
        </div>
      </div>

      <div class="bbd-card bbd-notice">
        <div class="bd-notice-title">购买须知</div>
        <div class="bd-notice" v-html="noticeHtml"></div>
      </div>
    </div>

    <div class="bbd-footer"><div class="bbd-btn" @click="onRegift"><span>立即转赠</span></div></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, localImg } from './demo'
import './common.css'

const router = useRouter()
const iid = Number(routeQuery().get('id') || 0)
const loaded = ref(false)
const failed = ref(false)
const product = ref({ name: '', main_image: '', total_num: 0, flux: 0 })
const collectionList = ref([])
const holder = ref('')
const creator = ref('')

const heroImg = computed(() => localImg(product.value.main_image || product.value.listimg))
const formatNum = (n) => Number(n || 0).toLocaleString('en-US')

const noticeHtml = `
<p>为更好服务平台的各位用户，特此公告数字藏品的二次交易细则。鉴于不同属性的数字艺术的二次交易细则各不相同，请各位用户务必仔细阅读并同意相关规则后进行相应操作，以免造成不必要的损失。</p>
<p>1.依照我国法律要求，特定条件下的数字藏品的二次交易不支持任何形式的变相炒作，我们坚决反对数字藏品炒作、场外交易、欺诈，或以任何其他非法、侵权方式使用的违规行为，请大家注意甄别上述涉嫌网络欺诈的行为及其相关风险。</p>
<p>2.数字藏品为虚拟数字商品，仅限18周岁以上，60周岁以下且实名认证的中国大陆用户购买。</p>
<p>3.本商品一经售出不支持退货。</p>`

function goBack() { router.back() }
function toRegiftRecord() { router.push('/pages/regift/record') }
function onRegift() { /* 原站点击后 URL 不变，保留占位 */ }

async function load() {
  if (!iid && !isDemo()) { loaded.value = true; failed.value = true; return }
  try {
    const res = await http.post(`/api/user_collection/user_collection/detail/ids/${iid}`, {})
    if (!res || res.code !== 1 || !res.data) { failed.value = true; loaded.value = true; return }
    const d = res.data
    const p = d.product || {}
    product.value = {
      name: p.name || '', main_image: p.main_image || p.listimg || '',
      total_num: p.total_num || 0, flux: p.flux || 0,
    }
    collectionList.value = Array.isArray(p.collectionlist) ? p.collectionlist : []
    holder.value = d.user?.nickname || ''
    creator.value = p.publisher?.name || '--'
  } catch (e) { failed.value = true }
  loaded.value = true
}
load()
</script>

<style scoped>
.bbd-page { background: var(--bb-bg); padding-bottom: 70px; }
.bbd-hero { position: relative; height: 436px; overflow: hidden; }
.bbd-blur {
  position: absolute; left: -12%; top: -12%; width: 124%; height: 124%;
  background-size: cover; background-position: center; filter: blur(28px);
}
.bbd-shade { position: absolute; inset: 0; background: rgba(255, 255, 255, 0.04); }
.bbd-nav { position: relative; z-index: 2; height: 44px; }
.bbd-nav .nav-back {
  position: absolute; top: 15.5px; left: 19px;
  width: 7.3px; height: 13px; cursor: pointer;
}
.bbd-extra {
  position: absolute; right: 6px; top: 16px;
  font-size: 12px; color: var(--bb-card); cursor: pointer;
}
.bbd-title {
  position: relative; z-index: 2; text-align: center;
  margin-top: 43px; font-size: 16px; font-weight: 500; color: var(--bb-ink); line-height: 22px;
}
.bbd-main {
  position: relative; z-index: 2; display: block;
  width: 220px; height: 220px; margin: 11px auto 0;
  border-radius: 20px; object-fit: cover;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}
.bbd-badges {
  position: relative; z-index: 2; margin-top: 14px;
  display: flex; align-items: center; justify-content: center; gap: 12px;
}
.bbd-badge { display: flex; height: 24px; border-radius: 4px; overflow: hidden; }
.bd-label { padding: 0 9px; font-size: 11px; line-height: 24px; text-align: center; }
.bd-num { padding: 0 9px; font-size: 14px; line-height: 24px; font-family: 'Bebas Neue', 'Arial Narrow', sans-serif; }
.bd-label i, .bd-num i { font-style: normal; }
.bd-issue { background: var(--bb-red2); color: var(--bb-card); }
.bd-issue-n { background: #FFEBEB; color: var(--bb-red); }
.bd-flux { background: var(--bb-ink); color: var(--bb-card); }
.bd-flux-n { background: #CDCCD5; color: var(--bb-ink); }

.bbd-body { position: relative; margin-top: -30px; padding: 0 16px 24px; }
.bbd-card { background: var(--bb-card); border-radius: 14px; margin-bottom: 12px; overflow: hidden; }
.bbd-holder {
  height: 62px; padding: 0 24px;
  display: flex; align-items: center; justify-content: space-between;
  font-size: 14px; color: #191919;
}
.bbd-holder span:first-child { font-weight: 500; }
.bbd-creator { height: 124px; display: flex; flex-direction: column; }
.bd-row { flex: 1; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; font-size: 14px; color: #191919; }
.bd-row span:first-child { font-weight: 500; }
.bd-row + .bd-row { border-top: 1px solid #F0F0F0; }

.bbd-works { padding: 20px 24px; }
.bd-works-title { font-size: 16px; font-weight: 700; color: #191919; }
.bd-works-sub { font-size: 12px; color: #999999; margin-top: 8px; }
.bd-work { display: flex; align-items: center; gap: 8px; padding: 6px; margin-top: 8px; background: #F7F7F7; border-radius: 8px; }
.bd-work-img { width: 59px; height: 59px; border-radius: 4px; object-fit: cover; }
.bd-work-info { flex: 1; min-width: 0; }
.bd-work-name { font-size: 13px; font-weight: 500; color: #191919; }
.bd-work-meta { display: flex; gap: 16px; margin-top: 8px; font-size: 12px; color: #999999; }
.bd-work-meta i { font-style: normal; color: #191919; }
.bd-work-rarity { width: 55px; height: 55px; object-fit: contain; }

.bbd-notice { padding: 20px 24px; }
.bd-notice-title { font-size: 16px; font-weight: 700; color: #191919; margin-bottom: 10px; }
.bd-notice { background: #F7F7F7; border-radius: 10px; padding: 8px; font-size: 12px; line-height: 18px; color: #666666; }
.bd-notice :deep(p) { margin: 0 0 6px; }
.bd-notice :deep(p:last-child) { margin-bottom: 0; }

.bbd-footer {
  position: fixed; left: 0; right: 0; bottom: 0; z-index: 10;
  max-width: 750px; margin: 0 auto; height: 66px; padding: 10px 16px; background: var(--bb-card);
}
.bbd-btn {
  width: 100%; height: 46px; border-radius: 14px;
  background: var(--bb-dark); color: var(--bb-card);
  font-size: 15px; font-weight: 600;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
}
</style>
