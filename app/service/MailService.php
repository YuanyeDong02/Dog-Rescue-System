<?php
declare (strict_types = 1);

namespace app\service;

use Postal\Client;
use Postal\Send\Message;
use think\console\Output;
use think\response\Json;
use think\Service;

class MailService extends Service
{
    public function register()
    {
    	$this->app->bind('mailService', MailService::class);
    }

    public function sendmail($toAddress, $subject, $body): Json
    {
        if (empty($toAddress)) {
            return json([
                'msg' => "Mailbox cannot be empty",
                'ret' => 0
            ]);
        }
        if (!filter_var($toAddress, FILTER_VALIDATE_EMAIL)) {
            return json([
                'msg' => "Incorrect mailbox format",
                'ret' => 0
            ]);
        }

        $client = new Client(env('MAIL.POSTAL_URL'), env('MAIL.POSTAL_KEY'));
        $message = new Message();
        $senderName = env('MAIL.SENDER_NAME');
        $senderAddress = env('MAIL.SENDER_ADDRESS');
        $message->from("$senderName <$senderAddress>");
        $message->sender($senderAddress);
        $message->to($toAddress);
        $message->subject($subject);
        $message->htmlBody($body);
        try {
            $result = $client->send->message($message);
            return json([
                'msg' => "Email sent successfully",
                'ret' => 1
            ]);
        } catch (\Exception $e) {
            return json([
                'msg' => "Mail delivery failure",
                'ret' => 0,
                'error' => $e->getMessage()
            ]);


        }



    }
}
