<?php
// +----------------------------------------------------------------------
// | 邀请控制器 —— 批次16（P084 邀请活动 / P085 邀请记录）
// | 响应结构与原站 api.hnyiqin.asia 同构，数据全部来自本项目 MySQL，零原站运行时依赖。
// | 原站 chunk：pages-invitation-invitation.243bf971.js / invitationrecord.2d8b2c65.js
// | 原站接口层：getRankingList -> /api/user/ranking（types:2）
// |             inviteByCode  -> /api/user/invite（inviter_code）
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Invitation extends BaseController
{
    /**
     * POST /api/user/ranking {types:2}
     * 原站 getRankingList()。实测 code:1 / msg:"查询成功" /
     * data{data{my_list:[],vip_time_num:0,real_name_num:0,yx_num:0,total_num:0,buy_num:0},my:"",activityRule:""}。
     * 前端 inviteTotalCount / displayTotalNum 取 data.data.total_num（实测 0），
     * 好友列表取 data.data.my_list（实测空数组 -> 空态图）。
     */
    public function ranking()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $row = Db::name('invitation_ranking')->where('id', 1)->find();
        if (!$row) return $this->fail('查询失败');

        $data = json_decode($row['data_json'], true);
        return json([
            'code' => 1,
            'msg'  => '查询成功',
            'time' => (string)$row['snapshot_time'],
            'data' => $data,
        ]);
    }

    /**
     * POST /api/user/invite {inviter_code}
     * 原站 inviteByCode()。chunk goAssist() 仅取 res.msg 做 toast，
     * 然后清空输入并 getList()。
     * 安全探测说明：用无效邀请码实测原站返回 code:0「活动未开启」，
     * 未对原站账号建立真实邀请关系（避免污染账号状态、影响用户逐批验收时的
     * 原站/Clone 对照）。本项目如实复刻该响应，不臆造成功路径。
     */
    public function invite()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);

        $code = trim((string)$this->request->param('inviter_code', ''));
        if ($code === '') {
            return json(['code' => 0, 'msg' => '请输入邀请码', 'time' => (string)time(), 'data' => null]);
        }
        return json(['code' => 0, 'msg' => '活动未开启', 'time' => (string)time(), 'data' => null]);
    }
}
