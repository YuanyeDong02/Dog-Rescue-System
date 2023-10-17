<?php
declare (strict_types=1);

namespace app\service\Mail;

use Exception;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use think\response\Json;

final class Smtp extends Base
{
    public function __construct()
    {
    }

    public function send($toAddress, $subject, $body): Json
    {
        $mail = new Message();
        try {
            $mail->setFrom(env('MAIL.SENDER_ADDRESS'), env('MAIL.SENDER_NAME'))
                ->addTo($toAddress)
                ->setSubject($subject)
                ->setHtmlBody($body);
            $mailer = new SmtpMailer([
                'host' => env('MAIL.HOST'),
                'username' => env('MAIL.USERNAME'),
                'password' => env('MAIL.PASSWORD'),
                'secure' => env('MAIL.SECURE'),
                'port' => env('MAIL.PORT')
            ]);
            $mailer->send($mail);
            return json([
                'msg' => "邮件发送完毕",
                'ret' => 1
            ]);
        } catch (Exception $e) {
            return json([
                'msg' => "邮件发送失败",
                'error' => $e->getMessage(),
                'ret' => 0
            ]);
        }
    }
}
