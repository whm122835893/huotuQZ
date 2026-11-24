const puppeteer = require('puppeteer-core');
const fs = require('fs');
const R = '/root/.codebuddy/artifact/2f8a7d30-9c21-4f0b-1d63-67daa1424000/recon';
const ORIGIN = 'https://h5.hnyiqin.asia';
const CLONE  = 'http://localhost:3000';
const token = fs.readFileSync('/tmp/newtoken.txt','utf8').trim();

// 批次21 两页 P100-P101。全部为无参数态静态真实态：
// P095/P097/P098 计数 0、P096 永久 loading、P099 404 catch 静态渲染。
// 系统隐藏项 Cancel(uni-actionsheet @y=822) 在对比层 SKIP。
const cases = [
  ['draw',   '/#/pages/activity/draw',   1800],
  ['record', '/#/pages/activity/record', 1800],
];

const COLLECT = () => {
  const out = [];
  const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
  let n;
  while ((n = walker.nextNode())) {
    const t = (n.textContent || '').trim();
    if (!t) continue;
    const range = document.createRange(); range.selectNodeContents(n);
    const r = range.getBoundingClientRect();
    if (r.width <= 0 || r.height <= 0 || r.y < -50 || r.y > 1400) continue;
    const cs = getComputedStyle(n.parentElement);
    out.push({ kind:'T', x:Math.round(r.x), y:Math.round(r.y), w:Math.round(r.width), h:Math.round(r.height),
      fs:cs.fontSize, fw:cs.fontWeight, c:cs.color, text:t.slice(0,110) });
  }
  document.querySelectorAll('*').forEach(el => {
    const r = el.getBoundingClientRect(); const cs = getComputedStyle(el);
    if (r.width > 0 && r.height > 0 && r.y >= -50 && r.y < 1400) {
      out.push({ kind:'E', tag:el.tagName, cls:String(el.className).slice(0,50),
        src: el.tagName === 'IMG' ? (el.currentSrc || el.src || '').split('/').pop().slice(0,60) : '',
        x:Math.round(r.x), y:Math.round(r.y), w:Math.round(r.width), h:Math.round(r.height),
        bg:cs.backgroundColor, br:cs.borderRadius, fs:cs.fontSize, fw:cs.fontWeight, c:cs.color });
    }
  });
  return out;
};

async function loginClone(page) {
  await page.goto(CLONE + '/#/pages/login/login', {waitUntil:'networkidle2', timeout:60000});
  const r = await page.evaluate(async () => {
    const x = await fetch('/api/user/login', {method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({account: process.env.HT_ACCOUNT || '', password: process.env.HT_PASSWORD || ''})});
    return x.json();
  });
  const t = r?.data?.userinfo?.token;
  await page.evaluate(v => {
    localStorage.setItem('ht_token', v);
    localStorage.setItem('ht_userinfo', JSON.stringify({token:v}));
  }, t);
  return t;
}

async function runSite(base, isClone, prefix) {
  const browser = await puppeteer.launch({executablePath:'/usr/bin/chromium', headless:'new', args:['--no-sandbox','--disable-dev-shm-usage']});
  const page = await browser.newPage();
  await page.setViewport({width:375, height:812, isMobile:true, hasTouch:true, deviceScaleFactor:2});
  if (isClone) {
    const t = await loginClone(page);
    console.log(prefix, 'login token=', t ? 'OK' : 'FAIL');
  } else {
    await page.evaluateOnNewDocument(t => { try { localStorage.setItem('token', t); } catch(e){} }, token);
  }
  const errs = [], badimg = [], apierr = [];
  page.on('pageerror', e => errs.push(e.message.slice(0,140)));
  page.on('response', r => {
    if (r.request().resourceType() === 'image' && r.status() >= 400) badimg.push(r.status()+' '+r.url().split('/').pop());
    if ((r.url().includes('/api/') || r.url().includes('/addons/')) && r.status() >= 400)
      apierr.push(r.status()+' '+r.url().split('/').pop());
  });

  for (const [name, hash, wait] of cases) {
    await page.goto(base + hash, {waitUntil:'networkidle2', timeout:60000});
    if (isClone) await page.reload({waitUntil:'networkidle2', timeout:60000});
    await new Promise(x => setTimeout(x, wait));
    await page.screenshot({path:`${R}/${prefix}_${name}.png`});
    const dom = await page.evaluate(COLLECT);
    fs.writeFileSync(`${R}/${prefix}_${name}_dom.json`, JSON.stringify(dom, null, 1));
    const txt = await page.evaluate(() => document.body.innerText.replace(/\s+/g,' ').slice(0,150));
    const nT = dom.filter(x => x.kind === 'T').length;
    console.log((isClone?'CLONE':'ORIG ').padEnd(6), name.padEnd(15), 'T=', nT, '|', txt.slice(0,105));
  }
  console.log(prefix, 'PAGE_ERRORS:', errs.length ? [...new Set(errs)].join(' | ') : 'none');
  console.log(prefix, 'BAD_IMAGES:', badimg.length ? [...new Set(badimg)].slice(0,8).join(' | ') : 'none');
  console.log(prefix, 'API_ERRORS:', apierr.length ? [...new Set(apierr)].slice(0,8).join(' | ') : 'none');
  await browser.close();
}

(async () => {
  const which = process.argv[2] || 'all';
  if (which !== 'clone') await runSite(ORIGIN, false, 'b21_base');
  if (which !== 'orig')  await runSite(CLONE, true, 'b21_clone');
})().catch(e => { console.error(e); process.exit(1); });
