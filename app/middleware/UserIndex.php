<?php
declare (strict_types = 1);

namespace app\middleware;

use app\service\AuthService;
use Closure;
use think\facade\Session;
use think\Request;
use think\Response;
use function redirect;

class UserIndex
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        if (Session::get("userID") == null) {
            // 未登录
            $authService = app(AuthService::class);
            if ($authService->rememberLogin()) {
                // cookie有效，用户已登录
                return $next($request);
            } else {
                 return redirect("/auth/login");
            }
        } else {
            // 已登录
            return $next($request);
        }
    }
}
