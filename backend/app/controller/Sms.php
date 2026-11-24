<?php
// +----------------------------------------------------------------------
// | 短信验证码 —— 独立实现（沙箱无真实短信通道，开发模式直接回传验证码用于测试）
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Sms extends BaseController
{
    /** 验证码有效期（秒） */
    const TTL = 300;

    /**
     * POST /api/sms/send  {mobile, event}
     * event: register / login / resetpwd
     */
    public function send()
    {
        $mobile = trim((string)$this->request->param('mobile', ''));
        $event  = trim((string)$this->request->param('event', ''));
        if (!preg_match('/^1\d{10}$/', $mobile)) {
            return $this->fail('请输入正确的手机号');
        }
        if (!in_array($event, ['register', 'login', 'resetpwd'])) {
            return $this->fail('不支持的验证类型');
        }

        // 60 秒内不可重发
        $last = Db::name('sms_log')->where('mobile', $mobile)->where('event', $event)
            ->where('createtime', '>', time() - 60)->order('id', 'desc')->find();
        if ($last) {
            return $this->fail('发送过于频繁，请稍后再试');
        }

        $code = (string)random_int(100000, 999999);
        Db::name('sms_log')->insert([
            'mobile'     => $mobile,
            'event'      => $event,
            'code'       => $code,
            'status'     => 1,
            'createtime' => time(),
        ]);

        // 开发模式：真实短信通道接入前，直接把验证码返回给前端展示（原站为顶象验证码+真实短信）
        return json([
            'code' => 1,
            'msg'  => '验证码已发送',
            'time' => (string)time(),
            'data' => ['mock_code' => $code, 'expire' => self::TTL],
        ]);
    }

    /** 校验验证码（供其他控制器调用） */
    public static function verify($mobile, $event, $code)
    {
        if (!$code) return false;
        $row = Db::name('sms_log')->where('mobile', $mobile)->where('event', $event)
            ->where('code', $code)->where('status', 1)
            ->where('createtime', '>', time() - self::TTL)->order('id', 'desc')->find();
        return $row ? true : false;
    }

    /** 核销验证码 */
    public static function consume($mobile, $event, $code)
    {
        Db::name('sms_log')->where('mobile', $mobile)->where('event', $event)
            ->where('code', $code)->where('status', 1)->update(['status' => 2]);
    }

    protected function fail($msg)
    {
        return json(['code' => 0, 'msg' => $msg, 'time' => (string)time(), 'data' => null]);
    }
}
