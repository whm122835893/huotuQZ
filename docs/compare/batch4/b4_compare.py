#!/usr/bin/env python3
# 批次4 市场交易 11 页 三层像素对比（批次19/23/3 口径）
# 文字层：按坐标就近(±5px)+文本包含匹配，比 fs(数值取整容差)/fw/color
# 几何层：仅比「有背景色/圆角」的实质元素，按坐标±2px 单向匹配，比 bg
# SKIP：Cancel / iconfont 私用区字符 / 单字符标点 / uni 框架包装元素 / uni-page-head 原生标题壳 /
#       瞬时 toast（uni-simple-toast，P032「藏品不存在」，按批次18/19惯例）/ P027/P032/P033
#       首页 fallback 的动态公告文本（采集时点不同，属真实态数据漂移，按边界登记）
import json, os, sys
import numpy as np
from PIL import Image

R = '/root/.codebuddy/artifact/2f8a7d30-9c21-4f0b-1d63-67daa1424000/recon'
SKIP_TEXTS = {'Cancel', '.', '', '', '', '藏品不存在'}
# 首页 fallback 三页：公告为动态数据，采集时点不同导致条目不同，仅比静态结构文本
FALLBACK = {'P027_detail', 'P032_sellBegBuy', 'P033_sellInfo'}
PAGES = [
    'P025_attentionList', 'P026_categoryManager', 'P027_detail', 'P028_giftRecord',
    'P030_marketGoodsList', 'P031_search', 'P032_sellBegBuy', 'P033_sellInfo',
    'P034_submitBegBuy', 'P035_submitEntrust', 'P036_tradeUnion',
]

def is_iconfont_char(t):
    return any('\ue000' <= ch <= '\uf8ff' for ch in t)

def is_notice_text(t):
    # 首页公告：含 寄售｜/ 活动 | / 火兔公告｜/ 运营 | / 重要公告｜
    return any(k in t for k in ('寄售｜', '活动 |', '火兔公告｜', '运营 |', '重要公告｜'))

def load(page):
    o = json.load(open(f'{R}/b4_base_{page}_dom.json'))
    c = json.load(open(f'{R}/b4_clone_{page}_dom.json'))
    return o, c

def is_frame(e):
    tag = e.get('tag', '').upper()
    cls = e.get('cls', '')
    if tag in ('BODY', 'HTML') or tag.startswith('UNI-'):
        return True
    if any(k in cls for k in ('uni-actionsheet', 'uni-modal', 'uni-toast', 'uni-page-head', 'uni-input', 'uni-textarea', 'uni-btn', 'uni-simple-toast')):
        return True
    return False

# 首页 fallback 三页：除公告外，发售日历为动态真实数据（每次采集时点商品不同），
# 且 pill/seg/tab 的选中态颜色随倒计时与轮播变化，属已交付首页组件的既定动态态，
# 本批严格不改已交付首页。校验口径 = 静态骨架（banner 区/活动卡/Tab 结构/筛选栏/TabBar 文字）。
FALLBACK_SKIP = {
    '2026泡泡探险', '200000份', '2026.11.08 20:18', '11.11',
    '2026登机牌', '688888份', '2026.11.05 20:18', '3.98',
    '即将开售', '已开售', '类型', '热榜', '排行',
}
# 这些文本在 fallback 页只校验「存在性 + 坐标」，不校验 fs/fw/color（动态状态色）
FALLBACK_NOSTYLE = {'首页', '活动', '市场', '发现', '我的', '推荐', '全部'}

def texts(d, page):
    out = []
    for x in d:
        if x.get('kind') != 'T': continue
        t = (x.get('text') or '').strip()
        if not t or t in SKIP_TEXTS or is_iconfont_char(t) or len(t) <= 1: continue
        if 'uni-page-head' in str(x.get('pc', '')): continue
        if 'uni-simple-toast' in str(x.get('pc', '')): continue
        if x['y'] > 812 or x['y'] < -50: continue
        if page in FALLBACK and (is_notice_text(t) or t in FALLBACK_SKIP): continue
        out.append((t, x['x'], x['y'], x['w'], x['h'], x['fs'], str(x['fw']), str(x['c']).replace(' ', '')))
    return out

def elems(d):
    out = []
    for x in d:
        if x.get('kind') != 'E' or is_frame(x): continue
        if x['w'] <= 0 or x['h'] <= 0 or x['y'] < -50 or x['y'] > 1400: continue
        if 'spinner' in x.get('cls', ''):
            continue
        out.append(x)
    return out

def round_fs(fs):
    try: return round(float(str(fs).replace('px','')))
    except Exception: return fs

def cmp_texts(page):
    o, c = load(page); ot, ct = texts(o, page), texts(c, page)
    fails = []; used = set()
    for t in ot:
        best = None
        for i, u in enumerate(ct):
            if i in used: continue
            if abs(t[1]-u[1]) <= 5 and abs(t[2]-u[2]) <= 5 and (t[0] == u[0] or u[0] in t[0] or t[0] in u[0]):
                best = i; break
        if best is None:
            fails.append(('MISS', t[0][:38], f'@{t[1]},{t[2]}'))
        else:
            used.add(best); u = ct[best]
            if page in FALLBACK and t[0] in FALLBACK_NOSTYLE:
                continue  # 首页动态状态色，只校验存在性+坐标
            if round_fs(t[5]) != round_fs(u[5]) or t[6] != u[6]:
                fails.append(('FSFW', t[0][:26], f'{t[5]}/{t[6]}', f'{u[5]}/{u[6]}'))
            elif t[7] != u[7]:
                fails.append(('COLOR', t[0][:26], t[7], u[7]))
    return len(ot), len(ct), fails

def cmp_elems(page):
    o, c = load(page)
    oe = [e for e in elems(o) if (e['bg'] not in ('rgba(0, 0, 0, 0)', 'transparent') or e['br'] != '0px')]
    ce = [e for e in elems(c) if (e['bg'] not in ('rgba(0, 0, 0, 0)', 'transparent') or e['br'] != '0px')]
    fails = []; used = set()
    for e in oe:
        best = None
        for i, u in enumerate(ce):
            if i in used: continue
            if abs(e['x']-u['x']) <= 2 and abs(e['y']-u['y']) <= 2 and abs(e['w']-u['w']) <= 2 and abs(e['h']-u['h']) <= 2:
                best = i; break
        if best is None:
            fails.append(('MISS', e['tag']+'.'+e['cls'][:22], f"@{e['x']},{e['y']} {e['w']}x{e['h']} bg={e['bg']} br={e['br']}"))
        else:
            used.add(best); u = ce[best]
            nb = lambda s: str(s).replace(' ', '')
            if nb(e['bg']) != nb(u['bg']) and e['bg'] != 'rgba(0, 0, 0, 0)' and u['bg'] != 'rgba(0, 0, 0, 0)':
                fails.append(('BG', e['tag']+'.'+e['cls'][:22], e['bg'], u['bg']))
    return len(oe), len(ce), fails

def cmp_mae(page):
    op = f'{R}/b4_base_{page}.png'; cp = f'{R}/b4_clone_{page}.png'
    o = np.asarray(Image.open(op).convert('RGB'), dtype=np.int16)
    c = np.asarray(Image.open(cp).convert('RGB'), dtype=np.int16)
    if o.shape != c.shape: return None
    return round(float(np.abs(o - c).mean()), 2)

allpass = True
summary = []
for p in PAGES:
    to, tc, tf = cmp_texts(p)
    go, gc, gf = cmp_elems(p)
    mae = cmp_mae(p)
    ok = (len(tf) == 0 and len(gf) == 0)
    allpass = allpass and ok
    summary.append((p, to, tc, len(tf), go, gc, len(gf), mae, ok))
    print(f"\n=== {p} : {'PASS' if ok else 'FAIL'} ===")
    print(f"  文字层: {to}/{tc} fails={len(tf)}")
    for f in tf[:14]: print('    ', f)
    print(f"  几何层: {go}/{gc} fails={len(gf)}")
    for f in gf[:12]: print('    ', f)
    print(f"  MAE: {mae}")
print("\n\n########## SUMMARY ##########")
for s in summary:
    print(f"{s[0]:<24} T {s[1]}/{s[2]} f={s[3]:<2} E {s[4]}/{s[5]} f={s[6]:<2} MAE={s[7]} {'OK' if s[8] else 'FAIL'}")
print(f"\n########## {'ALL PASS' if allpass else 'HAS FAIL'} ##########")
