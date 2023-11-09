<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use think\Request;
use think\response\Json;
use think\response\View;

class AuthController extends BaseController
{
    public function login(Request $request): Json
    {
        if (env('enable_captcha')) {
            $captcha = $request->param('captcha');
            if (!captcha_check($captcha)) {
                return json([
                    'ret' => 0,
                    'msg' => 'CAPTCHA error'
                ]);
            }
        }
        $email = $request->param('email');
        $password = $request->param('password');
        $rememberMe = $request->param('rememberMe');
        return $this->app->authService->login($email, $password, $rememberMe);
    }

    public function registerPage(): View
    {
        return env('enable_register') ?
            view('/auth/register') :
            view('/error', [
                'msg' => 'Registration is disabled'
            ]);
    }

    public function register(Request $request): Json
    {
        if (!env('enable_register')) {
            return json([
                'ret' => 0,
                'msg' => 'Registration is disabled'
            ]);
        }
        if (env('enable_captcha')) {
            $captcha = $request->param('captcha');
            if (!captcha_check($captcha)) {
                return json([
                    'ret' => 0,
                    'msg' => 'CAPTCHA error'
                ]);
            }
        }

        $email = $request->param('email');
        $tel = $request->param('tel');
        $password = $request->param('password');
        return $this->app->authService->userRegister($email, $tel, $password);
    }

    public function reset(Request $request): Json
    {
        if (env('enable_captcha')) {
            $captcha = $request->param('captcha');
            if (!captcha_check($captcha)) {
                return json([
                    'ret' => 0,
                    'msg' => 'CAPTCHA error'
                ]);
            }
        }
        $email = $request->param('email');
        return $this->app->authService->reset($email);
    }

    public function resetPassword(Request $request): Json
    {
        $code = $request->param('code');
        $password = $request->param('password');
        return $this->app->authService->resetPassword($code, $password);
    }
}
