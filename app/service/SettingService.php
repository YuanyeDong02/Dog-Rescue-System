<?php
declare (strict_types = 1);

namespace app\service;

use think\facade\Db;
use think\Service;

class SettingService extends Service
{
    public function register()
    {
    	$this->app->bind('settingService', SettingService::class);
    }

    public function getSettings()
    {
    	return $this->app->db->name('setting')->select();
    }

    public function updateSetting(int $id, string $value): void
    {
    	Db::name('setting')->where('id', $id)->update(['value' => $value]);
    }

    public function getSetting(string $key)
    {
    	return $this->app->db->name('setting')->where('key', $key)->find()['value'];
    }
}
