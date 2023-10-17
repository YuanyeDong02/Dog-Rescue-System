<?php
declare (strict_types=1);

namespace app\middleware;

use app\service\AuthService;
use Closure;
use think\facade\Session;

class Admin
{
    public function handle($request, Closure $next)
    {
        $userID = Session::get("userID");
        if ($userID == null) {
            // 未登录
            $authService = app(AuthService::class);
            if ($authService->rememberLogin()) {
                // cookie有效，用户已登录
                return $next($request);
            } else {
                 return redirect("/auth/login");
            }
        } // 检查权限
        if (isAdmin($userID)) {
            // 是否有权限
            return $next($request);
        } else {
            // 无权限
            return view('/error', [
                'msg' => '无权限'
            ]);
        }
    }
}
