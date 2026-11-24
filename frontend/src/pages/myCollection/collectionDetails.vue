<template>
  <!-- P065 藏品详情：主图模糊铺底 + 发行量 + 持有者/创作者 + 购买须知 + 转赠按钮 -->
  <div class="mc-page cd-page">
    <!-- 顶部主视觉 375×436：藏品主图放大 124% + blur(28px)，原站真实实现 -->
    <div class="cd-hero">
      <div class="cd-blur" :style="{ backgroundImage: `url(${heroImg})` }"></div>
      <div class="cd-hero-shade"></div>

      <div class="mc-nav cd-nav">
        <img class="nav-back" src="../../assets/images/back_btn.png" alt="返回" @click="goBack" />
        <div class="cd-nav-extra" @click="toRegiftRecord">转赠记录</div>
      </div>

      <div class="cd-title"><span>{{ product.name }}</span></div>
      <img class="cd-main" :src="heroImg" alt="" />
      <div class="cd-issue">
        <span class="ci-label"><i>发行</i></span>
        <span class="ci-num"><i>{{ formatNum(product.total_num) }}</i></span>
      </div>
    </div>

    <div class="cd-body">
      <div class="cd-panel cd-holder">
        <span class="cp-key">藏品持有者</span>
        <span class="cp-val">{{ holder }}</span>
      </div>

      <div class="cd-panel cd-creator">
        <div class="cp-row"><span class="cp-key">创作者</span><span class="cp-val">{{ creator }}</span></div>
        <div class="cp-row"><span class="cp-key">认证时间</span><span class="cp-val">--</span></div>
      </div>

      <div class="cd-panel cd-notice-panel">
        <div class="cd-notice-title">购买须知</div>
        <div class="cd-notice" v-html="noticeHtml"></div>
      </div>
    </div>

    <div class="cd-footer">
      <div class="cd-btn" @click="onRegift"><span>立即转赠</span></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import http from '../../api/request'
import { isDemo, routeQuery, DEMO_COLLECTIONS, localImg } from './demo'
import './common.css'

const router = useRouter()
const iid = Number(routeQuery().get('id') || 0)

const product = ref({ name: '', main_image: '', total_num: 0 })
const holder = ref('')
const creator = ref('')

const heroImg = computed(() => localImg(product.value.main_image || product.value.listimg))
const formatNum = (n) => Number(n || 0).toLocaleString('en-US')

// 原站前端写死的购买须知（API 该字段为空白），逐字还原
const noticeHtml = `
<p>为更好服务平台的各位用户，特此公告数字藏品的二次交易细则。鉴于不同属性的数字艺术的二次交易细则各不相同，请各位用户务必仔细阅读并同意相关规则后进行相应操作，以免造成不必要的损失。</p>
<p>1.依照我国法律要求，特定条件下的数字藏品的二次交易不支持任何形式的变相炒作，我们坚决反对数字藏品炒作、场外交易、欺诈，或以任何其他非法、侵权方式使用的违规行为，请大家注意甄别上述涉嫌网络欺诈的行为及其相关风险。</p>
<p>2.数字藏品为虚拟数字商品，仅限18周岁以上，60周岁以下且实名认证的中国大陆用户购买。</p>
<p>3.本商品一经售出不支持退货。</p>`

function goBack() { router.back() }
function toRegiftRecord() { router.push('/pages/regift/record') }
function onRegift() { /* 原站实测：点击后 URL 不变，保留占位 */ }

async function load() {
  if (isDemo()) {
    const c = DEMO_COLLECTIONS[0]
    product.value = { name: c.name, main_image: c.main_image, total_num: c.total_num }
    holder.value = '175****1293'
    creator.value = '火兔'
    return
  }
  try {
    const res = await http.post(`/api/user_collection/user_collection/detail/ids/${iid}`, {})
    const d = res.data || {}
    const p = d.product || {}
    product.value = { name: p.name || '', main_image: p.main_image || p.listimg || '', total_num: p.total_num || 0 }
    holder.value = d.user?.nickname || ''
    creator.value = p.publisher?.name || ''
  } catch (e) {
    product.value = { name: '', main_image: '', total_num: 0 }
  }
}

load()
</script>

<style scoped>
.cd-page { background: #F7F9FC; padding-bottom: 70px; }

/* 主视觉 375×436 */
.cd-hero { position: relative; height: 436px; overflow: hidden; }
.cd-blur {
  position: absolute; left: -12%; top: -12%; width: 124%; height: 124%;
  background-size: cover; background-position: center; filter: blur(28px);
}
.cd-hero-shade { position: absolute; inset: 0; background: rgba(255, 255, 255, 0.04); }
.cd-nav { position: relative; z-index: 2; background: transparent; }
.cd-nav .nav-back { width: 8px; height: 14px; top: 15px; left: 19px; }
.cd-nav-extra {
  position: absolute; right: 6px; top: 16px; font-size: 12px; color: #FFFFFF; cursor: pointer;
  text-align: right;
}

.cd-title {
  position: relative; z-index: 2; text-align: center;
  margin-top: 43px; font-size: 16px; font-weight: 500; color: #212121; line-height: 22px;
}
.cd-main {
  position: relative; z-index: 2; display: block;
  width: 220px; height: 220px; margin: 11px auto 0;
  border-radius: 20px; object-fit: cover; background: #F5F5F5;
}
.cd-issue {
  position: relative; z-index: 2; display: flex; width: 88px; height: 24px; margin: 14px auto 0;
  border-radius: 4px; overflow: hidden;
}
.ci-label { width: 40px; height: 24px; background: #FF4144; color: #FFFFFF; font-size: 11px; text-align: center; line-height: 24px; }
.ci-num { width: 48px; height: 24px; background: #FFEBEB; color: #D93437; font-size: 14px; text-align: left; padding-left: 8px; line-height: 24px; }
.ci-label i, .ci-num i { font-style: normal; }

/* 白色内容区，圆角上翻覆盖主视觉底部 */
.cd-body { position: relative; margin-top: -30px; padding: 0 16px 24px; }
.cd-panel { background: #FFFFFF; border-radius: 12px; margin-bottom: 12px; }
.cd-holder { height: 62px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
.cp-key { font-size: 14px; font-weight: 500; color: #191919; }
.cp-val { font-size: 14px; color: #191919; }
/* 创作者 / 认证时间：同一张 124px 白卡(y=480)，两行各 62 带分割线 */
.cd-creator { height: 124px; display: flex; flex-direction: column; }
.cp-row {
  flex: 1; display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px;
}
.cp-row + .cp-row { border-top: 1px solid #EEEEEE; }

/* 购买须知：外层白卡 y=616，内含标题(636) + 灰框(668×250) */
.cd-notice-panel { padding: 20px 24px 24px; margin-bottom: 0; }
.cd-notice-title { font-size: 16px; font-weight: 700; color: #191919; line-height: 22px; margin: 0 0 10px; }
.cd-notice {
  height: 250px; overflow: hidden;
  background: #F7F7F7; border-radius: 10px; padding: 8px;
  font-size: 12px; line-height: 20px; color: #666666;
}
.cd-notice :deep(p) { margin: 0 0 12px; }
.cd-notice :deep(p:last-child) { margin-bottom: 0; }

/* 底部固定按钮 */
.cd-footer { position: fixed; left: 0; right: 0; bottom: 0; height: 66px; padding: 10px 16px; background: #FFFFFF; }
.cd-btn {
  width: 100%; max-width: 343px; margin: 0 auto; height: 46px; border-radius: 14px;
  background: #313131; color: #FFFFFF; font-size: 15px; font-weight: 600;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
}
</style>
