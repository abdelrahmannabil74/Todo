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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('register', 'RegisterController@register');

    Route::post('tasks/create','TaskController@store')->name('createTask');
    Route::get('tasks','TaskController@index')->name('listTasks');

    Route::put('tasks/update/{task}','TaskController@update')->name('updateTask');
    Route::delete('tasks/delete/{task}','TaskController@destroy')->name('deleteTask');
    Route::put('tasks/toggle/{task}','TaskController@toggle')->name('toggleStatus');

    Route::get('users/profile/{user}','UserController@profile')->name('profile');
    Route::put('users/updateProfile/{user}','UserController@updateProfile')->name('updateProfile');

    Route::get('users/newsFeed','UserController@newsFeed')->name('newsFeed');
    Route::get('users/search','UserController@search')->name('search');

    Route::post('users/{user}/tasks/{task}/invite','UserController@invite')->name('inviteUser');
    Route::post('tasks/{task}/watch','UserController@watch')->name('watchTask');

    Route::post('/tasks/reminder','UserController@reminder')->name('reminder');

    Route::put('/tasks/{task}/accept','UserController@accept')->name('acceptTask');

    Route::put('/tasks/{task}/reject','UserController@reject')->name('rejectTask');



