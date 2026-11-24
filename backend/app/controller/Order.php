<?php
// +----------------------------------------------------------------------
// | 订单控制器 —— 订单域（批次8：P054-P064）+ 我的-寄售中
// | 响应结构与原站同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Order extends BaseController
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

    /**
     * POST /api/order/order/myConsignment {status,types,goods_type,page}
     * 我的-寄售中列表。寄售数据模块尚未交付，返回与原站空态同构的分页结构。
     */
    public function myConsignment()
    {
        $user = $this->auth();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        return $this->ok([
            'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 0, 'data' => [],
        ], '寄售列表');
    }

    /**
     * POST /api/order/order/orderList {page,status}
     * 订单列表。主 Tab（常规/求购/委托/出售）由前端传 order_type，
     * status：0 全部 / 1 已完成 / 2 进行中 / 3 已取消（对齐原站实测：status=0 与 2 均返回全部 4 条）。
     */
    public function orderList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $page      = (int)$this->request->param('page', 1);
        $status    = (string)$this->request->param('status', '0');
        $orderType = (string)$this->request->param('order_type', '');

        $query = Db::name('order')->where('user_id', (int)$user['id']);
        if ($orderType !== '' && $orderType !== '0') $query->where('order_type', $orderType);
        // 原站实测：status=1（已完成）/3（已取消）为空，0（全部）/2（进行中）返回全部订单
        if ($status === '1')      $query->where('order_status', 4);
        elseif ($status === '3')  $query->where('order_status', 5);
        $query->order('id', 'desc');

        $p = $this->paginate($query, $page);
        foreach ($p['data'] as &$row) {
            $row['goods_cover_image'] = $this->imageUrl($row['goods_cover_image']);
            $row['collection_code']   = $row['collection_code'] ? json_decode($row['collection_code'], true) : [];
            $row['createtime']        = (int)$row['createtime'];
            $row['updatetime']        = (int)$row['updatetime'];
        }
        unset($row);
        return $this->ok($p, '订单列表');
    }

    /**
     * POST /api/order/order/orderDetail/ids/{id}
     * 常规订单详情。路径参数 ids 由路由捕获。
     */
    public function orderDetail($ids = 0)
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $id = (int)($ids ?: $this->request->param('id', 0));
        $order = Db::name('order')->where('id', $id)->where('user_id', (int)$user['id'])->find();
        if (!$order) return $this->fail('订单不存在');
        $order['goods_cover_image'] = $this->imageUrl($order['goods_cover_image']);
        $order['collection_code']   = $order['collection_code'] ? json_decode($order['collection_code'], true) : [];
        $order['product']           = ['id' => (int)$order['goods_id'], 'name' => $order['goods_name'], 'main_image' => $order['goods_cover_image']];
        $order['card']              = [];
        $order['surplus_time_text'] = 0;
        return $this->ok($order, '订单详情');
    }

    /** POST /api/order/order/saleMarketOrderDetail 市场（寄售）订单详情 */
    public function saleMarketOrderDetail()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $id = (int)$this->request->param('id', 0);
        if ($id <= 0) return $this->fail('订单ID不能为空');
        $order = Db::name('order')->where('id', $id)->where('user_id', (int)$user['id'])->find();
        if (!$order) return $this->fail('订单不存在');
        $order['goods_cover_image'] = $this->imageUrl($order['goods_cover_image']);
        $order['collection_code']   = $order['collection_code'] ? json_decode($order['collection_code'], true) : [];
        return $this->ok($order, '订单详情');
    }

    /** POST /api/order/order/giveOrderDetail/ids/{id} 赠送订单详情 */
    public function giveOrderDetail($ids = 0)
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $id = (int)($ids ?: $this->request->param('id', 0));
        $order = Db::name('order')->where('id', $id)->where('user_id', (int)$user['id'])->find();
        if (!$order) return $this->fail('找不到该订单或已被删除');
        $order['goods_cover_image'] = $this->imageUrl($order['goods_cover_image']);
        $order['collection_code']   = $order['collection_code'] ? json_decode($order['collection_code'], true) : [];
        return $this->ok($order, '订单详情');
    }

    /** POST /api/market/trade/begBuyOrderList {page,status} 我的求购列表 */
    public function begBuyOrderList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $page = (int)$this->request->param('page', 1);
        $query = Db::name('beg_buy_order')->where('user_id', (int)$user['id'])->order('id', 'desc');
        return $this->ok($this->paginate($query, $page), '订单信息');
    }

    /** POST /api/market/trade/begBuyOrderInfo {id} 求购详情 */
    public function begBuyOrderInfo()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $id = (int)$this->request->param('id', 0);
        if ($id <= 0) return $this->fail('订单ID不能为空');
        $row = Db::name('beg_buy_order')->where('id', $id)->where('user_id', (int)$user['id'])->find();
        if (!$row) return $this->fail('订单不存在');
        return $this->ok($row, '订单详情');
    }

    /** POST /api/market/Prepayment/myOrderlist {page} 我的委托列表 */
    public function entrustOrderList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $page = (int)$this->request->param('page', 1);
        $query = Db::name('entrust_order')->where('user_id', (int)$user['id'])->order('id', 'desc');
        return $this->ok($this->paginate($query, $page), '订单信息');
    }

    /** POST /api/market/Prepayment/myOrderInfo {id} 委托详情 */
    public function entrustOrderInfo()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $id = (int)$this->request->param('id', 0);
        if ($id <= 0) return $this->fail('订单ID不能为空');
        $row = Db::name('entrust_order')->where('id', $id)->where('user_id', (int)$user['id'])->find();
        if (!$row) return $this->fail('订单不存在');
        return $this->ok($row, '订单详情');
    }

    /** 图片路径本地化：原站 /uploads/... → /uploads/...（本项目后端 public/uploads 已同步） */
    private function imageUrl($path): string
    {
        if (!$path) return '';
        if (preg_match('#^https?://#', $path)) return $path;
        return '/' . ltrim($path, '/');
    }
}
