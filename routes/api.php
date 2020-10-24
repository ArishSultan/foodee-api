<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'throttle:3'], function () {
    Route::post('forgot-password', 'AuthController@sendResetLinkEmail');
});

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
    Route::get('test', 'AuthController@sendMail');
    Route::get('subscription/test', 'SubscriptionController@sayHello');
    Route::post('subscription/check', 'SubscriptionController@checkSubscription');
    Route::post('subscription/purchase', 'SubscriptionController@purchaseSubscription');

    Route::delete('review/{id}', 'ReviewController@delete');
    Route::delete('profile/{id}', 'ProfileController@delete');
    Route::get('nearby', 'AuthController@nearBy');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('user', 'AuthController@user');
        Route::get('chats', 'ChatController@chats');
        Route::get('logout', 'AuthController@logout');
        Route::get('featured', 'PostController@featured');
        Route::get('profile/{id}', 'AuthController@user');
        Route::get('search/user', 'FilterController@index');
        Route::get('find/peoples', 'AuthController@findPeoples');
        Route::get('timeline/{id}', 'PostController@myTimeline');
        Route::get('reviews/{userId}', 'ReviewController@reviews');
        Route::get('notifications', 'NotificationController@index');
        Route::get('post/{id}/isLikedByMe', 'PostController@isLikedByMe');
        Route::get('who/liked/post/{id}', 'PostController@whoLikedMyPost');
        Route::get('messages/{to_id}/{from_id}', 'ChatController@messages');
        Route::get('nearby', 'AuthController@nearby');

        Route::resource('posts', 'PostController');
        Route::resource('profile', 'ProfileController');
        Route::resource('comments', 'CommentController');
        Route::resource('food', 'FoodCategoryController');

        Route::post('lat/lng', 'AuthController@userLatLng');
        Route::post('send/message', 'ChatController@send');
        Route::post('add/review', 'ReviewController@post');
        Route::post('post/like/{id}', 'PostController@like');
        Route::post('fcm/token', 'AuthController@updateToken');
        Route::post('update/photo', 'ProfileController@updatePhoto');
        Route::post('delete/thread/{id}', 'ChatController@deleteThread');
        Route::post('posts/delete/image', 'PostController@deletePostImages');
        Route::post('notification/clearall', 'NotificationController@clearAll');
        Route::post('notification/delete/{id}', 'NotificationController@deleteNotification');
    });
});

