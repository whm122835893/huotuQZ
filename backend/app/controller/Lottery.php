<?php
// +----------------------------------------------------------------------
// | 抽奖控制器 —— 批次3 占位落地页（P092 抽奖活动）配套接口
// | 响应结构与原站 api.hnyiqin.asia 同构，零原站运行时依赖。
// | 侦查实测（2026-11-15，页面内 fetch，路由 #/pages/lottery/lottery?id=2）：
// |   POST /api/lottery/info        {id:"2"}                              -> code:0「暂无抽奖活动」data:null
// |   POST /api/lottery/statistics  {lottery_id:"2"}                      -> code:1「successfully !!!」data:[]
// |   POST /api/lottery/record      {lottery_id:"2",type:10,page:1,list_rows:15}
// |        -> code:1「successfully !!!」data{total:0,per_page:15,current_page:1,last_page:0,data:[]}
// | 三接口均为「当前无活动」空态，页面显示「暂无抽奖活动」+ 粉色空箱插画。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;

class Lottery extends BaseController
{
    private function okRaw($data, string $msg = 'successfully !!!')
    {
        return json([
            'code' => 1,
            'msg'  => $msg,
            'time' => (string)time(),
            'data' => $data,
        ]);
    }

    /** POST /api/lottery/info {id} —— 当前无活动 */
    public function info()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return json([
            'code' => 0,
            'msg'  => '暂无抽奖活动',
            'time' => (string)time(),
            'data' => null,
        ]);
    }

    /** POST /api/lottery/statistics {lottery_id} —— 空统计 */
    public function statistics()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        return $this->okRaw([]);
    }

    /** POST /api/lottery/record {lottery_id,type,page,list_rows} —— 空分页 */
    public function record()
    {
        $user = $this->auth();
        if (!$user) return $this->fail('请先登录', 401);
        $listRows = (int)$this->request->param('list_rows', 15);
        if ($listRows <= 0) $listRows = 15;
        return $this->okRaw([
            'total'        => 0,
            'per_page'     => $listRows,
            'current_page' => 1,
            'last_page'    => 0,
            'data'         => [],
        ]);
    }
}
