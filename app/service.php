<?php

use app\service\ApplyService;
use app\service\AuthService;
use app\service\MailService;
use app\service\UserService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AuthService::class,
    MailService::class,
    UserService::class,
    ApplyService::class

];
