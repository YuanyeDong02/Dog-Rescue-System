<?php
// 应用公共文件
use app\service\SettingService;
use think\facade\Db;

function isAdmin($id): bool
{
    $result = Db::name('user')->field('admin')->where('id', $id)->find();
    if ($result) {
        return $result['admin'];
    }
    return false;
}

function generateToken($length = 32): string
{
    // 随机字符串
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $str[mt_rand(0, strlen($str) - 1)];
    }
    return $token;
}

function getPointRate(): float
{
    return getSetting('pointRate');
}

function getSetting(string $key)
{
    $settingService = app(SettingService::class);
    return $settingService->getSetting($key);
}