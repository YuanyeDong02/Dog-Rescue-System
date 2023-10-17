<?php
declare (strict_types=1);

namespace app\service;

use app\model\Token;
use app\model\User;
use think\facade\Cookie;
use think\facade\Session;
use think\facade\View;
use think\response\Json;
use think\Service;

class AuthService extends Service
{
    public function register(): void
    {
        $this->app->bind('authService', AuthService::class);
    }

    public function login(string $email, string $password, bool $rememberMe): Json
    {
        if (empty($email)) {
            return json([
                'msg' => "E-mail cannot be empty",
                'ret' => 0
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json([
                'msg' => "Incorrect e-mail format",
                'ret' => 0
            ]);
        }
        if (empty($password)) {
            return json([
                'msg' => "Password cannot be empty",
                'ret' => 0
            ]);
        }
        $user = new User();
        $user = $user->where('email', $email)->findOrEmpty();
        if (!$user->isExists()) {
            return json([
                'msg' => "User does not exist",
                'ret' => 0
            ]);
        }
        if (!password_verify($password, $user->password)) {
            return json([
                'msg' => "Incorrect password",
                'ret' => 0
            ]);
        }
        if ($rememberMe) {
            $token = new Token();
            $token->token = generateToken();
            $token->userID = $user->id;
            $token->expire = date('Y-m-d H:i:s', strtotime('+7 days'));
            $token->type = 'cookie';
            $token->save();
            Cookie::set("email", $email, 3600 * 24 * 7);
            Cookie::set("token", $token->token, 3600 * 24 * 7);
        }
        Session::set('userID', $user->id);
        return json([
            'msg' => "Login successful",
            'ret' => 1
        ]);
    }

    public function rememberLogin()
    {
        $tokenCode = Cookie::get('token');
        $email = Cookie::get('email');
        if (empty($tokenCode) || empty($email)) {
            return false;
        }
        $email = urldecode($email);
        $user = new User();
        $user = $user->where('email', $email)->findOrEmpty();
        if (!$user->isExists()) {
            return false;
        }
        $token = new Token();
        $token = $token->where('token', $tokenCode)->where('type', 'cookie')->findOrEmpty();
        if (!$token->isExists()) {
            return false;
        }
        if ($token->expire < date('Y-m-d H:i:s')) {
            return false;
        }
        if ($token->userID != $user->id) {
            return false;
        }
        Session::set('userID', $user->id);
        return true;
    }

    public function userRegister(string $email, string $tel, string $password): Json
    {
        if (empty($tel)) {
            return json([
                'msg' => "Telephone number cannot be empty",
                'ret' => 0
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json([
                'msg' => "Incorrect e-mail format",
                'ret' => 0
            ]);
        }
        if (empty($password)) {
            return json([
                'msg' => "Password cannot be empty",
                'ret' => 0
            ]);
        }
        $user = new User();
        // 检查邮箱是否已经注册
        $user = $user->where('email', $email)->findOrEmpty();
        if ($user->isExists()) {
            return json([
                'msg' => "E-mail already exists",
                'ret' => 0
            ]);
        }
        $user->tel = $tel;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_ARGON2ID);
        if ($user->save()) {
            return json([
                'msg' => "Register successful",
                'ret' => 1
            ]);

        } else {
            return json([
                'msg' => "Register failed",
                'ret' => 0
            ]);
        }
    }

    public function reset(string $email): Json
    {
        // 检查邮箱是否已经注册
        $user = new User();
        $user = $user->where('email', $email)->findOrEmpty();
        if (!$user->isExists()) {
            return json([
                'msg' => "Email not registered",
                'ret' => 0
            ]);
        }
        $username = $user->username;
        // 生成随机链接
        $code = generateToken(32);
        $link = env("APP.URL") . "/resetLink?code=" . $code;
        // 生成邮件内容
        // 从/resources/email/reset.html读取文件
        $content = View::fetch(app()->getRootPath() . "resources/email/reset.html", [
            'username' => $username,
            'link' => $link
        ]);
        // 保存code
        $token = new Token();
        $token->token = $code;
        $token->userID = $user->id;
        $token->expire = date('Y-m-d H:i:s', strtotime('+1 day'));
        $token->type = 'reset';
        $token->save();
        // 发送邮件
        return $this->app->mailService->sendmail($email, "Password reset request", $content);
    }

    public function resetPassword(string $code, string $password): Json
    {
        $token = new Token();
        $token = $token->where('token', $code)->where('type', 'reset')->findOrEmpty();
        if (!$token->isExists()) {
            return json([
                'msg' => "Invalid link",
                'ret' => 0
            ]);
        }
        if ($token->expire < date('Y-m-d H:i:s')) {
            return json([
                'msg' => "Link has expired, please regenerate",
                'ret' => 0
            ]);
        }
        $user = new User();
        $user = $user->where('id', $token->userID)->findOrEmpty();
        if (!$user->isExists()) {
            return json([
                'msg' => "Unable to match the corresponding user",
                'ret' => 0
            ]);
        }
        $user->password = password_hash($password, PASSWORD_ARGON2ID);
        if (!$user->save()) {
            return json([
                'msg' => "Failure to save user data",
                'ret' => 0
            ]);
        } else {
            // 摧毁token
            $token->delete();
            return json([
                'msg' => "Please use new password to log in",
                'ret' => 1
            ]);
        }
    }
}
