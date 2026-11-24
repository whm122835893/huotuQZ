#!/usr/bin/env python3
# 批次20 三层像素对比（批次19 口径）：文字节点层 / 关键元素几何 / 整图 MAE + 背景采样
# SKIP：Cancel（uni-actionsheet 系统隐藏项 @y=822）、\ue601 返回箭头（iconfont 渲染一致）、
#       spinner（P096 旋转动画包围盒随帧变化，几何层 SKIP，以 MAE 验证）、
#       uni 框架包装元素（BODY/HTML/UNI-*，本地以等价 div 实现）
import json, os, sys
import numpy as np
from PIL import Image

R = '/root/.codebuddy/artifact/2f8a7d30-9c21-4f0b-1d63-67daa1424000/recon'
SKIP_TEXTS = {'Cancel'}
PAGES = ['communityShare', 'drawResult', 'assist', 'leaderboard', 'shareGroup']

def load(page):
    o = json.load(open(f'{R}/b20_base_{page}_dom.json'))
    c = json.load(open(f'{R}/b20_clone_{page}_dom.json'))
    return o, c

def is_frame(e):
    tag = e.get('tag', '').upper()
    cls = e.get('cls', '')
    if tag in ('BODY', 'HTML') or tag.startswith('UNI-'):
        return True
    if 'uni-actionsheet' in cls or 'uni-modal' in cls or 'uni-toast' in cls:
        return True
    return False

def texts(d):
    out = []
    for x in d:
        if x.get('kind') != 'T':
            continue
        t = (x.get('text') or '').strip()
        if not t or t in SKIP_TEXTS or t.startswith('\ue601'):
            continue
        if x['y'] > 812 or x['y'] < -50:
            continue
        out.append((t, x['x'], x['y'], x['w'], x['h'], x['fs'], str(x['fw']), str(x['c']).replace(' ', '')))
    return out

def elems(d):
    out = []
    for x in d:
        if x.get('kind') != 'E' or is_frame(x):
            continue
        if x['w'] <= 0 or x['h'] <= 0 or x['y'] < -50 or x['y'] > 1400:
            continue
        if 'spinner' in x.get('cls', ''):  # 旋转动画包围盒不定
            continue
        out.append(x)
    return out

def cmp_texts(page):
    o, c = load(page)
    ot, ct = texts(o), texts(c)
    fails = []
    used = set()
    for t in ot:
        best = None
        for i, u in enumerate(ct):
            if i in used: continue
            if abs(t[1]-u[1]) <= 4 and abs(t[2]-u[2]) <= 4 and (
               t[0] == u[0] or u[0] in t[0] or t[0] in u[0]):
                best = i; break
        if best is None:
            fails.append(('MISS', t[0][:40], t[1], t[2]))
        else:
            used.add(best)
            u = ct[best]
            if t[5] != u[5] or t[6] != u[6]:
                fails.append(('FSFW', t[0][:30], t[5]+'/'+t[6], u[5]+'/'+u[6]))
            elif t[7] != u[7]:
                fails.append(('COLOR', t[0][:30], t[7], u[7]))
    return len(ot), len(ct), fails

def cmp_elems(page):
    o, c = load(page)
    oe = [e for e in elems(o) if (e['bg'] not in ('rgba(0, 0, 0, 0)', 'transparent') or e['br'] != '0px')]
    ce = [e for e in elems(c) if (e['bg'] not in ('rgba(0, 0, 0, 0)', 'transparent') or e['br'] != '0px')]
    fails = []
    used = set()
    for e in oe:
        best = None
        for i, u in enumerate(ce):
            if i in used: continue
            if abs(e['x']-u['x']) <= 2 and abs(e['y']-u['y']) <= 2 and \
               abs(e['w']-u['w']) <= 2 and abs(e['h']-u['h']) <= 2:
                best = i; break
        if best is None:
            fails.append(('MISS', e['tag']+'.'+e['cls'][:25], f"@{e['x']},{e['y']} {e['w']}x{e['h']} bg={e['bg']} br={e['br']}"))
        else:
            used.add(best)
            u = ce[best]
            nb = lambda s: str(s).replace(' ', '')
            if nb(e['bg']) != nb(u['bg']) and e['bg'] != 'rgba(0, 0, 0, 0)' and u['bg'] != 'rgba(0, 0, 0, 0)':
                fails.append(('BG', e['tag']+'.'+e['cls'][:25], e['bg'], u['bg']))
    return len(oe), len(ce), fails

def cmp_mae(page):
    o = np.asarray(Image.open(f'{R}/b20_base_{page}.png').convert('RGB'), dtype=np.int16)
    c = np.asarray(Image.open(f'{R}/b20_clone_{page}.png').convert('RGB'), dtype=np.int16)
    if o.shape != c.shape:
        return None, f'shape {o.shape} vs {c.shape}'
    return float(np.abs(o - c).mean()), ''

results = {}
all_pass = True
for p in PAGES:
    nt_o, nt_c, tf = cmp_texts(p)
    ne_o, ne_c, ef = cmp_elems(p)
    mae, msg = cmp_mae(p)
    ok = not tf and not ef and mae is not None and mae < 8
    if not ok: all_pass = False
    results[p] = {'MAE': None if mae is None else round(mae, 2)}
    print(f"== {p} ==  {'PASS' if ok else 'FAIL'}")
    print(f"  文字层: orig={nt_o} clone={nt_c} fails={len(tf)}")
    for f in tf[:12]: print('   ', f)
    print(f"  几何层: orig={ne_o} clone={ne_c} fails={len(ef)}")
    for f in ef[:12]: print('   ', f)
    print(f"  整图MAE: {results[p]['MAE']} {msg}")
    print()

print('ALL PASS' if all_pass else 'HAS FAIL')
sys.exit(0 if all_pass else 1)
