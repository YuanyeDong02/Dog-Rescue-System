<?php
declare (strict_types = 1);

namespace app\service;

class Vetting extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('Vetting', Vetting::class);
    }

    public function vetting($table)
    {
        $apply = new \app\model\Apply();
        $apply = $apply->where('id', $table['id'])->findOrEmpty();
        if (!$apply->isExists()) {
            return json([
                'msg' => "Apply does not exist",
                'ret' => 0
            ]);
        }
        $apply->active = 1;
        $apply->save();
        return json([
            'msg' => "Vetting successfully",
            'ret' => 1
        ]);
    }
}
