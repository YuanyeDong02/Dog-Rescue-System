<?php
declare (strict_types = 1);

namespace app\service\Mail;

use think\response\Json;

abstract class Base
{
    abstract public function send($to, $subject, $text): Json;
}
