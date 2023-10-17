<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use think\response\View;

class ResetLinkController extends BaseController
{
    public function index(): View
    {
        $code = $this->request->param('code');
        return view("/auth/resetLink", [
            'code' => $code
        ]);
    }
}