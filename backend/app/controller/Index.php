<?php
// 首页域接口 —— 响应结构与原站同构，数据全部来自本项目 MySQL
namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Index extends BaseController
{
    /** 统一成功返回 */
    protected function ok($data, string $msg = 'ok')
    {
        return json(['code' => 1, 'msg' => $msg, 'time' => (string)time(), 'data' => $data]);
    }

    /** POST /api/banner/show/getBanner 轮播图 */
    public function banner()
    {
        $rows = Db::name('banner')
            ->where('is_show', 1)
            ->order('id', 'desc')
            ->field('id,image,url,url_type,is_show')
            ->select()->toArray();
        return $this->ok([
            'total' => count($rows), 'per_page' => 15, 'current_page' => 1, 'last_page' => 1,
            'data' => $rows,
        ]);
    }

    /** POST /api/index/afficheList 公告列表（发现页：分页+分类筛选） */
    public function afficheList()
    {
        $page      = max(1, (int)($this->request->param('page') ?? 1));
        $listRows  = (int)($this->request->param('list_rows') ?? 15);
        $category  = $this->request->param('category');
        $search    = trim((string)($this->request->param('search') ?? ''));

        $query = Db::name('affiche')->where('is_show', 1);
        if ($search !== '') {
            // 搜索页（P040）：标题+正文模糊匹配（与原站行为对齐：正文命中也会返回）
            $query = $query->where(function ($q) use ($search) {
                $q->whereOr('title', 'like', '%' . $search . '%')
                  ->whereOr('content', 'like', '%' . $search . '%');
            });
        }
        if ($category !== null && $category !== '' && (int)$category > 0) {
            // category = affichetypelist 的 id（1官方 2运营 3活动 4寄售 5空投 6求购 7限价 8合成）
            $typeMap = Db::name('affiche_type')->column('name', 'id');
            $name = $typeMap[(int)$category] ?? null;
            if (!$name) {
                return $this->ok(['total' => 0, 'per_page' => $listRows, 'current_page' => $page, 'last_page' => 0, 'data' => []]);
            }
            $query = $query->where('affiche_type', $name);
        }

        $total = (clone $query)->count();
        $rows = $query->order('createtime', 'desc')
            ->field('id,title,createtime,type,url,affiche_type')
            ->page($page, $listRows)
            ->select()->toArray();
        return $this->ok([
            'total' => $total, 'per_page' => $listRows, 'current_page' => $page,
            'last_page' => (int)ceil($total / max(1, $listRows)),
            'data' => $rows,
        ]);
    }

    /** POST /api/index/affichetypelist 公告分类列表 */
    public function affichetypelist()
    {
        $rows = Db::name('affiche_type')->order('id', 'asc')->field('id,name')->select()->toArray();
        return $this->ok($rows);
    }

    /** POST /api/index/afficheDetail/ids/{id} 公告详情（P039） */
    public function afficheDetail($id = 0)
    {
        $row = Db::name('affiche')->where('id', (int)$id)->find();
        if (!$row) {
            return $this->ok(null, '详情');
        }
        return $this->ok([
            'id'              => (int)$row['id'],
            'collection_ids'  => (string)($row['collection_ids'] ?? ''),
            'title'           => (string)$row['title'],
            'describe'        => (string)($row['describe'] ?? ''),
            'content'         => (string)($row['content'] ?? ''),
            'type'            => (string)($row['type'] ?? '1'),
            'image'           => (string)($row['image'] ?? ''),
            'createtime'      => (int)$row['createtime'],
            'updatetime'      => (int)($row['updatetime'] ?? 0),
            'url'             => (string)($row['url'] ?? ''),
            'read'            => (int)($row['read'] ?? 0),
            'sort'            => (int)($row['sort'] ?? 0),
            'publish_time'    => (int)($row['publish_time'] ?? 0),
            'status'          => (int)($row['status'] ?? 1),
            'top'             => (int)($row['top'] ?? 0),
            'affiche_type_id' => (int)($row['affiche_type_id'] ?? 0),
            'image_type'      => null,
            'is_wxpusher'     => (int)($row['is_wxpusher'] ?? 2),
            'collection'      => [],
        ], '详情');
    }

    /** POST /api/index/getSeries 类型下拉（推荐/我的关注/全部） */
    public function getSeries()
    {
        $type = (int)($this->request->param('type') ?? 10);
        if ($type === 20) {
            // 盲盒系列（与原站同构）
            $list = [
                ['series_name' => '盲盒系列', 'series_id' => 3, 'is_def' => 0, 'flag' => 0, 'pattern' => 1,
                 'resale_put_start_time' => '02:15:44', 'resale_put_end_time' => '02:15:44',
                 'resale_buy_start_time' => '02:15:44', 'resale_buy_end_time' => '02:15:44'],
            ];
            return $this->ok($list);
        }
        $list = [
            ['series_name' => '推荐', 'series_id' => -1, 'is_def' => 0, 'flag' => 0, 'pattern' => 1,
             'resale_put_start_time' => '00:00:00', 'resale_put_end_time' => '00:00:00',
             'resale_buy_start_time' => '00:00:00', 'resale_buy_end_time' => '00:00:00'],
            ['series_name' => '我的关注', 'series_id' => -2, 'is_def' => 0, 'flag' => 0, 'pattern' => 1,
             'resale_put_start_time' => '00:00:00', 'resale_put_end_time' => '00:00:00',
             'resale_buy_start_time' => '00:00:00', 'resale_buy_end_time' => '00:00:00'],
            ['series_name' => '全部', 'series_id' => 0, 'is_def' => 1, 'flag' => 0, 'pattern' => 1,
             'resale_put_start_time' => '00:00:00', 'resale_put_end_time' => '00:00:00',
             'resale_buy_start_time' => '00:00:00', 'resale_buy_end_time' => '00:00:00'],
        ];
        return $this->ok($list);
    }

    /** POST /api/box/blind_box/list 发售日历（首页商品列表） */
    public function blindBoxList()
    {
        $page = max(1, (int)($this->request->param('page') ?? 1));
        $rows = (int)($this->request->param('list_rows') ?? 10);
        $status = $this->request->param('sale_status'); // 可选筛选

        $q = Db::name('goods')->where('is_show', 1);
        if ($status !== null && $status !== '') {
            $q->where('sale_status', (int)$status);
        }
        $list = $q->order('release_time', 'desc')
            ->page($page, $rows)->select()->toArray();

        // 批次号以 product 快照表为权威来源，与详情页 ProductDetail 的 batch_id 校验同源。
        // 避免硬编码 1 导致首页点击卡片跳详情时因批次不符被 back() 回退（表现为"点不进去"）。
        $batchMap = [];
        try {
            foreach (Db::name('product')->field('id,batch_id')->select() as $p) {
                $batchMap[(int)$p['id']] = (int)$p['batch_id'];
            }
        } catch (\Throwable $e) { /* 表缺失时回退 1 */ }

        $data = array_map(function ($g) use ($batchMap) {
            $gid = (int)$g['id'];
            return [
                'id' => $gid,
                'name' => $g['name'],
                'listimg' => $g['listimg'],
                'price' => $g['price'],
                'main_image' => $g['listimg'],
                'total_num' => (int)$g['total_num'],
                'release_time' => (int)$g['release_time'],
                'end_time' => (int)$g['end_time'],
                'total_num_batch' => (int)$g['total_num'],
                'release_date_time_text' => date('Y-m-d H:i', (int)$g['release_time']),
                'createtime' => (int)$g['createtime'],
                'type' => $g['type'],
                'goods_type' => $g['goods_type'],
                'batch_id' => $batchMap[$gid] ?? 1,
                'soldout' => 0,
                'fictitious_stock' => 0,
                'open_draw' => 0,
                'issuer_id' => 0,
                'flux' => (int)$g['flux'],
                'total_num_show' => 0,
                'batch' => [
                    'start_time' => (int)$g['release_time'],
                    'end_time' => (int)$g['end_time'],
                    'total_num' => (int)$g['total_num'],
                    'has_num' => 1,
                ],
                'publisher' => [
                    'name' => $g['publisher_name'],
                    'company_logo' => $g['publisher_logo'],
                ],
                'sale_status' => (int)$g['sale_status'],
                'sale_status_text' => (int)$g['sale_status'] === 1 ? 3 : 4,
                'release_time_date_text' => date('Y-m-d H:i:s', (int)$g['release_time']),
            ];
        }, $list);

        return $this->ok($data, '发售日历');
    }

    /** POST /api/market/market/marketrebang 热榜 */
    public function marketrebang()
    {
        $rows = Db::name('goods')->where('is_show', 1)
            ->order('hotnum', 'desc')->limit(20)->select()->toArray();
        $data = array_map(function ($g) {
            return [
                'id' => (int)$g['id'],
                'name' => $g['name'],
                'total_num' => (int)$g['total_num'],
                'flux' => (int)$g['flux'],
                'listimg' => $g['listimg'],
                'reprice_limit' => $g['reprice_limit'],
                'hotnum' => (int)$g['hotnum'],
                'is_like' => 0,
                'min_price' => (float)$g['min_price'],
            ];
        }, $rows);
        return $this->ok($data);
    }

    /** POST /api/user/newrank1 购入值排行（名人堂） */
    public function newrank1()
    {
        // 购入值排行（与原站 newrank1 结构同构）：rank2=购入值榜（市场-排行展示），rank1=对应第二榜
        $avatar = '/defautAvatar.png';
        $build = function (int $type) use ($avatar) {
            $rows = Db::name('rank')->where('rank_type', $type)->order('pos', 'asc')->select()->toArray();
            return array_map(function ($r, $i) use ($avatar) {
                return [
                    'id'            => $i + 1,
                    'price'         => (int)$r['price'],
                    'nickname'      => $r['nickname'],
                    'avatar'        => $avatar,
                    'user_avatar'   => null,
                    'user_nickname' => null,
                    'listimg'       => $avatar,
                ];
            }, $rows, array_keys($rows));
        };
        $rank1 = $build(1);
        $rank2 = $build(2);
        $headUser = [];
        for ($i = 0; $i < 20; $i++) {
            $headUser[] = ['id' => 100000 + $i, 'avatar' => $avatar, 'headimg' => $avatar];
        }
        $user = $this->auth();
        $myId = $user ? (int)$user['id'] : 0;
        return json([
            'code' => 1,
            'msg'  => '成功',
            'time' => (string)time(),
            'data' => [
                'headUser' => $headUser,
                'list'     => [],
                'rank1'    => $rank1,
                'rank2'    => $rank2,
                'my1'      => ['id' => $myId, 'myrank' => '未上榜', 'last' => 925, 'price' => 0],
                'my2'      => ['id' => $myId, 'myrank' => '未上榜', 'last' => 3992, 'price' => 0],
            ],
        ]);
    }

    /** POST /api/index/getConfig 全局配置 */
    public function getConfig()
    {
        return $this->ok([
            'sitename' => '火兔',
            'logo' => '/uploads/20240118/17b679f0562f639f96344f08e59e0729.jpg',
        ]);
    }
}
