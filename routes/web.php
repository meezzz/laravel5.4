<?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::get('/', function () {
        return view('welcome');
    });

    //TestController
    Route::resource('test', 'TestController');

    //UserController
    Route::get('/user/test', 'UserController@test');
    Route::get('/user/relation', 'UserController@relation');
    Route::resource('/user', 'UserController');


    /**************     admin start    ***************************/
    //admin 后台首页
    Route::get('/admin', 'Admin\AdminController@index');
    //用户管理
    Route::get('/admin/user/add', 'Admin\UserController@add');
    Route::post('/admin/user/doadd', 'Admin\UserController@doadd');
    Route::get('/admin/user/index', 'Admin\UserController@index');
    Route::get('/admin/user/edit/{id}', 'Admin\UserController@edit');
    Route::post('/admin/user/doedit', 'Admin\UserController@doedit');
    Route::get('/admin/user/delete/{id}', 'Admin\UserController@delete');

    //分类
    Route::resource('/admin/cate', 'Admin\CateController');
    /**************     admin end    ***************************/
    /************************   workman   ****************************/
    
    /************************   workman   ****************************/









