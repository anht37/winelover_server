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
    Route::resource('wine', 'WineController');
    Route::resource('winery', 'WineryController');
    Route::resource('rating', 'RatingController');
    
    Route::get('like/{rating_id}', 'LikeController@index');
    Route::post('like/{rating_id}', 'LikeController@store');
    Route::put('like/{rating_id}/{like}', 'LikeController@update');
    Route::get('like/{rating_id}/{like}', 'LikeController@show');
    Route::delete('like/{rating_id}/{like}', 'LikeController@destroy');

    Route::get('comment/{rating_id}', 'CommentController@index');
    Route::post('comment/{rating_id}', 'CommentController@store');
    Route::put('comment/{rating_id}/{comment}', 'CommentController@update');
    Route::get('comment/{rating_id}/{comment}', 'CommentController@show');
    Route::delete('comment/{rating_id}/{comment}', 'CommentController@destroy');

    //Route::resource('comment', 'CommentController');
});


Route::any('api/(.*)', 'ApiController@display_error');


Route::any('{api_error}', 'ApiController@display_error')->where('api_error', 'api\/.*');



// Route::get('/authtest', function()
// {
//     return View::make('hello');
// });
// Route::group(array('prefix' => 'api/v1'), function()
// {
//     Route::resource('wine', 'WineController');
// });
