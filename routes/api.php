<?php

use Illuminate\Support\Facades\Route;

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

    Route::post('subscription/purchase', 'SubscriptionController@purchaseSubscription');
    Route::post('subscription/check', 'SubscriptionController@checkSubscription');
    Route::get('subscription/test', 'SubscriptionController@sayHello');
	Route::delete('profile/{id}', 'ProfileController@delete');
        Route::delete('review/{id}', 'ReviewController@delete');
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::get('profile/{id}', 'AuthController@user');
        Route::post('fcm/token', 'AuthController@updateToken');
        Route::post('lat/lng', 'AuthController@userLatLng');
        Route::get('find/peoples', 'AuthController@findPeoples');
        Route::resource('comments', 'CommentController');
        Route::resource('profile', 'ProfileController');
        Route::resource('food', 'FoodCategoryController');
        Route::resource('posts', 'PostController');
        Route::get('featured', 'PostController@featured');
        Route::get('post/{id}/isLikedByMe', 'PostController@isLikedByMe');
        Route::post('post/like/{id}', 'PostController@like');
        Route::get('timeline/{id}', 'PostController@myTimeline');
        Route::post('update/photo', 'ProfileController@updatePhoto');
        Route::get('search/user', 'FilterController@index');
        Route::post('posts/delete/image', 'PostController@deletePostImages');
        Route::get('who/liked/post/{id}', 'PostController@whoLikedMyPost');
        // Chat messages routes
        Route::post('send/message', 'ChatController@send');
        Route::post('delete/thread/{id}', 'ChatController@deleteThread');
        Route::get('chats', 'ChatController@chats');

        Route::get('messages/{to_id}/{from_id}', 'ChatController@messages');

        Route::get('notifications', 'NotificationController@index');
        Route::post('notification/delete/{id}', 'NotificationController@deleteNotification');
        Route::post('notification/clearall', 'NotificationController@clearAll');

        Route::post('add/review', 'ReviewController@post');
        Route::get('reviews/{userId}', 'ReviewController@reviews');
    });
});

