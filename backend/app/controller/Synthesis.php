<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use think\facade\Db;

/**
 * 合成域接口（批次6 · P043-P046）
 * 对齐原站 /api/synthesis/* —— themeList / themeinfo / playInfo / material /
 * selectCollection / synthesizeNow / recordList / recordInfo
 */
class Synthesis extends BaseController
{
    /** POST /api/synthesis/themeList 合成主题列表（含玩法） */
    public function themeList()
    {
        $themes = Db::name('synthesis_theme')->where('status', 1)->order('id', 'asc')->select()->toArray();
        $plays  = Db::name('synthesis_play')->where('status', 1)->order('id', 'asc')->select()->toArray();

        $out = [];
        foreach ($themes as $t) {
            $play = [];
            foreach ($plays as $p) {
                if ((int)$p['theme_id'] === (int)$t['id']) {
                    $play[] = $this->formatPlay($p);
                }
            }
            $out[] = [
                'id'        => (int)$t['id'],
                'types'     => (int)$t['types'],
                'name'      => $t['name'],
                'image'     => $t['image'],
                'rule_text' => $t['rule_text'],
                'play'      => $play,
            ];
        }
        return $this->ok($out);
    }

    /** POST /api/synthesis/themeinfo 主题详情（thm 参数） */
    public function themeinfo()
    {
        $thm = (int)($this->request->param('thm') ?? 0);
        if ($thm <= 0) {
            return $this->fail('未开启', 0);
        }
        $theme = Db::name('synthesis_theme')->where('status', 1)->where('id', $thm)->find();
        if (!$theme) {
            return $this->fail('未开启', 0);
        }
        $plays = Db::name('synthesis_play')->where('status', 1)->where('theme_id', $thm)->order('id', 'asc')->select()->toArray();
        $theme['play'] = array_map([$this, 'formatPlay'], $plays);
        return $this->ok($theme);
    }

    /** POST /api/synthesis/playInfo 玩法详情（合成详情页 P046） */
    public function playInfo()
    {
        $playId = (int)($this->request->param('play_id') ?? 0);
        $play = Db::name('synthesis_play')->where('id', $playId)->where('status', 1)->find();
        if (!$play) {
            return $this->fail('玩法不存在', 0);
        }
        $now = (int)(microtime(true) * 1000);
        if ((int)$play['real_end_time'] > 0 && $now > (int)$play['real_end_time']) {
            return $this->fail('活动已结束', 0);
        }
        if ((int)$play['real_start_time'] > 0 && $now < (int)$play['real_start_time']) {
            return $this->fail('活动未开始', 0);
        }
        $play = $this->formatPlay($play);
        $play['rule_text'] = []; // 材料方案列表（活动进行中由后台配置，当前与原站一致为空）
        return $this->ok($play);
    }

    /** POST /api/synthesis/material 活动材料（进行中点击 GO 调用） */
    public function material()
    {
        $userId  = $this->auth();
        $playId  = (int)($this->request->param('play_id') ?? 0);
        $play = Db::name('synthesis_play')->where('id', $playId)->where('status', 1)->find();
        if (!$play) {
            return $this->fail('玩法不存在', 0);
        }
        $now = (int)(microtime(true) * 1000);
        $inProgress = $now >= (int)$play['real_start_time'] && $now <= (int)$play['real_end_time'];
        if (!$inProgress) {
            // 与原站一致：材料校验不通过返回 3003，前端跳转合成详情页
            return $this->fail('请前往合成详情页', 3003);
        }
        // 未获得合成资格（无合成材料持有记录）—— 与原站 4003 行为对齐
        return $this->fail('您未获得合成资格', 4003);
    }

    /** POST /api/synthesis/selectCollection 选择合成材料时的用户藏品列表 */
    public function selectCollection()
    {
        $this->auth();
        // 克隆站暂无用户藏品域：返回空列表（与活动结束态行为一致）
        return $this->ok([]);
    }

    /** POST /api/synthesis/synthesizeNow 立即合成 */
    public function synthesizeNow()
    {
        $this->auth();
        return $this->fail('合成材料不足', 0);
    }

    /** POST /api/synthesis/recordList 合成记录列表（P044 活动记录） */
    public function recordList()
    {
        $userId  = $this->auth();
        $page    = max(1, (int)($this->request->param('page') ?? 1));
        $listRows = (int)($this->request->param('list_rows') ?? 15);

        $query = Db::name('synthesis_record')->where('user_id', $userId)->where('status', 1);
        $total = (clone $query)->count();
        $rows  = $query->order('id', 'desc')
            ->page($page, $listRows)
            ->select()->toArray();

        $lastPage = (int)ceil($total / max(1, $listRows));
        return $this->ok([
            'total'        => $total,
            'per_page'     => $listRows,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'data'         => array_map([$this, 'formatRecord'], $rows),
        ]);
    }

    /** POST /api/synthesis/recordInfo 合成记录详情（P045 参与结果） */
    public function recordInfo()
    {
        $userId   = $this->auth();
        $recordId = (int)($this->request->param('record_id') ?? 0);
        $rec = Db::name('synthesis_record')
            ->where('id', $recordId)->where('user_id', $userId)->where('status', 1)->find();
        if (!$rec) {
            return $this->fail('记录不存在', 0);
        }
        $result = json_decode((string)$rec['result'], true);
        return $this->ok(['result' => is_array($result) ? $result : []]);
    }

    /** 玩法字段格式化（与原站 themeList/playInfo 结构一致） */
    private function formatPlay(array $p): array
    {
        return [
            'id'              => (int)$p['id'],
            'title'           => $p['title'],
            'image'           => $p['image'],
            'start_time'      => (int)$p['start_time'],
            'end_time'        => (int)$p['end_time'],
            'activity_id'     => (int)$p['activity_id'],
            'description'     => $p['description'],
            'is_auto'         => (int)$p['is_auto'],
            'is_delay'        => (int)$p['is_delay'],
            'is_advance'      => (int)$p['is_advance'],
            'advance_date'    => (int)$p['advance_date'],
            'delay_date'      => (int)$p['delay_date'],
            'real_start_time' => (int)$p['real_start_time'],
            'real_end_time'   => (int)$p['real_end_time'],
            'stock'           => $p['stock'] === null ? null : (int)$p['stock'],
            'classify'        => $p['classify'],
            'type'            => $p['type'],
        ];
    }

    /** 记录字段格式化（P044 列表项 / 材料详情弹窗） */
    private function formatRecord(array $r): array
    {
        $consume = json_decode((string)$r['consume'], true);
        return [
            'id'          => (int)$r['id'],
            'play_id'     => (int)$r['play_id'],
            'consume'     => is_array($consume) ? $consume : [],
            'method'      => (int)$r['method'],
            'create_time' => $r['create_time'],
        ];
    }
}
