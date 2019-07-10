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
        Route::get('profile/{id}', 'AuthController@user');
        Route::post('fcm/token', 'AuthController@updateToken');
        Route::post('lat/lng', 'AuthController@userLatLng');
        Route::get('find/peoples', 'AuthController@findPeoples');
        Route::resource('posts', 'PostController');
        Route::resource('comments', 'CommentController');
        Route::resource('profile', 'ProfileController');
        Route::resource('food', 'FoodCategoryController');
        Route::get('post/{id}/isLikedByMe', 'PostController@isLikedByMe');
        Route::post('post/like/{id}', 'PostController@like');
        Route::get('timeline/{id}', 'PostController@myTimeline');
        Route::post('update/photo', 'ProfileController@updatePhoto');
        Route::get('search/user', 'FilterController@index');

        // Chat messages routes
        Route::post('send/message', 'ChatController@send');

        Route::get('chats', 'ChatController@chats');

        Route::get('messages/{to_id}/{from_id}', 'ChatController@messages');

        Route::get('notifications', 'NotificationController@index');
    });

});
//Route::middleware('auth:api')->get('/user', '');
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
