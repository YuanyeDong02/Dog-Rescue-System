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
    Route::get('apply/add', 'user/Applyinformation');
    Route::post('apply/add', 'user/postapply');
    Route::get('apply/Applyfinished', 'user/Applyfinished');
    Route::get('apply', 'user/Applyprogress');
    Route::get('dogdetail/:id', 'user/dogdetail');
    Route::get('dogname/:id', 'user/dogname');
    Route::rule('logout', 'user/logout');

})->middleware(UserIndex::class);

// 注册admin
Route::rule('admin/', 'admin/index')->middleware(Admin::class);
Route::group('admin', function () {
    Route::rule('/', 'admin/index');
    Route::rule('index', 'admin/index');
    Route::get('newDog', 'admin/newDog');
    Route::post('newDog', 'admin/postnewDog');

    // 用户相关
    Route::get('users/:id', 'admin/editUser');
    Route::post('users/:id', 'admin/updateUser');
    Route::delete('users/:id', 'admin/deleteUser');
    Route::rule('users', 'admin/users');
    // 渠道相关
   ;
})->middleware(Admin::class);

Route::miss(function () {
    return view('404');
});