<?php
// +----------------------------------------------------------------------
// | 市场控制器 —— 市场列表（响应结构与原站同构，数据来自本项目 MySQL）
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Market extends BaseController
{
    /** 市场列表项结构（与原站字段同构） */
    private function shapeItem(array $r): array
    {
        $minPrice = $r['min_price'];
        return [
            'name'                => $r['name'],
            'series_id'           => (int)$r['series_id'],
            'id'                  => (int)$r['id'],
            'listimg'             => $r['listimg'],
            'total_num'           => (int)$r['total_num'],
            'reprice_limit'       => $r['reprice_limit'] !== null ? (string)$r['reprice_limit'] : null,
            'collection_level'    => '',
            'openresale_time'     => (int)$r['openresale_time'],
            'flag'                => 0,
            'select_status'       => 0,
            'deal_num'            => (int)$r['deal_num'],
            'flux'                => (string)$r['flux'],
            'is_like'             => 0,
            'market_fluctuate_new' => (float)$r['fluctuate'],
            'market_value'        => $minPrice !== null ? (int)round((float)$minPrice * (int)$r['total_num']) : 0,
            'market_fluctuate'    => 0,
            'min_price'           => $minPrice !== null ? (float)$minPrice : null,
            'is_out'              => (int)$r['is_out'],
            'new_time'            => (int)$r['openresale_time'],
            'goods_type'          => (string)$r['goods_type'],
            'product'             => [
                'id'        => (int)$r['id'],
                'name'      => $r['name'],
                'listimg'   => $r['listimg'],
                'total_num' => (int)$r['total_num'],
            ],
        ];
    }

    /**
     * POST /api/market/market/getMarketList  {goods_type,page,list_rows,series_id,add_market_fluctuate,add_deal_num}
     * series_id: 0=全部 -1=推荐 -2=我的关注；响应 data 为纯数组（与原站一致）
     */
    public function getMarketList()
    {
        $user = $this->auth();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        $goodsType = (int)($this->request->param('goods_type') ?? 2);
        $seriesId = (int)($this->request->param('series_id') ?? 0);
        $page = max(1, (int)($this->request->param('page') ?? 1));
        $rows = (int)($this->request->param('list_rows') ?? 10);

        // 我的关注 / 推荐：当前无数据，返回空数组（与原站同构）
        if ($seriesId === -2 || $seriesId === -1) {
            return $this->ok([]);
        }

        $q = Db::name('market')->where('goods_type', $goodsType);
        if ($seriesId > 0) {
            $q->where('series_id', $seriesId);
        }
        $list = $q->order('sort', 'asc')->page($page, $rows)->select()->toArray();
        return $this->ok(array_map([$this, 'shapeItem'], $list));
    }

    /**
     * POST /api/market/market/marketList  {goods_type,page,list_rows,series_id}
     * 盲盒市场列表（分页结构）。
     */
    public function marketList()
    {
        $user = $this->auth();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        $goodsType = (int)($this->request->param('goods_type') ?? 1);
        $seriesId = (int)($this->request->param('series_id') ?? 0);
        $page = max(1, (int)($this->request->param('page') ?? 1));
        $rows = (int)($this->request->param('list_rows') ?? 10);

        $q = Db::name('market')->where('goods_type', $goodsType);
        if ($seriesId > 0) {
            $q->where('series_id', $seriesId);
        }
        $total = (clone $q)->count();
        $list = $q->order('sort', 'asc')->page($page, $rows)->select()->toArray();
        return $this->ok([
            'total'        => $total,
            'per_page'     => (string)$rows,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / max(1, $rows)),
            'data'         => array_map([$this, 'shapeItem'], $list),
        ]);
    }
}
