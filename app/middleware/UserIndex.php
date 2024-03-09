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
     *
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        if (Session::get("userID") == null) {
            $authService = app(AuthService::class);
            if ($authService->rememberLogin()) {
                return $next($request);
            } else {
                 return redirect("/auth/login");
            }
        } else {
            return $next($request);
        }
    }
}
