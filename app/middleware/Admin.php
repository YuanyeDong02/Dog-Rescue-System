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

            $authService = app(AuthService::class);
            if ($authService->rememberLogin()) {

                return $next($request);
            } else {
                 return redirect("/auth/login");
            }
        }
        if (isAdmin($userID)) {

            return $next($request);
        } else {

            return view('/error', [
                'msg' => 'No Authority'
            ]);
        }
    }
}
