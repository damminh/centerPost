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

//user
Route::group([
    'namespace' => 'users'
], function() {
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => 'api.web:1'], function () {

    });
});

//member
Route::group([
    'namespace' => 'members'
], function() {
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => 'api.web:2'], function () {

    });
});