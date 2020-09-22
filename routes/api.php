<?php

use Illuminate\Http\Request;
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

Route::group(['namespace' => 'Api', 'as' => 'api.'], static function () {
    // Auth routes
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], static function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('social-login', 'AuthController@loginSocialAuth')->name('socialAuth.login');
        Route::post('social-login/{provider}/{frontend}', 'AuthController@redirectToSocialAuth')->name('socialAuth.redirect');
        Route::post('sign-up', 'AuthController@signUp')->name('singUp');

        Route::group(['middleware' => 'auth:api'], static function() {
            Route::get('logout', 'AuthController@logout')->name('logout');
        });
    });

    Route::group(['middleware' => 'auth:api'], static function() {
        Route::group(['middleware' => 'role:root'], static function () {
            // User routes
            Route::resource('users', 'UserController')->except(['create', 'edit']);
            Route::put('users/{user}/role/{roleName}', 'UserController@attachRole')->name('users.role.attach');
            Route::delete('users/{user}/role/{roleName}', 'UserController@detachRole')->name('users.role.detach');
            Route::get('users/{user}/relations', 'UserController@relations')->name('users.relations');
            Route::post('users/{user}/relations/{addressee}', 'UserController@requestRelation')->name('users.relations.request');
            Route::put('users/{user}/relations/{addressee}/accept', 'UserController@acceptRelation')->name('users.relations.accept');
            Route::put('users/{user}/relations/{addressee}/block', 'UserController@blockRelation')->name('users.relations.block');
            Route::delete('users/{user}/relations/{addressee}', 'UserController@destroyRelation')->name('users.relations.destroy');
        });

        // Me User routes
        Route::group(['namespace' => 'Me', 'prefix' => 'me', 'as' => 'me.'], static function () {
            Route::get('/', 'UserController@show')->name('show');
            Route::put('/', 'UserController@update')->name('update');
            Route::delete('/', 'UserController@destroy')->name('destroy');
            Route::get('relations', 'UserController@relations')->name('relations');
            Route::post('relations/{addressee}', 'UserController@requestRelation')->name('relations.request');
            Route::put('relations/{addressee}/accept', 'UserController@acceptRelation')->name('relations.accept');
            Route::put('relations/{addressee}/block', 'UserController@blockRelation')->name('relations.block');
            Route::delete('relations/{addressee}', 'UserController@destroyRelation')->name('relations.destroy');
        });
    });
});
