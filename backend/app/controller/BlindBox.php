<?php
// +----------------------------------------------------------------------
// | 盲盒控制器 —— 批次11（P072-P076）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class BlindBox extends BaseController
{
    /**
     * POST /api/box/blind_box/openlog
     * {blind_box_id, page, list_rows}
     * P074 开盒记录。原站该账号 total=0；本项目按 ht_blind_box_openlog 真实数据分页返回。
     */
    public function openlog()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $boxId    = (int)$this->request->param('blind_box_id', 0);
        $page     = max(1, (int)$this->request->param('page', 1));
        $listRows = max(1, (int)$this->request->param('list_rows', 20));

        $q = Db::name('blind_box_openlog');
        if ($boxId > 0) $q->where('blind_box_id', $boxId);
        $total = (clone $q)->count();
        $rows  = $q->order('createtime', 'desc')->page($page, $listRows)->select()->toArray();

        $list = [];
        foreach ($rows as $r) {
            $list[] = [
                'id'          => (int)$r['id'],
                'createtime'  => (int)$r['createtime'],
                'userss'      => ['nickname' => $r['nickname']],
                'collections' => ['name' => $r['collection_name']],
            ];
        }

        return $this->ok([
            'total'        => $total,
            'per_page'     => (string)$listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $list,
        ], '开盲盒记录');
    }

    /**
     * POST /api/user_collection/user_collection/openbox
     * {openid} 逐个拆开。原站对无可开盲盒返回 code:0「没有啦」。
     * 本项目按 ht_user_blind_box 判定：有可开盲盒则返回单个结果，否则如实返回 code:0。
     */
    public function openbox()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $openid = (int)$this->request->param('openid', 0);
        $row = Db::name('user_blind_box')
            ->where('user_id', (int)$user['id'])
            ->where('num', '>', 0)
            ->where(function ($q) use ($openid) { if ($openid > 0) $q->where('blind_box_id', $openid); })
            ->find();
        if (!$row) return $this->fail('没有啦');

        $c = Db::name('collection')->where('id', (int)$row['collection_id'])->find();
        if (!$c) return $this->fail('没有啦');

        return $this->ok([
            'name'            => $c['name'],
            'main_image'      => $c['main_image'],
            'collection_code' => (int)$row['collection_code'],
            'rarity_icon'     => '/static/cw/rank_icon_1.png',
        ], '开启成功');
    }

    /**
     * POST /api/user_collection/user_collection/openBoxs
     * {openid} 一键开启（一次最多 10 个）。原站重复提交返回 code:0「请勿重复提交」。
     */
    public function openBoxs()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $openid = (int)$this->request->param('openid', 0);
        $q = Db::name('user_blind_box')
            ->where('user_id', (int)$user['id'])
            ->where('num', '>', 0);
        if ($openid > 0) $q->where('blind_box_id', $openid);
        $rows = $q->limit(10)->select()->toArray();
        if (!$rows) return $this->fail('请勿重复提交');

        $list = [];
        foreach ($rows as $r) {
            $c = Db::name('collection')->where('id', (int)$r['collection_id'])->find();
            if (!$c) continue;
            $list[] = [
                'name'            => $c['name'],
                'listimg'         => $c['listimg'],
                'collection_code' => (int)$r['collection_code'],
                'rarity_icon'     => '/static/cw/rank_icon_1.png',
            ];
        }
        if (!$list) return $this->fail('请勿重复提交');
        return $this->ok($list, '开启成功');
    }

    /**
     * POST /api/box/blind_box/BoxDetail/ids/{id}
     * {origin, crystal_goods_id} 盲盒商品详情（侦查到，本批 5 页均未调用，如实实现壳）。
     * 原站 2026-11-12 实测：对 id=33 / id=20 均返回 code:0「找不到该盲盒，或者已经下架了」
     * （发售日历在售但详情页已下架），本项目按此真实响应同构返回，不臆造详情数据。
     */
    public function boxDetail($ids = 0)
    {
        return $this->fail('找不到该盲盒，或者已经下架了');
    }
}
