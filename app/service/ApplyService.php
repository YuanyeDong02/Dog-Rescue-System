<?php
declare (strict_types=1);

namespace app\service;

use app\model\Apply;
use app\validate\applytableinfor;
use think\exception\ValidateException;
use think\Service;

class ApplyService extends Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('ApplyService', ApplyService::class);
    }

    public function applytable($table)
    {
        try {
            validate(applytableinfor::class)->check($table);
        } catch (ValidateException $e) {
            return json([
                'msg' => $e->getMessage(),
                'ret' => 0
            ]);
        }
        $apply = new Apply();
        $apply->create($table);
        return json([
            'msg' => "Apply successfully",
            'ret' => 1
        ]);

    }

}

