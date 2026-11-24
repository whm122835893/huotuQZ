<?php
// +----------------------------------------------------------------------
// | 抽奖活动控制器 —— 批次20（P095 分享助力 / P096 中签结果 / P097 邀请好友助力 /
// |   P098 参与人数 / P099 分享社群）
// | 原站 chunk：pages-draw-shareCommunityGroup-shareCommunityGroup.fe090c11.js
// | 侦查实测（2026-11-16，GET + header token）：
// |   GET /api/score/get_my_share_details?type=1|2
// |     -> HTTP/2 404，content-type: text/html，content-length: 0（空响应体）
// |     -> 原站前端 axios/fetch catch 后积分默认 0，页面静态渲染
// | 本项目如实复刻该 404 空体响应（HTTP 状态码与响应体均逐字对齐，不臆造积分数据）。
// | P095/P097/P098 计数全部来自页面参数/localStorage，直接访问恒 0，无接口调用；
// | P096 onLoad JSON.parse(undefined) 抛异常 -> 永久加载态，无接口调用。
// | 故本控制器仅承载 P099 的唯一接口，其余四页零端点（无产物不设席位）。
// |
// | 批次21（P100 抽签 / P101 中签记录）追加两个同域端点：
// |   GET  /api/draw/get      -> {"code":0,"msg":"活动不存在","data":null}
// |        （原站无活动参数直访真实态；P100 组件据此渲染空 → 纯白页）
// |   POST /api/draw/drawLog  -> {"code":1,"msg":"获取成功","data":[]}
// |        （原站中签记录空态；P101 据此渲染开箱空态图 + 提示文字）
// | 均为静态真实态快照，与页面一一对应，不臆造活动/中签数据。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;

class Draw extends BaseController
{
    /**
     * GET /api/score/get_my_share_details?type=1助力|2积分 —— 原站 404 空体。
     * 与批次17 返佣接口（HTTP 200 + body code 404 JSON）不同：本接口原站为
     * HTTP 404 + 空 body（text/html），故这里同样返回 404 状态码 + 空体，
     * 前端 axios 走 error 分支 catch，行为与原站逐帧一致。
     * 注意：直接 http_response_code + exit，绕过框架 debug trace 附加
     * （APP_DEBUG=true 时 response('') 会被追加 22KB trace 页，与原站空体不符）。
     */
    public function getMyShareDetails()
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Length: 0');
        exit;
    }

    /**
     * GET /api/draw/get —— 抽签活动详情。
     * 原站无活动参数直访返回 {"code":0,"msg":"活动不存在","data":null}，
     * P100 组件渲染时抛异常 → 纯白页。如实复刻该业务态。
     */
    public function get()
    {
        return $this->fail('活动不存在', 0);
    }

    /**
     * POST /api/draw/drawLog —— 中签记录列表。
     * 原站返回 {"code":1,"msg":"获取成功","data":[]}（当前账号无中签记录），
     * P101 据此渲染开箱空态图 +「目前没有任何数据」。如实复刻空数组。
     */
    public function drawLog()
    {
        return json(['code' => 1, 'msg' => '获取成功', 'time' => (string)time(), 'data' => []]);
    }
}
