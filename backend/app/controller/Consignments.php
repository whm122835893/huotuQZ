<?php
// +----------------------------------------------------------------------
// | 寄售大厅控制器 —— 批次10（P069-P071）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Consignments extends BaseController
{
    /**
     * POST /api/user_collection/user_collection/show_resale_money
     * {price, user_collection_id}
     * P069 寄售表单：根据寄售价格计算服务费、版权分成、预计收入。
     * 配置来自 ht_consignment_config（原站真实快照）；金额按原站公式动态计算。
     */
    public function resaleMoney()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $price = (float)$this->request->param('price', 0);
        if ($price < 0) $price = 0;

        $cfg = Db::name('consignment_config')->order('id', 'asc')->find();
        if (!$cfg) $cfg = [
            'minprice' => 0, 'price_pro' => 1, 'price_price_ratio' => 90,
            'pay_platform_service_rate' => '0.0600', 'share_proportion_rate' => '0.0000',
            'low_price' => 0, 'low_price_text' => '', 'tips' => '',
        ];

        $serviceRate = (float)$cfg['pay_platform_service_rate'];
        $shareRate   = (float)$cfg['share_proportion_rate'];
        $servicePrice = round($price * $serviceRate, 2);
        $sharePrice   = round($price * $shareRate, 2);
        $realPrice    = round($price - $servicePrice - $sharePrice, 2);

        return $this->ok([
            'minprice'                 => (float)$cfg['minprice'],
            'price_pro'                => (int)$cfg['price_pro'],
            'tips'                     => $cfg['tips'],
            'price_price_ratio'        => (int)$cfg['price_price_ratio'],
            'pay_platform_service_rate' => number_format($serviceRate, 2, '.', ''),
            'share_proportion_rate'    => number_format($shareRate, 2, '.', ''),
            'platform_service_price'   => number_format($servicePrice, 2, '.', ''),
            'share_proportion_price'   => number_format($sharePrice, 2, '.', ''),
            'real_price'               => number_format($realPrice, 2, '.', ''),
            'low_price'                => (float)$cfg['low_price'],
            'low_price_text'           => $cfg['low_price_text'],
        ], '相关金额');
    }

    /**
     * POST /api/order/order/myConsignment
     * {status, goods_type, page} / {keyword, status, goods_type, page}
     * P070 我的寄售列表 + P071 寄售搜索（同一接口，keyword 为名称模糊匹配）。
     * 原站该账号寄售订单 total=0，本项目 ht_consignment 无种子数据 → 恒返回空分页壳。
     */
    public function myConsignment()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $page      = max(1, (int)$this->request->param('page', 1));
        $listRows  = 10;
        $keyword   = trim((string)$this->request->param('keyword', ''));
        $status    = $this->request->param('status', '');
        $goodsType = $this->request->param('goods_type', '');

        $query = Db::name('consignment')->where('user_id', (int)$user['id']);
        if ($keyword !== '') $query->whereLike('goods_name', '%' . $keyword . '%');
        if ($status !== '')   $query->where('status', (int)$status);
        if ($goodsType !== '') $query->where('goods_type', (int)$goodsType);

        $total = (clone $query)->count();
        $rows  = $query->page($page, $listRows)->select()->toArray();
        $list  = [];
        foreach ($rows as $r) {
            $list[] = [
                'id'                => (int)$r['id'],
                'goods_name'        => $r['goods_name'],
                'goods_cover_image' => $r['goods_cover_image'],
                'goods_type'        => (int)$r['goods_type'],
                'price'             => $r['price'],
                'status'            => (int)$r['status'],
                'user_collection'   => [
                    'collection_code' => $r['collection_code'],
                    'total_num'       => (int)$r['total_num'],
                ],
            ];
        }

        return $this->ok([
            'total'        => $total,
            'per_page'     => $listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $list,
        ], '寄售列表');
    }
}
