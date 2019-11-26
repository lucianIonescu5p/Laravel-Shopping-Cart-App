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

Route::get('/', 'ShopController@index');

Route::get('/cart', 'ShopController@cart');
Route::post('/cart', 'ShopController@mail');

Route::get('/login', 'LoginController@login');
Route::get('/logout', 'LoginController@logout');
Route::post('/login', 'LoginController@auth');

Route::get('/orders', 'OrdersController@orders')->middleware('admin.auth');
Route::get('/order', 'OrdersController@order')->middleware('admin.auth');

Route::resource('products', 'ProductsController')->middleware('admin.auth');
