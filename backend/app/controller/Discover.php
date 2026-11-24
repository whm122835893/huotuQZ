<?php
// 发现域接口（P037/P041/P042）—— 资讯(新闻发声)与辟谣墙，与原站路径同构
// 原站当前这两类数据均为空（list total=0，info data=null），本项目保持同构：
// 表已建、接口可用，数据为空时返回与原站一致的空结构。
namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Discover extends BaseController
{
    protected function ok($data, string $msg = 'successfully !!!')
    {
        return json(['code' => 1, 'msg' => $msg, 'time' => (string)time(), 'data' => $data]);
    }

    protected function currentUser()
    {
        return $this->auth();
    }

    // ---------- 资讯（新闻发声） ----------

    /** POST /api/discover/ant_news/list 资讯列表 */
    public function newsList()
    {
        $page     = max(1, (int)($this->request->param('page') ?? 1));
        $listRows = (int)($this->request->param('list_rows') ?? 15);
        $query    = Db::name('ant_news')->where('status', 1);
        $total    = (clone $query)->count();
        $rows     = $query->order('publish_time', 'desc')
            ->page($page, $listRows)->select()->toArray();
        return $this->ok([
            'total' => $total, 'per_page' => $listRows, 'current_page' => $page,
            'last_page' => (int)ceil($total / max(1, $listRows)), 'data' => $rows,
        ]);
    }

    /** POST /api/discover/ant_news/info 资讯详情（P037） */
    public function newsInfo()
    {
        $id  = (int)($this->request->param('id') ?? 0);
        $row = Db::name('ant_news')->where('id', $id)->where('status', 1)->find();
        return $this->ok($row ?: null);
    }

    /** POST /api/discover/ant_news/category 资讯分类 */
    public function newsCategory()
    {
        return $this->ok([]);
    }

    // ---------- 辟谣墙 ----------

    /** POST /api/discover/rumor_wall/list 辟谣墙列表 */
    public function rumorList()
    {
        $page     = max(1, (int)($this->request->param('page') ?? 1));
        $listRows = (int)($this->request->param('list_rows') ?? 15);
        $query    = Db::name('rumor_wall')->where('status', 1);
        $total    = (clone $query)->count();
        $rows     = $query->order('createtime', 'desc')
            ->page($page, $listRows)->select()->toArray();
        $rows = array_map([$this, 'decorateRumor'], $rows);
        return $this->ok([
            'total' => $total, 'per_page' => $listRows, 'current_page' => $page,
            'last_page' => (int)ceil($total / max(1, $listRows)), 'data' => $rows,
        ]);
    }

    /** POST /api/discover/rumor_wall/info 辟谣详情（P042） */
    public function rumorInfo()
    {
        $id  = (int)($this->request->param('id') ?? 0);
        $row = Db::name('rumor_wall')->where('id', $id)->where('status', 1)->find();
        return $this->ok($row ? $this->decorateRumor($row, true) : null);
    }

    /** POST /api/discover/rumor_wall/getcomment 评论列表 */
    public function rumorComments()
    {
        $rumorId  = (int)($this->request->param('rumor_id') ?? $this->request->param('id') ?? 0);
        $page     = max(1, (int)($this->request->param('page') ?? 1));
        $listRows = (int)($this->request->param('list_rows') ?? 10);
        $query    = Db::name('rumor_comment')
            ->where('rumor_id', $rumorId)->where('status', 1)->where('parent_id', 0);
        $total = (clone $query)->count();
        $rows  = $query->order('createtime', 'desc')
            ->page($page, $listRows)->select()->toArray();
        $rows = array_map([$this, 'decorateComment'], $rows);
        return $this->ok([
            'total' => $total, 'per_page' => $listRows, 'current_page' => $page,
            'last_page' => (int)ceil($total / max(1, $listRows)), 'data' => $rows,
        ]);
    }

    /** POST /api/discover/rumor_wall/getReply 评论回复列表 */
    public function rumorReplies()
    {
        $commentId = (int)($this->request->param('comment_id') ?? $this->request->param('id') ?? 0);
        $rows = Db::name('rumor_comment')
            ->where('parent_id', $commentId)->where('status', 1)
            ->order('createtime', 'asc')->select()->toArray();
        $rows = array_map([$this, 'decorateComment'], $rows);
        return $this->ok($rows);
    }

    /** POST /api/discover/rumor_wall/comment 发表评论/回复 */
    public function rumorComment()
    {
        $user = $this->currentUser();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        $content = trim((string)$this->request->param('content', ''));
        $rumorId = (int)($this->request->param('rumor_id') ?? 0);
        if ($content === '' || $rumorId <= 0) {
            return $this->fail('评论内容不能为空');
        }
        Db::name('rumor_comment')->insert([
            'rumor_id'      => $rumorId,
            'user_id'       => (int)$user['id'],
            'content'       => $content,
            'parent_id'     => (int)($this->request->param('parent_id') ?? 0),
            'reply_user_id' => (int)($this->request->param('reply_user_id') ?? 0),
            'create_time'   => date('Y-m-d H:i:s'),
            'createtime'    => time(),
            'status'        => 1,
        ]);
        return $this->ok(null, '评论成功');
    }

    /** POST /api/discover/rumor_wall/release 发表辟谣（P041） */
    public function rumorRelease()
    {
        $user = $this->currentUser();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        $content = trim((string)$this->request->param('content', ''));
        if ($content === '') {
            return $this->fail('请输入内容');
        }
        $images = $this->request->param('images', []);
        if (is_array($images)) {
            $images = implode(',', array_filter($images));
        }
        Db::name('rumor_wall')->insert([
            'user_id'      => (int)$user['id'],
            'content'      => $content,
            'images'       => (string)$images,
            'is_anonymous' => (int)($this->request->param('is_anonymous') ?? 0),
            'create_time'  => date('Y-m-d H:i:s'),
            'createtime'   => time(),
            'status'       => 1,
        ]);
        return $this->ok(null, '发表成功');
    }

    // ---------- 内部 ----------

    protected function decorateRumor(array $row, bool $withProfile = false): array
    {
        $row['id'] = (int)$row['id'];
        if ($withProfile) {
            $u = Db::name('user')->where('id', (int)$row['user_id'])->find();
            $anonymous = (int)($row['is_anonymous'] ?? 0) === 1;
            $row['profile'] = [
                'avatar'   => $anonymous ? '' : (string)($u['avatar'] ?? ''),
                'nickname' => $anonymous ? '匿名用户' : (string)($u['nickname'] ?? ''),
            ];
        }
        return $row;
    }

    protected function decorateComment(array $row): array
    {
        $u = Db::name('user')->where('id', (int)$row['user_id'])->find();
        $row['id'] = (int)$row['id'];
        $row['profile'] = [
            'avatar'   => (string)($u['avatar'] ?? ''),
            'nickname' => (string)($u['nickname'] ?? ''),
        ];
        return $row;
    }
}
