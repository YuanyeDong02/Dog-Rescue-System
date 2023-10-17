<?php
declare (strict_types = 1);

namespace app\service\Mail;

use Exception;
use think\response\Json;
use Postal\Client;
use Postal\Send\Message;

final class Postal extends Base
{
    public function __construct()
    {
    }

    public function send($toAddress, $subject, $body): Json
    {
        try {
            $client = new Client(env('MAIL.POSTAL_URL'), env('MAIL.POSTAL_KEY'));
            $message = new Message();
            $message->to($toAddress);
            $senderName = env('MAIL.SENDER_NAME');
            $senderAddress = env('MAIL.SENDER_ADDRESS');
            $message->sender($senderAddress);
            $message->from("$senderName <$senderAddress>");
            $message->replyTo($senderAddress);
            $message->subject($subject);
            $message->htmlBody($body);
            $result = $client->send->message($message);
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
