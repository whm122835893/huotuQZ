<?php
// +----------------------------------------------------------------------
// | 钱包控制器 —— 批次14（P082）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// | 原站 chunk：pages-wallet-wallet.35f56741.js（模块 f410 / 6fe5）
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Wallet extends BaseController
{
    /**
     * POST /api/wallet/huifu/wallet
     * 原站 getHuiFuInfo()，已开户时由 toWalletPage() 调用。
     * 实测：code:1「请求成功」+ data{openstatus:true, info{redirect_url,...}}
     */
    public function walletInfo()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $row = Db::name('wallet_huifu')->where('user_id', (int)$user['id'])->find();
        if (!$row) return $this->fail('尚未开户');

        return $this->ok([
            'openstatus' => (bool)((int)$row['openstatus']),
            'info'       => [
                'sys_id'         => (string)$row['sys_id'],
                'extension'      => (string)$row['extension'],
                'acct_id'        => (string)$row['acct_id'],
                'resp_desc'      => (string)$row['resp_desc'],
                'resp_code'      => (string)$row['resp_code'],
                'mer_priv'       => (string)$row['mer_priv'],
                'user_cust_id'   => (string)$row['user_cust_id'],
                'redirect_url'   => (string)$row['redirect_url'],
                'mer_cust_id'    => (string)$row['mer_cust_id'],
                'biz_product_id' => (string)$row['biz_product_id'],
            ],
        ], '请求成功');
    }

    /**
     * POST /api/wallet/huifu/openAccount
     * 原站 openHuiFu()，未开户时由 openHuiFuWallet() 调用。
     * 实测：该账号已开户 -> code:0「您已经开户了」data:null（如实复刻，不臆造成功路径）。
     */
    public function openAccount()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        if ((int)($user['huifu_account'] ?? 0) === 1) {
            return $this->fail('您已经开户了');
        }
        return $this->fail('对接中');
    }

    /**
     * POST /api/order/pay/CreateHuifuAccountOrder
     * 原站 CreateHuifuAccountOrder()，actionServicePop() 服务费确认后调用。
     * 实测：code:0「您已经支付开户手续费，请勿重复操作」data:null（如实复刻）。
     */
    public function createAccountOrder()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        return $this->fail('您已经支付开户手续费，请勿重复操作');
    }

    /**
     * POST /api/user/getWalletInfo
     * 原站 getWalletInfo()。实测：code:0「尚未开户」data:null。
     */
    public function getWalletInfo()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        return $this->fail('尚未开户');
    }
}
