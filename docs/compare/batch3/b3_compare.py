#!/usr/bin/env python3
# 批次3 我的域 14 页 三层像素对比（批次19/23 口径）
# 文字层：按坐标就近(±4px)+文本包含匹配，比 fs/fw/color（fs 数值容差）
# 几何层：仅比「有背景色/圆角」的实质元素，按坐标±2px 单向匹配，比 bg
# SKIP：Cancel、iconfont 私用区字符（原站 uni iconfont，本地以同源 PNG/SVG 复刻）、
#       单字符标点（原站拆碎的 '.'）、uni 框架包装元素、uni-page-head 原生标题壳。
import json, os
import numpy as np
from PIL import Image

R = '/root/.codebuddy/artifact/2f8a7d30-9c21-4f0b-1d63-67daa1424000/recon'
SKIP_TEXTS = {'Cancel', '.', '', '', ''}
PAGES = [
    'P009_about','P010_getCode','P011_setPwd','P012_address_edit','P013_address_list',
    'P014_cancellation','P015_depositinquiry','P016_enablingcenter','P017_flowDoc',
    'P018_info','P019_join','P021_privacy','P022_service','P024_vip',
]

def is_iconfont_char(t):
    return any('\ue000' <= ch <= '\uf8ff' for ch in t)

def load(page):
    o = json.load(open(f'{R}/b3_base_{page}_dom.json'))
    c = json.load(open(f'{R}/b3_clone_{page}_dom.json'))
    return o, c

def is_frame(e):
    tag = e.get('tag', '').upper()
    cls = e.get('cls', '')
    if tag in ('BODY', 'HTML') or tag.startswith('UNI-'):
        return True
    if any(k in cls for k in ('uni-actionsheet','uni-modal','uni-toast','uni-page-head','uni-input','uni-textarea','uni-btn')):
        return True
    return False

def texts(d):
    out = []
    for x in d:
        if x.get('kind') != 'T': continue
        t = (x.get('text') or '').strip()
        if not t or t in SKIP_TEXTS or is_iconfont_char(t) or len(t) <= 1: continue
        if 'uni-page-head' in str(x.get('pc', '')): continue
        if x['y'] > 812 or x['y'] < -50: continue
        out.append((t, x['x'], x['y'], x['w'], x['h'], x['fs'], str(x['fw']), str(x['c']).replace(' ', '')))
    return out

def elems(d):
    out = []
    for x in d:
        if x.get('kind') != 'E' or is_frame(x): continue
        if x['w'] <= 0 or x['h'] <= 0 or x['y'] < -50 or x['y'] > 1400: continue
        out.append(x)
    return out

def round_fs(fs):
    try: return round(float(str(fs).replace('px','')))
    except Exception: return fs

def cmp_texts(page):
    o, c = load(page); ot, ct = texts(o), texts(c)
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
    op = f'{R}/b3_base_{page}.png'; cp = f'{R}/b3_clone_{page}.png'
    o = np.asarray(Image.open(op).convert('RGB'), dtype=np.int16)
    c = np.asarray(Image.open(cp).convert('RGB'), dtype=np.int16)
    if o.shape != c.shape: return None
    return round(float(np.abs(o - c).mean()), 2)

allpass = True
for p in PAGES:
    to, tc, tf = cmp_texts(p)
    go, gc, gf = cmp_elems(p)
    mae = cmp_mae(p)
    ok = (len(tf) == 0 and len(gf) == 0)
    allpass = allpass and ok
    print(f"\n=== {p} : {'PASS' if ok else 'FAIL'} ===")
    print(f"  文字层: {to}/{tc} fails={len(tf)}")
    for f in tf[:12]: print('    ', f)
    print(f"  几何层: {go}/{gc} fails={len(gf)}")
    for f in gf[:10]: print('    ', f)
    print(f"  MAE: {mae}")
print(f"\n########## {'ALL PASS' if allpass else 'HAS FAIL'} ##########")
