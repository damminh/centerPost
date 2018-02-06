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
    'namespace' => 'users',
    'prefix' => 'users'
], function() {
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => 'api.web:1'], function () {
        Route::resource('members', 'MemberController', ['only' => ['index', 'store', 'update', 'destroy']]);
        // Route::resource('groups', 'GroupController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::resource('domains', 'DomainController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::resource('posts', 'PostController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::resource('requirements', 'RequirementController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::get('types', 'TypeController@index');
        Route::get('posts/{id}', 'PostController@index_one');
        Route::get('requirements/{id}', 'RequirementController@index_one');
        Route::get('reports', 'DashBoardController@report');
    });
});

//member
Route::group([ 
    'namespace' => 'members',
    'prefix' => 'members'
], function() {
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => 'api.web:2'], function () {
        Route::resource('posts', 'PostController', ['only' => ['index', 'store', 'udpate']]);
        Route::get('requirements', 'RequirementController@index');
        Route::get('types', 'TypeController@index');
        Route::get('posts/{id}', 'PostController@index_one');
        Route::get('requirements/{id}', 'RequirementController@index_one');
    });
});

Route::get('tests', 'TestController@index');