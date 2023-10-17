<?php
declare (strict_types=1);

namespace app\middleware;

use app\service\AuthService;
use Closure;
use think\facade\Session;
use think\Request;
use think\Response;
use function redirect;

class Auth
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (Session::get("userID") != null) {
            // 存在session，用户已登录
            return redirect("/user/index");
        } // 检查cookie
        $authService = app(AuthService::class);
        if ($authService->rememberLogin()) {
            // cookie有效，用户已登录
            return redirect("/user/index");
        } else {
            // 用户未登录
            return $next($request);
        }
    }
}
