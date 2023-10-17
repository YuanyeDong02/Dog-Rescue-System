<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\Token;
use Closure;
use think\Request;
use think\Response;
use function redirect;

class ResetLink
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
        $code = $request->param('code');
        $token = new Token();
        $token = $token->where('token', $code)->where('type', 'reset')->findOrEmpty();
        if (!$token->isExists()) {
            return redirect('invalidLink');
        }
        if (strtotime($token->expire) < time()) {
            $token->delete();
            return redirect('invalidLink');
        }
        return $next($request);
    }
}
