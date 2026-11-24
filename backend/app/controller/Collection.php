<?php
// +----------------------------------------------------------------------
// | 我的资产控制器 —— 批次9（P065-P068）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Collection extends BaseController
{
    /** 原站分页壳：{total,per_page,current_page,last_page,data[]} */
    private function pageShell(array $rows, int $total, int $page, int $listRows): array
    {
        return [
            'total'        => $total,
            'per_page'     => $listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $rows,
        ];
    }

    /** /uploads/... 原样返回，前端经 vite 代理访问本项目后端 */
    private function imageUrl(string $path): string
    {
        return $path;
    }

    /** 藏品定义 -> 原站 collection / product 子对象结构 */
    private function formatCollection(array $c): array
    {
        return [
            'id'                => (int)$c['id'],
            'name'              => $c['name'],
            'main_image'        => $this->imageUrl($c['main_image']),
            'listimg'           => $this->imageUrl($c['listimg']),
            'collection_level'  => '',
            'total_num_show'    => 0,
            'total_num'         => (int)$c['total_num'],
            // 批次11：盲盒详情（P072）「流通」徽标取此字段，原站 detail 快照 flux=0
            'flux'              => (int)($c['flux'] ?? 0),
            'is_img_diff'       => 0,
            'unfreeze_collection_id' => 0,
            'freeze_time'       => 0,
            'price'             => $c['price'],
            'series_id'         => (int)$c['series_id'],
            'series'            => ['id' => (int)$c['series_id'], 'name' => $c['series_name']],
            'publisher'         => ['name' => $c['publisher_name']],
            'weither_resale'    => (int)$c['weither_resale'],
            'weither_regift'    => (int)$c['weither_regift'],
            'purchase_information' => '',
            // 批次10 补全：寄售价格限制/费率（原站 product 同名字段，供 P069 寄售表单使用）
            'reprice_limit'        => $c['reprice_limit'] ?? '0.00',
            'reprice_min_limit'    => $c['reprice_min_limit'] ?? '0.00',
            'platform_resale_rate' => (int)($c['platform_resale_rate'] ?? 6),
            'price_ratio'          => (int)($c['price_ratio'] ?? 90),
            'price_pro'            => (int)($c['price_pro'] ?? 1),
        ];
    }

    /**
     * POST /api/user_collection/user_collection/antMycollection
     * {types:2 藏品 | 1 盲盒, page, collection_id}
     * P067 我的藏品主列表。types=2 返回 3 个藏品（持有 4 份）；types=1 盲盒原站为空。
     */
    public function antMycollection()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $types = (int)$this->request->param('types', 2);
        $page  = max(1, (int)$this->request->param('page', 1));

        if ($types !== 2) {
            // 原站盲盒 Tab：total=0 + current_count/current_consignment
            $shell = $this->pageShell([], 0, $page, 15);
            $shell['current_count'] = 0;
            $shell['current_consignment'] = 0;
            return $this->ok($shell, '获取成功');
        }

        $q = Db::name('user_collection')->where('user_id', (int)$user['id'])->order('utime', 'desc');
        $total = (clone $q)->count();
        $rows  = $q->page($page, 15)->select()->toArray();

        $ids = array_column($rows, 'collection_id');
        $colls = $ids ? Db::name('collection')->whereIn('id', $ids)->select()->toArray() : [];
        $map = [];
        foreach ($colls as $c) $map[$c['id']] = $c;

        $data = [];
        foreach ($rows as $r) {
            $c = $map[$r['collection_id']] ?? null;
            if (!$c) continue;
            $data[] = [
                'collection_id' => (int)$r['collection_id'],
                'num'           => (int)$r['num'],
                'sell_num'      => (string)$r['sell_num'],
                'utime'         => (int)$r['utime'],
                'collection'    => $this->formatCollection($c),
                'lock_num'      => (int)$r['lock_num'],
                'is_collection' => (int)$r['is_collection'],
                'can_compound'  => (int)$r['can_compound'],
            ];
        }
        $shell = $this->pageShell($data, $total, $page, 15);
        // 原站额外字段：当前持有总份数 / 当前寄售数
        $cnt = Db::name('user_collection')->where('user_id', (int)$user['id'])->field('SUM(num) AS n, SUM(sell_num) AS s')->find();
        $shell['current_count'] = (int)($cnt['n'] ?? 0);
        $shell['current_consignment'] = (int)($cnt['s'] ?? 0);
        return $this->ok($shell, '获取成功');
    }

    /**
     * POST /api/user_collection/user_collection/getDetail
     * {collection_id, types, page}
     * P066 持仓详情：返回 {list:分页实例, collection:藏品定义}
     */
    public function getDetail()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $cid  = (int)$this->request->param('collection_id', 0);
        $page = max(1, (int)$this->request->param('page', 1));

        $c = Db::name('collection')->where('id', $cid)->find();
        if (!$c) return $this->ok(['list' => $this->pageShell([], 0, $page, 1000), 'collection' => null], '获取成功');

        $q = Db::name('user_collection_item')
            ->where('user_id', (int)$user['id'])
            ->where('collection_id', $cid)
            ->order('createtime', 'desc');
        $total = (clone $q)->count();
        $rows  = $q->page($page, 1000)->select()->toArray();

        $list = [];
        foreach ($rows as $r) {
            $list[] = [
                'collection_id'    => (int)$r['collection_id'],
                'collection_code'  => (int)$r['collection_code'],
                'rz_code'          => $r['rz_code'],
                'status'           => (int)$r['status'],
                'source_type'      => (int)$r['source_type'],
                'from_price'       => $r['from_price'],
                'id'               => (int)$r['id'],
                'cancel_time'      => 0,
                'updatetime'       => (int)$r['updatetime'],
                'createtime'       => (int)$r['createtime'],
                'unfreeze_collection_time' => (int)$r['createtime'],
                'unfreeze_collection_lock' => 0,
                'resale_time'      => 0,
                'sale_price'       => $r['sale_price'],
            ];
        }
        return $this->ok([
            'list'       => $this->pageShell($list, $total, $page, 1000),
            'collection' => $this->formatCollection($c),
        ], '获取成功');
    }

    /**
     * POST /api/user_collection/user_collection/detail/ids/{id}
     * P065 藏品详情：单实例 + product 子对象 + 持有者信息
     */
    public function detail($ids = 0)
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $iid = (int)($ids ?: $this->request->param('id', 0));
        $item = Db::name('user_collection_item')
            ->where('id', $iid)
            ->where('user_id', (int)$user['id'])
            ->find();
        if (!$item) return $this->fail('收藏不存在或者已经被删除了', 0);

        $c = Db::name('collection')->where('id', (int)$item['collection_id'])->find();
        $product = $c ? $this->formatCollection($c) : ['id' => 0, 'name' => '', 'main_image' => '', 'total_num' => 0, 'publisher' => ['name' => '']];
        $product['price_pro'] = 1;
        $product['is_like'] = 0;
        $product['imageinfo'] = ['imagewidth' => '680', 'imageheight' => '680'];

        return $this->ok([
            'id'              => (int)$item['id'],
            'user_id'         => (int)$item['user_id'],
            'order_id'        => (int)$item['order_id'],
            'order_type'      => 1,
            'collection_id'   => (int)$item['collection_id'],
            'collection_code' => (int)$item['collection_code'],
            'status'          => (int)$item['status'],
            'types'           => (int)$item['types'],
            'source_type'     => (int)$item['source_type'],
            'from_price'      => $item['from_price'],
            'rz_code'         => $item['rz_code'],
            'createtime'      => (int)$item['createtime'],
            'updatetime'      => (int)$item['updatetime'],
            'user_hash'       => $item['user_hash'],
            'user_hash_text'  => substr($item['user_hash'], 0, 8) . '****',
            'product'         => $product,
            'user'            => [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'] ?? $user['username'],
            ],
        ], '详情');
    }

    /**
     * POST /api/user_collection/user_collection/likeCollection {keyword}
     * P068 搜索：按藏品名模糊匹配，返回 {collection_id,name}[]（原站实测结构）
     */
    public function likeCollection()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $kw = trim((string)$this->request->param('keyword', ''));
        if ($kw === '') return $this->ok([], '获取成功');
        $rows = Db::name('collection')
            ->whereLike('name', '%' . $kw . '%')
            ->field('id AS collection_id, name')
            ->order('id', 'asc')
            ->select()->toArray();
        return $this->ok($rows, '获取成功');
    }
}
