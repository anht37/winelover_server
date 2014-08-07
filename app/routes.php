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
    
    Route::group(array('before' => 'session'), function()
    {

        Route::resource('wine', 'WineController');
        Route::resource('winenote', 'WinenoteController');
        Route::resource('winery', 'WineryController');
        Route::resource('rating', 'RatingController');
        Route::delete('mywine/{rating_id}', 'RatingController@remove');
        Route::resource('like', 'LikeController');

        Route::get('comment/{rating_id}', 'CommentController@index');
        Route::post('comment/{rating_id}', 'CommentController@store');
        Route::put('comment/{rating_id}/{id}', 'CommentController@update');
        Route::get('comment/{rating_id}/{id}', 'CommentController@show');
        Route::delete('comment/{rating_id}/{id}', 'CommentController@destroy');

        Route::resource('follow', 'FollowController');
        Route::resource('wishlist', 'WishlistController');
        Route::resource('profile', 'ProfileController');
        Route::get('profile/basic/{user_id}', 'ProfileController@getProfile_basic_user');
        Route::get('profile/wishlist/{user_id}', 'ProfileController@getProfile_wishlist_user');
        Route::get('profile/top_rate/{user_id}/{per_page}', 'ProfileController@getProfile_Top_rate');
        
        Route::get('timeline', 'UserController@timeline');
        Route::get('feature_users', function()
        {
            
            $error_code = ApiResponse::OK;
            $pagination = ApiResponse::pagination();
            $page = $pagination['page'];
            $limit = $pagination['limit'];
            $user = User::forPage($page, $limit)->get();
            if(count($user) == 0) {
                if($page == 1) {
                    $data = "";
                } else {
                    $error_code = ApiResponse::URL_NOT_EXIST;
                    $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
                }   
            } else {
                $data = $user->toArray();
            }
            return Response::json(array("code" => $error_code, "data" => $data));

        });

    });
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
