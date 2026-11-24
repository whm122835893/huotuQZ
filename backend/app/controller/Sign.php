<?php
// +----------------------------------------------------------------------
// | 签到控制器 —— 批次15（P083）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// | 原站 chunk：pages-sign-index.07b21d92.js（页面模块 9b1b / 接口层模块 5f3a）
// | 原站接口层：getSignConfig -> /addons/signin/api.index/index（needBaseUrl:false）
// |             getSignRecord -> /addons/signin/api.index/monthSign
// |             sign         -> /addons/signin/api.index/dosign
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Sign extends BaseController
{
    /** 读取侦查时刻的原站快照（顶层 time 与 data 原样保存） */
    private function snapshot()
    {
        $row = Db::name('sign_config')->where('id', 1)->find();
        if (!$row) return null;
        return [
            'time' => (int)$row['snapshot_time'],
            'data' => json_decode($row['config_json'], true),
        ];
    }

    /** 顶层响应：原站 msg 为空串、time 为快照时间戳，逐字对齐 */
    private function raw($data, $time)
    {
        return json([
            'code' => 1,
            'msg'  => '',
            'time' => (string)$time,
            'data' => $data,
        ]);
    }

    /**
     * POST /addons/signin/api.index/index
     * 原站 getSignConfig()。实测 code:1 / msg:"" / data{signinscore,isfillup,
     * fillupscore,fillupdays,ranklist(10),successions:0,is_signin:false,self_rank:0,
     * score:3,msg:"今天签到可获得 1 积分,请点击签到领取积分"}。
     */
    public function indexConfig()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $s = $this->snapshot();
        if (!$s) return $this->fail('获取签到记录失败');
        return $this->raw($s['data'], $s['time']);
    }

    /**
     * POST /addons/signin/api.index/monthSign
     * 原站 getSignRecord({month})。实测 data:{"04":"04","05":"05"}；
     * 传 month=2026-10 亦返回同一份 data（原站实测行为，本项目如实复刻）。
     */
    public function monthSign()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $s = $this->snapshot();
        if (!$s) return $this->fail('获取签到记录失败');

        $row = Db::name('sign_month')->where('month', '2026-11')->find();
        $days = $row ? json_decode($row['days_json'], true) : [];
        return $this->raw($days, $s['time']);
    }

    /**
     * POST /addons/signin/api.index/dosign
     * 原站 sign()。chunk onSign() 仅判断 code==1 -> successVisible + loadPageData()，
     * 失败时 toast(t.msg||'签到失败')。
     * 侦查说明：未对原站账号执行真实签到（避免污染账号状态、影响用户逐批验收时的
     * 原站/Clone 对照），故按 chunk 契约返回等价 code:1；页面状态在前端内存更新，
     * 刷新后回到侦查快照态，DB 快照永不改写。
     */
    public function doSign()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->ok([], '签到成功');
    }
}
