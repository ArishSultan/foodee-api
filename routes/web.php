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

Route::get('/account/confirm/{email}/{id}', 'AuthController@confirm');
Route::get('/user/{id}', 'AuthController@user');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
