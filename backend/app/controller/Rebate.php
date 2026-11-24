<?php
// +----------------------------------------------------------------------
// | 返佣控制器 —— 批次17（P086 排行榜 / P087 返佣主页 / P088 被邀请人清单）
// | 响应结构与原站 api.hnyiqin.asia 同构，零原站运行时依赖。
// | 原站 chunk：pages-rebate-rebate.90a45f76.js（接口层模块 5f3a）
// | 侦查实测（2026-11-15，header token + version:1.0.1，页面内 fetch）：
// |   POST /api/user/commison_rank  {types:1|2} -> HTTP 404
// |        {"code":404,"msg":"An error occurred","time":1794681284,"data":null}
// |   POST /api/user/commision_info {}          -> HTTP 404 同构
// |   POST /api/user/bei_yao_list   {page:1}    -> HTTP 404 同构
// | 三个接口原站均异常，故三页真实态为：排行榜空态 / 返佣主页白屏 / 被邀请人清单空态。
// | 本项目如实复刻该异常响应（不臆造榜单/返佣数据）；接口正常时的完整模板见前端
// | pages/rebate/rebate.vue ?demo=1 注入态（chunk 逐字文案），遵守「不臆造」硬规则。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;

class Rebate extends BaseController
{
    /**
     * 原站 404 响应同构返回。顶层 time 为 int（与批次15 字符串 time 不同，逐字对齐）。
     * HTTP 状态码保持 200（本地前端 axios 拦截器按 body.code 判定；原站 404 为
     * 其服务端异常，非本项目契约要求，如实登记在 API 文档「边界说明」）。
     */
    private function errorOccurred()
    {
        return json([
            'code' => 404,
            'msg'  => 'An error occurred',
            'time' => 1794681284,
            'data' => null,
        ]);
    }

    /** POST /api/user/commison_rank {types:1日/2周/3月} —— 原站 404，排行榜空态 */
    public function commisonRank()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->errorOccurred();
    }

    /** POST /api/user/commision_info {} —— 原站 404，返佣主页 loading 恒 true 白屏 */
    public function commisionInfo()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->errorOccurred();
    }

    /** POST /api/user/bei_yao_list {page:1} —— 原站 404，被邀请人清单空态 */
    public function beiYaoList()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->errorOccurred();
    }
}
