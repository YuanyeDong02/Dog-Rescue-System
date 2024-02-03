<?php
declare (strict_types=1);

namespace app\service;

use app\model\User;
use think\facade\Cache;
use think\facade\Db;
use think\facade\View;
use think\Paginator;
use think\response\Json;
use think\Service;

class UserService extends Service
{

    public function register()
    {
        $this->app->bind('userService', UserService::class);
    }

    public function getPoints(int $userID): float
    {
        $points = Db::name('user')->where('id', $userID)->value('points');

        if (empty($points)) {
            return 0;
        }

        return $points;
    }

    public function getUser(int $userID): ?array
    {
        $user = Db::name('user')->where('id', $userID)->find();
        return $user;
    }

    public function getUsers(): Paginator
    {
        return Db::name('user')->paginate(25);
    }





    public function updateUser(int $userid, array $data): Json
    {
        $user = new User();
        $user = $user->where('id', $userid)->find();
        // 用户不存在
        if (empty($user)) {
            return json([
                'ret' => 0,
                'msg' => 'User does not exist'
            ]);
        }
        // 更新用户信息
        $result = array();
        $keys = array_keys($data);
        if (in_array('username', $keys) && $user->username != $data['username']) {
            // 检查重复
            $check = Db::name('user')->where('username', $data['username'])->find();
            if (!empty($check)) {
                return json([
                    'ret' => 0,
                    'msg' => 'Username already exists'
                ]);
            }
            unset($check);
            $result['username'] = $data['username'];
        }
        if (in_array('email', $keys) && $user->email != $data['email']) {
            // 检查重复
            $check = Db::name('user')->where('email', $data['email'])->find();
            if (!empty($check)) {
                return json([
                    'ret' => 0,
                    'msg' => 'Email already exists'
                ]);
            }
            $result['email'] = $data['email'];
        }
        if (in_array('tel', $keys) && $user->tel != $data['tel']) {
            $result['tel'] = $data['tel'];
        }

        if (in_array('password', $keys) && $data['password'] != '') {
            $result['password'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        }
        if (in_array('admin', $keys) && $user->admin != $data['admin']) {
            $result['admin'] = $data['admin'];
        }
        if ($user->update($result, ['id' => $userid])) {
            return json([
                'ret' => 1,
                'msg' => 'Successful update'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => 'update failure'
            ]);
        }
    }

    public function deleteUser(int $id): bool
    {
        $user = new User();
        $user = $user->where('id', $id)->find();
        if (empty($user)) {
            return false;
        }
        return $user->delete();
    }



    public function Statussucess (string $email): Json
    {


        $link = env("APP.URL");
        // 生成邮件内容
        // 从/resources/email/reset.html读取文件
        $content = View::fetch(app()->getRootPath() . "resources/email/Statussucess.html", [
            'link' => $link
        ]);


        // 发送邮件
        return $this->app->mailService->sendmail($email, "Password reset request", $content);
    }

    public function Statusreject (string $email): Json
    {


        $link = env("APP.URL");
        // 生成邮件内容
        // 从/resources/email/reset.html读取文件
        $content = View::fetch(app()->getRootPath() . "resources/email/Statusreject.html", [
            'link' => $link
        ]);


        // 发送邮件
        return $this->app->mailService->sendmail($email, "Password reset request", $content);
    }
}
