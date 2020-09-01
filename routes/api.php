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
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], static function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('sign-up', 'AuthController@signUp')->name('singUp');

        Route::group(['middleware' => 'auth:api'], static function() {
            Route::get('logout', 'AuthController@logout')->name('logout');
            Route::get('user', 'AuthController@user')->name('user');
            Route::put('update', 'AuthController@update')->name('update');
            Route::delete('destroy', 'AuthController@destroy')->name('destroy');
        });
    });

    Route::group(['middleware' => 'auth:api', 'role:root'], static function() {
        Route::resource('users', 'UserController')->except(['create', 'edit']);
    });
});
