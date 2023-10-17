<?php
declare (strict_types = 1);

namespace app\service\Mail;

use think\response\Json;

final class NullMail extends Base
{
    public function __construct()
    {
    }

    public function send($to, $subject, $text): Json
    {
        return json([
            'msg' => "邮件发送完毕",
            'ret' => 1
        ]);
    }
}
