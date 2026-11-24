<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use think\facade\Db;

/**
 * 水晶（龙晶）商城域接口（批次7 · P047-P053）
 *
 * 对齐原站 /api/crystal_goods/* —— list / info / crystalDetail /
 * exchangeRecord / exchange / getCardCode
 *
 * 侦查结论（2026-11-09）：原站龙晶商城当前为未开放状态，
 * list / crystalDetail / exchangeRecord 均返回 total=0 的空分页；
 * info 对任意 id 返回 code:0「商品不存在」，前端详情页 1.5s 后 backPage 退回首页；
 * exchange 无地址与操作密码时返回「请输入操作密码」；确认订单页无商品上下文时整页白屏。
 * 本项目 DB 保持与原站一致的空数据态，接口契约与分页结构同构。
 */
class Crystal extends BaseController
{
    /** 统一分页壳：与原站 {total,per_page,current_page,last_page,data[]} 一致 */
    private function paginate($query, int $page, int $listRows = 15): array
    {
        $page = max(1, $page);
        $total = (clone $query)->count();
        $rows  = $query->page($page, $listRows)->select()->toArray();
        return [
            'total'        => $total,
            'per_page'     => $listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $rows,
        ];
    }

    /** POST /api/crystal_goods/list 商品列表（tab: type 10藏品/20盲盒/30实物；crystal_type yellow/purple/blue） */
    public function goodsList()
    {
        $type   = (string)$this->request->param('type', '10');
        $crystalType = (string)$this->request->param('crystal_type', '');
        $page   = (int)$this->request->param('page', 1);

        $query = Db::name('crystal_goods')->where('status', 1);
        if ($type !== '') $query->where('type', $type);
        if ($crystalType !== '') $query->where('crystal_type', $crystalType);
        $query->order('sort', 'asc')->order('id', 'asc');

        return $this->ok($this->paginate($query, $page), 'successfully !!!');
    }

    /** POST /api/crystal_goods/info 商品详情（详情页 onLoad 传 goods_id / id / collection_id / batch_id / blind_box_id） */
    public function info()
    {
        $goodsId = (int)($this->request->param('goods_id') ?: $this->request->param('id') ?: 0);
        if ($goodsId <= 0) return $this->fail('商品不存在', 0);
        $goods = Db::name('crystal_goods')->where('id', $goodsId)->where('status', 1)->find();
        if (!$goods) return $this->fail('商品不存在', 0);
        return $this->ok($this->formatGoods($goods), 'successfully !!!');
    }

    /** POST /api/crystal_goods/crystalDetail 龙晶明细（全部/龙晶获取/龙晶消耗） */
    public function crystalDetail()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $page = (int)$this->request->param('page', 1);
        $type = (int)$this->request->param('type', 0);

        $query = Db::name('crystal_detail')->where('user_id', (int)$user['id']);
        if ($type > 0) $query->where('type', $type);
        $query->order('id', 'desc');
        return $this->ok($this->paginate($query, $page), 'successfully !!!');
    }

    /** POST /api/crystal_goods/exchangeRecord 兑换记录 */
    public function exchangeRecord()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $page = (int)$this->request->param('page', 1);
        $query = Db::name('crystal_exchange_record')->where('user_id', (int)$user['id'])->order('id', 'desc');
        return $this->ok($this->paginate($query, $page), 'successfully !!!');
    }

    /** POST /api/crystal_goods/exchange 立即兑换（需操作密码，成功后扣龙晶并写兑换记录） */
    public function exchange()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $payPwd = (string)$this->request->param('pay_password', '');
        if ($payPwd === '') return $this->fail('请输入操作密码', 0);

        $goodsId = (int)$this->request->param('goods_id', 0);
        $number  = max(1, (int)$this->request->param('number', 1));
        $goods = Db::name('crystal_goods')->where('id', $goodsId)->where('status', 1)->find();
        if (!$goods) return $this->fail('商品不存在', 0);

        $field = $this->crystalField((string)$goods['crystal_type']);
        $cost  = (int)$goods['crystal_number'] * $number;
        if ((int)$user[$field] < $cost) return $this->fail('水晶不足', 0);

        Db::startTrans();
        try {
            Db::name('user')->where('id', (int)$user['id'])->dec($field, $cost)->update();
            $recordId = Db::name('crystal_exchange_record')->insertGetId([
                'user_id'        => (int)$user['id'],
                'goods_id'       => $goodsId,
                'goods_name'     => $goods['goods_name'],
                'list_image'     => $goods['list_image'],
                'crystal_number' => (int)$goods['crystal_number'],
                'num'            => $number,
                'status'         => 1,
                'createtime'     => time(),
            ]);
            Db::name('crystal_detail')->insert([
                'user_id'        => (int)$user['id'],
                'type'           => 2,
                'crystal_number' => -$cost,
                'memo'           => '兑换商品',
                'createtime'     => time(),
            ]);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->fail('兑换失败', 0);
        }
        return $this->ok(['id' => $recordId, 'password_error' => 0], '兑换成功');
    }

    /** POST /api/crystal_goods/getCardCode 卡密（原站空实现，保持同构） */
    public function getCardCode()
    {
        return $this->ok(null, 'successfully !!!');
    }

    /** 商品输出格式化：与原站 info / list 字段同构 */
    private function formatGoods(array $g): array
    {
        $img = (string)$g['list_image'];
        return [
            'id'              => (int)$g['id'],
            'goods_id'        => (int)$g['id'],
            'goods_name'      => $g['goods_name'],
            'type'            => (int)$g['type'],
            'goods_type'      => (int)$g['type'],
            'crystal_type'    => $g['crystal_type'],
            'crystal_number'  => (int)$g['crystal_number'],
            'limited'         => (int)$g['limited'],
            'stock'           => (int)$g['stock'],
            'status'          => (int)$g['status'],
            'exchange_status' => (int)$g['exchange_status'],
            'list_image'      => $img,
            'main_image'      => (string)$g['main_image'],
            'description'     => (string)$g['description'],
            'content'         => (string)$g['content'],
            'batch_id'        => (int)$g['batch_id'],
            'collection_id'   => (int)$g['collection_id'],
            'blind_box_id'    => (int)$g['blind_box_id'],
            'product'         => [
                'id'         => (int)$g['id'],
                'name'       => $g['goods_name'],
                'listimg'    => $img,
                'main_image' => (string)$g['main_image'],
                'goods_type' => (int)$g['type'],
                'total_num'  => (int)$g['limited'],
                'crystal'    => [
                    'crystal_type'   => $g['crystal_type'],
                    'crystal_price'  => (int)$g['crystal_number'],
                    'crystal_number' => (int)$g['crystal_number'],
                    'stock'          => (int)$g['stock'],
                ],
            ],
        ];
    }

    /** 水晶种类 → 用户余额列 */
    private function crystalField(string $type): string
    {
        return match ($type) {
            'purple' => 'crystal_purple',
            'blue'   => 'crystal_blue',
            default  => 'crystal_yellow',
        };
    }
}
