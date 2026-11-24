<?php
// +----------------------------------------------------------------------
// | 活动精选控制器 —— 批次22（P102 /pages/activeSelected/activeSelected）
// | 原站 chunk：pages-activeSelected-activeSelected.165f52fe.js
// | 侦查实测（2026-11-16，POST + header token）：
// |   POST /api/Activity/category -> {"code":1,"msg":"分类列表","data":[]}
// |   POST /api/activity/index    -> {"code":1,"msg":"请求成功",
// |                                  "data":{"total":0,"per_page":10,"current_page":1,
// |                                          "last_page":0,"data":[]}}
// | 当前账号两个分类下均无活动，P102 渲染开箱空态 +「目前没有任何数据」。
// | 本项目如实复刻两个静态真实态响应（逐字对齐原站 code/msg/data 结构，不臆造活动数据）。
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;

class Activity extends BaseController
{
    /**
     * POST /api/Activity/category —— 活动分类列表。
     * 原站返回 data:[]（当前无自定义分类）。
     */
    public function category()
    {
        return json([
            'code' => 1,
            'msg' => '分类列表',
            'time' => (string)time(),
            'data' => [],
        ]);
    }

    /**
     * POST /api/activity/index —— 活动列表（按分类分页）。
     * 原站返回 total:0 / data:[]（无活动）。
     */
    public function index()
    {
        return json([
            'code' => 1,
            'msg' => '请求成功',
            'time' => (string)time(),
            'data' => [
                'total' => 0,
                'per_page' => 10,
                'current_page' => 1,
                'last_page' => 0,
                'data' => [],
            ],
        ]);
    }
}
