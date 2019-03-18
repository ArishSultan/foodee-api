<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
    'prefix' => 'auth',
    'middleware' => 'cors'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::group([
    'prefix' => 'v1',
    'middleware' => 'cors'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::resource('posts', 'PostController');
        Route::resource('comments', 'CommentController');
        Route::resource('profile', 'ProfileController');
        Route::resource('food', 'FoodCategoryController');
        Route::get('post/{id}/isLikedByMe', 'PostController@isLikedByMe');
        Route::post('post/like/{id}', 'PostController@like');
    });
});
//Route::middleware('auth:api')->get('/user', '');
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
