<?php
// +----------------------------------------------------------------------
// | 转赠控制器 —— 批次13（P080-P081）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Regift extends BaseController
{
    /**
     * POST /api/user/aboutGift
     * {page, types}  types: 1 我转赠的 / 2 我收到的
     * P080 转赠记录。原站该账号两个 Tab 均 total=0，本项目按 ht_gift_record 真实数据分页返回。
     */
    public function aboutGift()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $types    = (int)$this->request->param('types', 1);
        $page     = max(1, (int)$this->request->param('page', 1));
        $listRows = 20;

        $q = Db::name('gift_record')
            ->where('user_id', (int)$user['id'])
            ->where('types', $types);
        $total = (clone $q)->count();
        $rows  = $q->order('createtime', 'desc')->page($page, $listRows)->select()->toArray();

        $list = [];
        foreach ($rows as $r) {
            $product = Db::name('collection')->where('id', (int)$r['collection_id'])->find();
            $list[] = [
                'id'            => (int)$r['id'],
                'createtime'    => (int)$r['createtime'],
                'product'       => [
                    'name'      => (string)($product['name'] ?? ''),
                    'listimg'   => (string)($product['listimg'] ?? ''),
                    'total_num' => (int)($product['total_num'] ?? 0),
                ],
                'usercollection' => [
                    'types'           => 2,
                    'collection_code' => (string)$r['collection_code'],
                ],
                'user'      => ['mobile' => (string)$r['user_mobile']],
                'from_user' => ['mobile' => (string)$r['from_user_mobile']],
            ];
        }

        return $this->ok([
            'total'        => $total,
            'per_page'     => (string)$listRows,
            'current_page' => $page,
            'last_page'    => $total > 0 ? (int)ceil($total / $listRows) : 0,
            'data'         => $list,
        ], '转赠记录');
    }

    /**
     * POST /api/user/ParamsToUser
     * {phone}  支持手机号或钱包地址(本地按手机号/用户名匹配)
     * P081 选择用户。原站返回 data 为单个用户对象；查无此人时 data 为 null，前端 toast「用户不存在」。
     */
    public function paramsToUser()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $phone = trim((string)$this->request->param('phone', ''));
        if ($phone === '') return $this->ok(null, '用户列表');

        $row = Db::name('user')
            ->where('mobile', $phone)
            ->whereOr('username', $phone)
            ->find();
        if (!$row) return $this->ok(null, '用户列表');

        $mobile = (string)$row['mobile'];
        $masked = strlen($mobile) >= 7 ? substr($mobile, 0, 6) . '*****' : $mobile;

        return $this->ok([
            'id'       => (int)$row['id'],
            'nickname' => (string)$row['nickname'],
            'avatar'   => (string)($row['avatar'] ?: '/defautAvatar.png'),
            'mobile'   => $masked,
            'ntf_url'  => null,
            'nft_url'  => null,
            'url'      => '/u/' . $row['id'],
        ], '用户列表');
    }

    /**
     * POST /api/user_collection/user_collection/reGiftOrder
     * {pay_password, re_user_id, user_collection_id}
     * P081 确认转赠。原站实测该账号输入任意操作密码均返回 code:0「密码错误」；
     * 本项目按 ht_user.pay_password 真实校验（当前账号未设置，故同样返回密码错误），不臆造成功路径。
     */
    public function reGiftOrder()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $pwd    = (string)$this->request->param('pay_password', '');
        $stored = (string)($user['pay_password'] ?? '');

        if ($stored === '' || $pwd === '' || !hash_equals($stored, $pwd)) {
            return $this->fail('密码错误');
        }
        return $this->fail('转赠失败，请稍后重试');
    }
}
