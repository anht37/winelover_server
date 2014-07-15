<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});


// Route group for API versioning
Route::group(array('prefix' => 'api'), function()
{
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('logout', 'UserController@logout');
    Route::post('forgot_password', 'UserController@forgot_password');
    Route::post('push_notification', 'UserController@push_notification');
    Route::post('scan', 'WineController@scan');
    Route::resource('user', 'UserController');
});

Route::any('api/(.*)', 'ApiController@display_error');


Route::any('{api_error}', 'ApiController@display_error')->where('api_error', 'api\/.*');
