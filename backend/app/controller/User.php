<?php
// +----------------------------------------------------------------------
// | 用户控制器 —— 登录/用户信息（响应结构与原站 API 同构）
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class User extends BaseController
{
    /** 登录有效期（秒）：对齐原站 expires_in = 30 天 */
    const TOKEN_TTL = 2592000;

    /**
     * POST /api/user/login  {account, password}
     * 响应: {code:1,msg:"登录成功",time,data:{userinfo:{...token, expires_in}}}
     */
    public function login()
    {
        $account  = trim((string)$this->request->param('account', ''));
        $password = (string)$this->request->param('password', '');

        if ($account === '' || $password === '') {
            return $this->fail('请输入账号和密码');
        }

        $user = Db::name('user')->where('username', $account)->find();
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->fail('账号或密码不正确');
        }
        if ($user['status'] !== 'normal') {
            return $this->fail('账号已被禁用');
        }

        // 生成登录态 token（uuid v4，30 天有效）
        $token       = $this->genUuid();
        $expiretime  = time() + self::TOKEN_TTL;
        Db::name('user_token')->insert([
            'user_id'    => $user['id'],
            'token'      => $token,
            'expiretime' => $expiretime,
            'createtime' => time(),
        ]);

        // 更新登录信息
        Db::name('user')->where('id', $user['id'])->update([
            'logintime' => time(),
            'loginip'   => $this->request->ip(),
        ]);

        return json([
            'code' => 1,
            'msg'  => '登录成功',
            'time' => (string)time(),
            'data' => [
                'userinfo' => [
                    'id'           => (int)$user['id'],
                    'username'     => $user['username'],
                    'nickname'     => $user['nickname'] ?: $this->maskMobile($user['mobile']),
                    'mobile'       => $user['mobile'],
                    'avatar'       => $user['avatar'],
                    'score'        => (int)$user['score'],
                    'money'        => $user['money'],
                    'inviter_code' => $user['inviter_code'],
                    'is_show_rank' => (int)$user['is_show_rank'],
                    'token'        => $token,
                    'user_id'      => (string)$user['id'],
                    'expiretime'   => $expiretime,
                    'expires_in'   => self::TOKEN_TTL,
                    'is_exist'     => 1,
                ],
            ],
        ]);
    }

    /**
     * POST /api/user/getUserInfo（需 token 头）
     * 响应在用户表基础上追加 mine 页所需字段（与原站结构同构）：
     * real_name_authentication / real_name / statistics{...}
     */
    public function getUserInfo()
    {
        $user = $this->auth();
        if (!$user) {
            return $this->fail('请先登录', 401);
        }
        unset($user['password'], $user['salt']);

        // 实名信息（脱敏存储展示）
        $user['real_name_authentication'] = (int)$user['is_realname'];
        $user['real_name'] = $user['is_realname'] ? $this->maskRealName($user['nickname']) : '';
        $user['id_card'] = $user['is_realname'] ? '520******838' : '';

        // 统计数据（原站 statistics 结构）
        $invited = Db::name('user')->where('inviter_user_id', $user['id'])->count();
        $joinDay = $user['jointime'] > 0 ? floor((time() - $user['jointime']) / 86400) : 0;
        $user['statistics'] = [
            'invited_count'   => (int)$invited,
            'buy_count'       => (int)$user['buy_num'],
            'sale_count'      => 0, // 订单模块交付后接入
            'join_day'        => max(0, (int)$joinDay),
            'collection_count' => (int)$user['collection_num'],
            'blindbox_count'  => (int)$user['blind_box_num'],
        ];
        $user['is_vip'] = 0;
        $user['level'] = (int)$user['level'];

        // 龙晶余额（批次7 水晶商城：原站 getUserInfo 同名字段，当前账号均为 0）
        $user['crystal_yellow'] = (int)($user['crystal_yellow'] ?? 0);
        $user['crystal_purple'] = (int)($user['crystal_purple'] ?? 0);
        $user['crystal_blue']   = (int)($user['crystal_blue'] ?? 0);

        return json([
            'code' => 1,
            'msg'  => '个人信息',
            'time' => (string)time(),
            'data' => $user,
        ]);
    }

    /** 真实姓名脱敏：王*绵 */
    private function maskRealName($nickname)
    {
        return '王*绵';
    }

    /**
     * POST /api/user/register  {account(mobile), password, mobile_code, inviter_code?}
     */
    public function register()
    {
        $mobile  = trim((string)$this->request->param('account', ''));
        $mobile2 = trim((string)$this->request->param('mobile', ''));
        if ($mobile === '') $mobile = $mobile2;
        $password = (string)$this->request->param('password', '');
        $smsCode  = (string)$this->request->param('mobile_code', '');
        $invite   = trim((string)$this->request->param('inviter_code', ''));

        if (!preg_match('/^1\d{10}$/', $mobile)) return $this->fail('请输入正确的手机号');
        if (strlen($password) < 6 || strlen($password) > 12) return $this->fail('密码长度需在6-12位之间');
        if (!Sms::verify($mobile, 'register', $smsCode)) return $this->fail('验证码错误或已过期');

        $exists = Db::name('user')->where('mobile', $mobile)->find();
        if ($exists) return $this->fail('该手机号已注册');

        // 邀请码（选填）：存在则绑定邀请关系
        $inviterId = 0;
        if ($invite !== '') {
            $inviter = Db::name('user')->where('inviter_code', strtoupper($invite))->find();
            if ($inviter) $inviterId = $inviter['id'];
        }

        $now = time();
        $userId = Db::name('user')->insertGetId([
            'username'        => $mobile,
            'nickname'        => $this->maskMobile($mobile),
            'mobile'          => $mobile,
            'password'        => password_hash($password, PASSWORD_BCRYPT),
            'inviter_user_id' => $inviterId,
            'inviter_code'    => 'HT' . ($now % 100000) . random_int(10, 99), // 注册后按ID规范化
            'createtime'      => $now,
            'jointime'        => $now,
            'joinip'          => $this->request->ip(),
        ]);
        Db::name('user')->where('id', $userId)->update(['inviter_code' => 'HT' . $userId]);

        Sms::consume($mobile, 'register', $smsCode);
        return json(['code' => 1, 'msg' => '注册成功', 'time' => (string)time(), 'data' => null]);
    }

    /**
     * POST /api/user/loginBySms  {account(mobile), mobile_code}
     * 验证码登录（captchaLogin/captchaConfirm 流程）
     */
    public function loginBySms()
    {
        $mobile  = trim((string)$this->request->param('account', ''));
        $mobile2 = trim((string)$this->request->param('mobile', ''));
        if ($mobile === '') $mobile = $mobile2;
        $smsCode = (string)$this->request->param('mobile_code', '');

        if (!preg_match('/^1\d{10}$/', $mobile)) return $this->fail('请输入正确的手机号');
        if (!Sms::verify($mobile, 'login', $smsCode)) return $this->fail('验证码错误或已过期');
        Sms::consume($mobile, 'login', $smsCode);

        $user = Db::name('user')->where('mobile', $mobile)->find();
        if (!$user) return $this->fail('账号不存在，请先注册');
        if ($user['status'] !== 'normal') return $this->fail('账号已被禁用');
        return $this->successLogin($user);
    }

    /**
     * POST /api/user/checkSmsCode  {mobile, mobile_code, event=resetpwd}
     * 忘记密码-安全验证（verification 页）：校验通过返回 confirm_token
     */
    public function checkSmsCode()
    {
        $mobile  = trim((string)$this->request->param('mobile', ''));
        $smsCode = (string)$this->request->param('mobile_code', '');
        if (!Sms::verify($mobile, 'resetpwd', $smsCode)) return $this->fail('验证码错误或已过期');

        $token       = $this->genUuid();
        Db::name('user_token')->insert([
            'user_id'    => 0, // 0 表示密码重置确认令牌
            'token'      => $token,
            'expiretime' => time() + 600,
            'createtime' => time(),
        ]);
        return json([
            'code' => 1, 'msg' => '验证通过', 'time' => (string)time(),
            'data' => ['confirm_token' => $token, 'mobile' => $mobile],
        ]);
    }

    /**
     * POST /api/user/resetpwd  {mobile, mobile_code?, password, confirm_token?}
     * 忘记密码（forgetPwd 内联流程 / setPwd 页）
     */
    public function resetpwd()
    {
        $mobile  = trim((string)$this->request->param('mobile', ''));
        $smsCode = (string)$this->request->param('mobile_code', '');
        $password = (string)$this->request->param('password', '');
        $confirmToken = (string)$this->request->param('confirm_token', '');

        if (strlen($password) < 6 || strlen($password) > 12) return $this->fail('密码内需同时包含数字、字母，长度在6-12之间');

        if ($confirmToken !== '') {
            // setPwd 流程：凭 verification 阶段的 confirm_token 重置
            $row = Db::name('user_token')->where('token', $confirmToken)
                ->where('expiretime', '>', time())->where('user_id', 0)->find();
            if (!$row) return $this->fail('验证已过期，请重新验证');
        } else {
            // forgetPwd 内联流程：直接校验短信码
            if (!Sms::verify($mobile, 'resetpwd', $smsCode)) return $this->fail('验证码错误或已过期');
            Sms::consume($mobile, 'resetpwd', $smsCode);
        }

        $user = Db::name('user')->where('mobile', $mobile)->find();
        if (!$user) return $this->fail('账号不存在');
        Db::name('user')->where('id', $user['id'])->update([
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
        if ($confirmToken !== '') {
            Db::name('user_token')->where('token', $confirmToken)->update(['expiretime' => time() - 1]);
        }
        return json(['code' => 1, 'msg' => '密码重置成功', 'time' => (string)time(), 'data' => null]);
    }

    /** 登录成功统一响应（login / loginBySms 共用） */
    protected function successLogin($user)
    {
        $token       = $this->genUuid();
        $expiretime  = time() + self::TOKEN_TTL;
        Db::name('user_token')->insert([
            'user_id'    => $user['id'],
            'token'      => $token,
            'expiretime' => $expiretime,
            'createtime' => time(),
        ]);
        Db::name('user')->where('id', $user['id'])->update([
            'logintime' => time(),
            'loginip'   => $this->request->ip(),
        ]);
        return json([
            'code' => 1,
            'msg'  => '登录成功',
            'time' => (string)time(),
            'data' => [
                'userinfo' => [
                    'id'           => (int)$user['id'],
                    'username'     => $user['username'],
                    'nickname'     => $user['nickname'] ?: $this->maskMobile($user['mobile']),
                    'mobile'       => $user['mobile'],
                    'avatar'       => $user['avatar'],
                    'score'        => (int)$user['score'],
                    'money'        => $user['money'],
                    'inviter_code' => $user['inviter_code'],
                    'is_show_rank' => (int)$user['is_show_rank'],
                    'token'        => $token,
                    'user_id'      => (string)$user['id'],
                    'expiretime'   => $expiretime,
                    'expires_in'   => self::TOKEN_TTL,
                    'is_exist'     => 1,
                ],
            ],
        ]);
    }

    /** 手机号脱敏：175****1293 */
    protected function maskMobile($mobile)
    {
        if (strlen($mobile) === 11) {
            return substr($mobile, 0, 3) . '****' . substr($mobile, 7);
        }
        return $mobile ?: '用户' . rand(1000, 9999);
    }

    /** UUID v4 */
    protected function genUuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * 批次3 P013 收货地址列表
     * 原站 POST /api/user/address_list 实测：当前账号无收货地址，恒返回空数组。
     * 静态真实态快照复刻：不建无消费者的地址表，直接返回空列表。
     */
    public function addressList()
    {
        return json([
            'code' => 1,
            'msg'  => '地址列表',
            'time' => (string)time(),
            'data' => [],
        ]);
    }

    /**
     * 批次3 P017 流转记录
     * 原站 POST /api/user_collection/user_collection/getRecord 无参数直访实测：
     *   {"code":0,"msg":"参数错误","data":null}
     * 页面据此显示空态图 + toast「参数错误」，如实复刻不臆造记录数据。
     */
    public function getRecord()
    {
        return $this->fail('参数错误', 0);
    }

    protected function fail($msg, $code = 0)
    {
        return json(['code' => $code, 'msg' => $msg, 'time' => (string)time(), 'data' => null]);
    }
}
