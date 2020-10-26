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
use think\facade\Route;

/*Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');*/

Route::group('', function () {

    /*Route::get('customer_orders', 'app\admin\controller\CustomerOrders@index');
    Route::get('customer_orders', 'app\admin\controller\CustomerOrders@lists');*/

    Route::group('', function () {
        // Route::resource('customer_orders', 'app\admin\controller\CustomerOrders');
    	Route::get('customer_orders/index', 'app\admin\controller\CustomerOrders@index');
        Route::get('customer_orders/lists', 'app\admin\controller\CustomerOrders@lists');
        Route::rule('customer_orders/add', 'app\admin\controller\CustomerOrders@create','GET|POST');
    	Route::rule('customer_orders/get_temporary_goods', 'app\admin\controller\CustomerOrders@get_temporary_goods','GET|POST');
        Route::rule('customer_orders/get_all_goods', 'app\admin\controller\CustomerOrders@get_all_goods','GET|POST');
        Route::rule('customer_orders/add_goods', 'app\admin\controller\CustomerOrders@add_goods','GET|POST');
        Route::rule('customer_orders/del_goods', 'app\admin\controller\CustomerOrders@del_goods','GET|POST');

        // 编辑客户下单
        Route::rule('customer_orders/show_edit', 'app\admin\controller\CustomerOrders@show_edit','GET|POST');
    })->middleware([
        thans\layuiAdmin\middleware\Login::class,
        thans\layuiAdmin\middleware\AdminsAuth::class,
    ]);

    /*Route::group('', function () {
        Route::group('personal', function () {
            Route::rule(
                'setting',
                'thans\layuiAdmin\controller\Personal@setting',
                'GET|POST'
            );
        });
        Route::get('logout', 'thans\layuiAdmin\controller\Login@logout');
        Route::post('upload/image', 'thans\layuiAdmin\controller\Upload@image');
        Route::post('upload/file', 'thans\layuiAdmin\controller\Upload@file');
    })->middleware([thans\layuiAdmin\middleware\Login::class]);*/

    /*Route::group('', function () {

        Route::group('personal', function () {
            Route::rule(
                'setting',
                'thans\layuiAdmin\controller\Personal@setting',
                'GET|POST'
            );
        });
        Route::get('logout', 'thans\layuiAdmin\controller\Login@logout');
        Route::post('upload/image', 'thans\layuiAdmin\controller\Upload@image');
        Route::post('upload/file', 'thans\layuiAdmin\controller\Upload@file');
    })->middleware([thans\layuiAdmin\middleware\Login::class]);

    Route::group('', function () {
        Route::get('dashboard', 'thans\layuiAdmin\controller\Index@dashboard');
        Route::resource('menu', 'thans\layuiAdmin\controller\Menu');
        Route::resource(
            'permission',
            'thans\layuiAdmin\controller\auth\Permission'
        );
        Route::resource('role', 'thans\layuiAdmin\controller\auth\Role');
        Route::resource(
            'auth/admins',
            'thans\layuiAdmin\controller\auth\Admins'
        )->except(['delete']);
    })->middleware([
        thans\layuiAdmin\middleware\Login::class,
        thans\layuiAdmin\middleware\AdminsAuth::class,
    ]);

    Route::group('system', function () {
        Route::resource('config_tab.config', 'thans\layuiAdmin\controller\system\Config');
        Route::rule('config_tab/setting/:type/[:tab_id]', 'thans\layuiAdmin\controller\system\ConfigTab@setting');
        Route::resource('config_tab', 'thans\layuiAdmin\controller\system\ConfigTab');
    })->middleware([
        thans\layuiAdmin\middleware\Login::class,
        thans\layuiAdmin\middleware\AdminsAuth::class,
    ]);*/
});