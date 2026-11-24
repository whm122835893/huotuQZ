<?php
// +----------------------------------------------------------------------
// | 商品详情控制器 —— 批次12（P077-P079）
// | 数据全部来自本项目 MySQL（ht_product / ht_product_notice），零原站运行时依赖。
// | 响应结构与原站 api.hnyiqin.asia 同构。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Product extends BaseController
{
    /**
     * POST /api/box/collection/ProductDetail/ids/<ids>?batch_id=<b>
     * P078 藏品详情。原站契约：
     *   - ids 缺失            -> code:0 "参数错误"
     *   - batch_id 缺失       -> code:0 "参数错误"
     *   - batch_id 与商品不符 -> code:0 "找不到是哪个批次"
     *   - 正常                -> code:1 "商品详情" + 完整 data
     */
    public function productDetail($ids = 0)
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $id       = (int)($ids ?: $this->request->param('id', 0));
        $batchId  = (int)$this->request->param('batch_id', 0);
        if (!$id || !$batchId) return $this->fail('参数错误');

        $row = Db::name('product')->where('id', $id)->find();
        if (!$row) return $this->fail('找不到该商品，或者已经下架了');
        if ((int)$row['batch_id'] !== $batchId) return $this->fail('找不到是哪个批次');

        $data = json_decode((string)$row['payload'], true);
        if (!is_array($data)) $data = [];
        // 与快照表实时字段对齐，避免 payload 与列不一致
        $data['id']                     = (int)$row['id'];
        $data['name']                   = (string)$row['name'];
        $data['goods_type']             = (int)$row['goods_type'];
        $data['main_image']             = (string)$row['main_image'];
        $data['listimg']                = (string)$row['listimg'];
        $data['price']                  = (string)$row['price'];
        $data['total_num']              = (int)$row['total_num'];
        $data['flux']                   = (int)$row['flux'];
        $data['batch_id']               = (int)$row['batch_id'];
        $data['release_time_date_text'] = (string)$row['release_time_date_text'];
        return $this->ok($data, '商品详情');
    }

    /**
     * POST /api/product/relatedNotice
     * {search: <商品名>, page: 1}
     * P079 相关公告。原站对应 /api/index/afficheList（该路径已被批次5 公告列表占用，
     * 且原站 search 返回结果与 ht_affiche 的 LIKE 结果不一致，故按原站真实返回快照独立建表）。
     * 契约：无 keyword 时前端不发起请求（原站纯白 navbar 空态）。
     */
    public function relatedNotice()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $search   = trim((string)$this->request->param('search', ''));
        $page     = max(1, (int)$this->request->param('page', 1));
        $listRows = max(1, (int)($this->request->param('list_rows', 15) ?: 15));

        if ($search === '') {
            return $this->ok($this->emptyPage($page, $listRows), '公告列表');
        }

        $q     = Db::name('product_notice')->where('keyword', $search);
        $total = (clone $q)->count();
        $rows  = $q->order('sort', 'asc')
            ->field('id,title,affiche_type,type,url,createtime')
            ->page($page, $listRows)->select()->toArray();
        foreach ($rows as &$r) {
            $r['id']         = (int)$r['id'];
            $r['createtime'] = (int)$r['createtime'];
        }
        unset($r);

        return $this->ok([
            'total'        => $total,
            'per_page'     => (string)$listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $rows,
        ], '公告列表');
    }

    private function emptyPage($page, $listRows)
    {
        return [
            'total' => 0, 'per_page' => (string)$listRows,
            'current_page' => $page, 'last_page' => 0, 'data' => [],
        ];
    }
}
