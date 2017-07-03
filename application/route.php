<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



use think\Route;

Route::resource('admin', 'admin/user'); // 用户增删改
Route::resource('adfilm', 'admin/film'); // 电影增删改
Route::resource('schedule', 'admin/filmschedule'); // 排片表增删改
Route::get('adlog', 'admin/adminlogin/index'); // 后台登录
Route::get('add', 'admin/banner/add');//轮播图管理
Route::get('links', 'admin/quicklinks/index');//友情链接
Route::get('order', 'admin/Orders/index');//后台订单管理



//首页
// Route::get('index', 'index/index/index');

//影片
Route::get('cinema', 'index/Cinema/index');
//影院
Route::get('film', 'index/Film/index');
//前台影片搜索
Route::get('search', 'index/Search/index');
//后台影片搜索
Route::get('filmsearch', 'admin/search/filmsearch');
//后台管理前台首页展示管理
Route::get('adminnotice', 'admin/notice/index');
//链接搜索
Route::get('linkssearch', 'admin/search/linksearch');
//用户搜索
Route::get('usersearch', 'admin/search/user');
//收藏
Route::get('collection', 'index/collection/save');
//取消收藏
Route::get('delcollection'. 'index/collection/delete');

//影院影片
Route::get('movie', 'index/Movie/index');
//登录
Route::get('login', 'index/index/login');
//退出
Route::get('loginout', 'index/user/loginout');
//个人中心
Route::get('PersonalCenter', 'index/index/PersonalCenter');
//修改信息
Route::get('faq', 'index/index/faq');
//评论
Route::get('grid', 'index/index/grid');
//收藏
Route::get('plans', 'index/index/plans');
//订单
Route::get('charts', 'index/user/charts');
//注册
Route::get('register', 'index/index/register');
//前台用户订单删除
Route::get('rm_order', 'index/user/rm_order');



