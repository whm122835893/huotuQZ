<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use think\facade\Db;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /** token 鉴权：优先 header token，兼容请求体 token（发布代理层剥离自定义头时兜底） */
    protected function auth()
    {
        $token = (string)$this->request->header('token', '');
        if ($token === '') {
            $token = (string)$this->request->param('token', '');
        }
        if ($token === '') {
            return null;
        }
        $row = Db::name('user_token')->where('token', $token)->where('expiretime', '>', time())->find();
        if (!$row) {
            return null;
        }
        return Db::name('user')->where('id', $row['user_id'])->find();
    }

    /** 统一失败返回（与原站 code 结构一致） */
    protected function fail($msg, $code = 0)
    {
        return json(['code' => $code, 'msg' => $msg, 'time' => (string)time(), 'data' => null]);
    }

    /** 统一成功返回 */
    protected function ok($data, string $msg = 'ok')
    {
        return json(['code' => 1, 'msg' => $msg, 'time' => (string)time(), 'data' => $data]);
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

}
