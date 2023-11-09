<?php
declare (strict_types=1);

namespace app\service;

use app\model\User;
use think\facade\Db;
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
                'msg' => '用户不存在'
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
                    'msg' => '用户名已存在'
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
                    'msg' => '邮箱已存在'
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
                'msg' => '更新成功'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '更新失败'
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

    public function getSumCost(int $id): float
    {
        $sum = Db::name('orders')->where('userID', $id)->sum('price');
        if (empty($sum)) {
            return 0;
        }
        return $sum;
    }
}
