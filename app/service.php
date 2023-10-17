<?php

use app\AppService;
use app\service\AddressService;
use app\service\AuthService;
use app\service\ChannelService;
use app\service\MailService;
use app\service\OrderService;
use app\service\ParcelService;
use app\service\SettingService;
use app\service\UserService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AddressService::class,
    AuthService::class,
    ChannelService::class,
    MailService::class,
    OrderService::class,
    ParcelService::class,
    UserService::class,
    SettingService::class,
];
