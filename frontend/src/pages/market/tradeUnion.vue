<template>
  <!-- P036 GM 工会（原站 #/pages/market/tradeUnion，无参数直访真实态）
       侦查实测：深棕主题；整页背景 rgb(61,40,28)，顶部为 d-bg 宝箱背景图；
       navbar「< GM工会」；lock 锁图标(55x55 @160,94)；「您的公会未解锁」+「赶快购买公会藏品解锁您的公会吧~」；
       Tab「个人权益(高亮金线) / 我的工会 / 申请入会」；权益卡 rgb(81,55,36) 含
       「手续费减免 + 说明」「公会成员人数 + 说明」两行，配钱包/喇叭图标。 -->
  <div class="b4-tu-page">
    <img class="b4-tu-bg" :src="bg" alt="" />
    <img class="b4-tu-back" :src="back" alt="返回" @click="goBack" />
    <div class="b4-tu-title">GM工会</div>

    <img class="b4-tu-lock" :src="lock" alt="" />
    <div class="b4-tu-lock-tip">您的公会未解锁</div>
    <div class="b4-tu-lock-sub">赶快购买公会藏品解锁您的公会吧~</div>

    <div class="b4-tu-tabs">
      <span class="t on" style="left:30px">个人权益</span>
      <span class="t" style="left:155px">我的工会</span>
      <span class="t" style="left:280px">申请入会</span>
    </div>
    <div class="b4-tu-underline"></div>

    <div class="b4-tu-card">
      <div class="b4-tu-item">
        <div class="b4-tu-ic wallet"><img :src="wallet" alt="" /></div>
        <div class="b4-tu-info">
          <div class="b4-tu-item-title">手续费减免</div>
          <div class="b4-tu-item-desc">持有人可以享受消费返利1.88%，被邀请人
可以享受消费返利1%手续费;持有人还可以
额外享受所有被邀请人消费返利的0.88%。</div>
        </div>
      </div>
      <div class="b4-tu-divider"></div>
      <div class="b4-tu-item">
        <div class="b4-tu-ic msg"><img :src="dmsg" alt="" /></div>
        <div class="b4-tu-info">
          <div class="b4-tu-item-title">公会成员人数</div>
          <div class="b4-tu-item-desc">每位公会会长最多可绑定邀请30人。</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const back = new URL('../../assets/images/b3-back-btn.png', import.meta.url).href
const bg = new URL('../../assets/images/b4-gh-dbg.png', import.meta.url).href
const lock = new URL('../../assets/images/b4-gh-lock.png', import.meta.url).href
const dmsg = new URL('../../assets/images/b4-gh-dmsg.png', import.meta.url).href
const wallet = new URL('../../assets/images/b4-gh-bg_29x26.png', import.meta.url).href
const router = useRouter()
function goBack() { router.back() }
</script>

<style scoped>
.b4-tu-page {
  --b4-base: rgb(61, 40, 28);
  --b4-card: rgb(81, 55, 36);
  --b4-gold: rgb(227, 168, 106);
  --b4-cream: rgb(202, 182, 161);
  --b4-sub: rgb(252, 216, 177);
  position: relative; width: 375px; min-height: 812px; margin: 0 auto;
  background: var(--b4-base); overflow: hidden; font-family: 'Alibaba PuHuiTi', -apple-system, sans-serif;
}
.b4-tu-bg { position: absolute; left: 0; top: 0; width: 375px; height: 328px; object-fit: cover; opacity: 0.55; }
.b4-tu-back { position: absolute; left: 19px; top: 16px; width: 7px; height: 13px; cursor: pointer; z-index: 3; }
.b4-tu-title {
  position: absolute; left: 0; right: 0; top: 11px; height: 23px; text-align: center;
  font-size: 17px; font-weight: 700; line-height: 23px; color: #000; z-index: 2;
}
.b4-tu-lock { position: absolute; left: 160px; top: 94px; width: 55px; height: 55px; z-index: 2; }
.b4-tu-lock-tip { position: absolute; left: 0; right: 0; top: 176px; text-align: center; font-size: 16px; font-weight: 700; line-height: 22px; color: #000; z-index: 2; }
.b4-tu-lock-sub { position: absolute; left: 0; right: 0; top: 214px; text-align: center; font-size: 12px; font-weight: 500; line-height: 17px; color: var(--b4-sub); z-index: 2; }
.b4-tu-tabs { position: absolute; left: 0; right: 0; top: 289px; height: 24px; z-index: 2; }
.b4-tu-tabs .t { position: absolute; top: 0; font-size: 16px; font-weight: 500; line-height: 22px; color: var(--b4-cream); }
.b4-tu-tabs .t.on { font-weight: 700; color: var(--b4-gold); }
.b4-tu-underline { position: absolute; left: 53px; top: 318px; width: 20px; height: 3px; border-radius: 50px; background: var(--b4-gold); z-index: 2; }
.b4-tu-card {
  position: absolute; left: 16px; right: 16px; top: 355px; height: 235px; border-radius: 10px;
  background: var(--b4-card); padding: 30px 24px 18px 33px; box-sizing: border-box; z-index: 2;
}
.b4-tu-item { display: flex; gap: 20px; }
.b4-tu-ic { width: 30px; flex-shrink: 0; display: flex; align-items: flex-start; padding-top: 3px; }
.b4-tu-ic.wallet img { width: 29px; height: 26px; object-fit: contain; }
.b4-tu-ic.msg img { width: 30px; height: 24px; object-fit: contain; }
.b4-tu-info { flex: 1; }
.b4-tu-item-title { font-size: 16px; font-weight: 700; color: var(--b4-gold); line-height: 20px; }
.b4-tu-item-desc { margin-top: 10px; font-size: 12px; font-weight: 500; line-height: 20px; white-space: pre-line; color: var(--b4-cream); }
.b4-tu-divider { height: 1px; background: rgba(227, 168, 106, 0.22); margin: 24px 0; }
</style>
