<?php
declare (strict_types=1);

namespace app\service;

use think\facade\Db;
use think\Service;

class ChannelService extends Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('channelService', ChannelService::class);
    }

    public function getChannels(bool $page = false)
    {
        $channels = Db::name('channel');
        if ($page) {
            return $channels->paginate([
                'list_rows' => 10,
                'var_page' => 'page',
            ]);
        } else {
            return $channels->select();
        }
    }

    public function updateChannel(int $id, array $data)
    {
        return Db::name('channel')->where('id', $id)->update($data);
    }

    public function addChannel(array $data)
    {
        return Db::name('channel')->insert($data);
    }

    public function deleteChannel(int $id)
    {
        return Db::name('channel')->where('id', $id)->delete();
    }
}
