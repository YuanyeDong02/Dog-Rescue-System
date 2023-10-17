<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use app\middleware\Admin;
use app\middleware\Auth;
use app\middleware\UserIndex;
use app\middleware\ResetLink;
use think\facade\Route;

// 注册index
Route::rule('/', view('index'));
Route::rule('index', 'index');

// 注册auth
Route::group('auth', function () {
    Route::rule('/', 'index/index');
    Route::get('login', view('auth/login'));
    Route::post('login', 'auth/login');
    Route::get('register', 'auth/registerPage');
    Route::post('register', 'auth/register');
    Route::get('reset', view('auth/reset'));
    Route::post('reset', 'auth/reset');
    Route::post('resetPassword', 'auth/resetPassword');
})->middleware(Auth::class);

// 注册重置链接
Route::get('resetLink', 'ResetLink/index')->middleware(ResetLink::class);
Route::get('invalidLink', view('auth/linkNotFound'));

// 注册user
Route::rule('user/', 'user/index')->middleware(UserIndex::class);
Route::group('user', function () {
    Route::rule('/', 'user/index');
    Route::rule('index', 'user/index');
    // 快递相关
    Route::get('parcels/add', view('user/addParcel'));
    Route::post('parcels/add', 'user/addParcel');
    Route::get('parcels', 'user/parcels');
    // 地址相关
    Route::get('addresses/add', 'user/addAddressPage');
    Route::post('addresses/add', 'user/addAddress');
    Route::get('addresses/:id', 'user/editAddress');
    Route::post('addresses/:id', 'user/updateAddress');
    Route::delete('addresses/:id', 'user/deleteAddress');
    Route::get('addresses', 'user/addresses');
    // 设置相关
    Route::get('settings', 'user/settings');
    Route::post('settings', 'user/updateSettings');
    // 渠道相关
    Route::rule('channels', 'user/channels');
    // 订单相关
    Route::get('orders/:orderUID/image', 'user/getOrderImagePage');
    Route::get('orders/:orderUID/parcels', 'user/getOrderParcels');
    Route::get('orders/add/select', 'user/selectAddress');
    Route::get('orders/add', 'user/addOrderPage');
    Route::post('orders/add', 'user/addOrder');
    Route::get('orders', 'user/orders');
    // 支付相关
    Route::get('pay', 'user/paymentPage');
    // 获取图片
    Route::rule('getOrderImage/:md5', 'user/getOrderImage');
    // 登出
    Route::rule('logout', 'user/logout');
})->middleware(UserIndex::class);

// 注册admin
Route::rule('admin/', 'admin/index')->middleware(Admin::class);
Route::group('admin', function () {
    Route::rule('/', 'admin/index');
    Route::rule('index', 'admin/index');
    // 快递相关
    Route::post('parcels/:id', 'admin/parcelSetStatus');
    Route::delete('parcels/:id', 'admin/parcelDelete');
    Route::rule('parcels', 'admin/parcels');
    // 批量录入页面
    Route::get('batchParcel', view('admin/batchParcel'));
    Route::post('batchParcel', 'admin/batchParcel');
    // 订单相关
    Route::get('orders/handle/:uid', 'admin/handleOrderPage');
    Route::post('orders/handle', 'admin/handleOrder');
    Route::post('orders/update/:uid', 'admin/orderSetStatus');
    Route::delete('orders/delete/:uid', 'admin/orderDelete');
    Route::rule('orders', 'admin/orders');
    // 用户相关
    Route::get('users/:id', 'admin/editUser');
    Route::post('users/:id', 'admin/updateUser');
    Route::delete('users/:id', 'admin/deleteUser');
    Route::rule('users', 'admin/users');
    // 渠道相关
    Route::get('channels/:add', 'admin/addChannelPage');
    Route::post('channels/:add', 'admin/addChannel');
    Route::get('channels/:id', 'admin/channelsDetail');
    Route::post('channels/:id', 'admin/updateChannel');
    Route::delete('channels/:id', 'admin/deleteChannel');
    Route::rule('channels', 'admin/channels');
    // 网站设置相关
    Route::get('settings/:id', 'admin/settingDetail');
    Route::post('settings/:id', 'admin/updateSetting');
    Route::rule('settings', 'admin/settings');
})->middleware(Admin::class);

Route::miss(function () {
    return view('404');
});