<?php
// +----------------------------------------------------------------------
// | 排行控制器 —— 批次18（P089 大师动态 / P090 名人堂 / P091 热榜）
// | 响应结构与原站 api.hnyiqin.asia 逐字同构，零原站运行时依赖。
// |
// | 侦查实测（2026-11-15，页面内 fetch，header token + version:1.0.1）：
// |   POST /api/user/newrank1 {}                    -> code:0「名人堂未开启」data:null
// |   POST /api/market/market/marketrebang {}      -> code:1「ok」data: 6 条真实榜单
// |   masterRank 无专用接口（仅 getConfig / getUserInfo 全局），list 为空态。
// |
// | [兼容性决策] 原站同名的 /api/user/newrank1 与 /api/market/market/marketrebang
// |   在本项目早期批次已实现，且被【已交付的首页 index.vue / 市场 market.vue】调用，
// |   返回的是彼时的演示数据（newrank1 带 rank2 榜单、marketrebang 单条商品）。
// |   直接改写这两个端点将破坏已完成页面（违反「不私自增删改已完成内容」硬规则），
// |   故本批新增 /api/rank/* 三个专用端点，承载原站【当前】真实态快照，
// |   仅由批次18 三页调用。差异登记于 docs/02-API文档.md 与 OPEN-DECISIONS.md。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;

class Rank extends BaseController
{
    /** POST /api/rank/masterList —— P089 大师动态：原站 list 为空，纯空态页 */
    public function masterList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->ok([], 'ok');
    }

    /** POST /api/rank/hallOfFame {type:1购入/2售出}
     *  P090 名人堂：原站 newrank1 返回 code:0「名人堂未开启」data:null，
     *  chunk .catch 触发「名人堂加载失败」toast 并置 loading=empty。逐字复刻。 */
    public function hallOfFame()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return json([
            'code' => 0,
            'msg'  => '名人堂未开启',
            'time' => (string)time(),
            'data' => null,
        ]);
    }

    /** POST /api/rank/hotList {} —— P091 热榜：原站 marketrebang 真实 6 条快照 */
    public function hotList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return json([
            'code' => 1,
            'msg'  => 'ok',
            'time' => (string)time(),
            'data' => $this->hotSnapshot(),
        ]);
    }

    /** 原站 marketrebang data 逐字快照（listimg 为原站相对路径，
     *  前端 demo.js 的 localHotImg() 将其映射为已本地化的 b18-hot1..6，不请求原站 CDN）。 */
    private function hotSnapshot()
    {
        return [
            ['id' => 23, 'name' => '熊小七',          'total_num' => 3000,  'flux' => 1500,  'listimg' => '/uploads/20261105/76c4b4a3d1285e5cd5f085bf511aa3ef.jpg', 'reprice_limit' => '1000.00',  'hotnum' => 951, 'is_like' => 0, 'min_price' => 150],
            ['id' => 22, 'name' => '2026青花瓷-龙王', 'total_num' => 2888,  'flux' => 2700,  'listimg' => '/uploads/20261105/22f503c727362cf58a1cf76b2846c99a.jpg', 'reprice_limit' => '10000.00', 'hotnum' => 887, 'is_like' => 0, 'min_price' => 380],
            ['id' => 25, 'name' => '大吉大利',        'total_num' => 5000,  'flux' => 1000,  'listimg' => '/uploads/20261106/3009215916b46e7fadf16520c1bd7a2c.jpg', 'reprice_limit' => '300.00',   'hotnum' => 668, 'is_like' => 0, 'min_price' => 110],
            ['id' => 24, 'name' => '天官赐福',        'total_num' => 1500,  'flux' => 800,   'listimg' => '/uploads/20261106/e1712a4bf6e126a37880fca8a70f7ed4.jpg', 'reprice_limit' => '2000.00',  'hotnum' => 315, 'is_like' => 0, 'min_price' => 150],
            ['id' => 32, 'name' => '今晚吃鸡',        'total_num' => 1000,  'flux' => 500,   'listimg' => '/uploads/20261108/c125ef4fa80e2c6e6a3fe923af9d72b8.png', 'reprice_limit' => '2000.00',  'hotnum' => 115, 'is_like' => 0, 'min_price' => 2000],
            ['id' => 34, 'name' => '弥勒-达观祥和',   'total_num' => 44350, 'flux' => 44350, 'listimg' => '/uploads/20261109/6d82a28e5130ee9dbddbe9a5c0c31c1e.jpg', 'reprice_limit' => '50.00',    'hotnum' => 107, 'is_like' => 0, 'min_price' => 36],
        ];
    }
}
