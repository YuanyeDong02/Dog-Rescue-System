<?php
declare (strict_types = 1);

namespace app\service;

use think\response\Json;
use think\Service;

class MailService extends Service
{
    public function register()
    {
    	$this->app->bind('mailService', MailService::class);
    }

    public function getDriver()
    {
        $mailDriver = env('MAIL.DRIVER');
        switch ($mailDriver) {
            case 'smtp':
                return new Mail\Smtp();
            case 'postal':
                return new Mail\Postal();
            default:
                return new Mail\NullMail();
        }
    }

    public function sendmail($toAddress, $subject, $body): Json
    {
        if (empty($toAddress)) {
            return json([
                'msg' => "邮箱不能为空",
                'ret' => 0
            ]);
        }
        if (!filter_var($toAddress, FILTER_VALIDATE_EMAIL)) {
            return json([
                'msg' => "邮箱格式不正确",
                'ret' => 0
            ]);
        }
        $mailDriver = $this->getDriver();
        return $mailDriver->send($toAddress, $subject, $body);
    }
}
