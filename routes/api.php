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

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
//    'middleware' => ['auth:api']
], function () {
    Route::get('todos', 'Api\TodoController@index');
    Route::post('todo/create', 'Api\TodoController@store');
    Route::put('todo/update/', 'Api\TodoController@update');
    Route::put('todo/complete/', 'Api\TodoController@complete');
    Route::delete('/todo/delete/{id}', 'Api\TodoController@delete');
    Route::delete('/todos/delete-completed', 'Api\TodoController@deleteCompleted');
});

