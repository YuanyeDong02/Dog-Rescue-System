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
     *
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (Session::get("userID") != null) {

            return redirect("/user/index");
        }
        $authService = app(AuthService::class);
        if ($authService->rememberLogin()) {

            return redirect("/user/index");
        } else {
            return $next($request);
        }
    }
}
