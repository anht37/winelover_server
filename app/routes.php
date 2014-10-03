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
    Route::resource('user', 'UserController');
    
    Route::get('test','WineController@create_rating_from_wine_selected');

    // Route::get('read', function()
    //     {
    //         DB::table('wines2')->truncate();
    //         $file_list = array();
    //         $file_folder = app_path() . "/wine_name.txt";
    //         $myfile = fopen($file_folder, "r") or die("Unable to open file!");
    //         $read_file = fread($myfile,filesize($file_folder));
    //         $change_file = str_replace(".PNG",".png",$read_file);
    //         $wine_names = explode('.png'. "\n", $change_file);

    //         foreach ($wine_names as $wine_name) {
    //             if(strlen($wine_name) > 10) {
    //                  $rakuten_wine = explode( '_', $wine_name);
                
    //                 $file_list[] = array(
    //                     "rakuten_id" => "rakuten_" . $rakuten_wine[0] . "_" . $rakuten_wine[1],
    //                 );
    //                 //$file_list[] = $rakuten_wine;
    //             } 
    //         }
    //         dd($file_list);
    //         // foreach ($file_list as $file) {
    //         //     Wine2::create($file);
    //         // }
    //     });

    Route::group(array('before' => 'session'), function()
    {

        Route::resource('wine', 'WineController');
        //Route::post('wine/wine_related', 'WineController@get_wine_related');
        Route::post('scan', 'WineController@scan');
        Route::resource('winenote', 'WinenoteController');
        Route::resource('winery', 'WineryController');
        
        Route::resource('rating', 'RatingController');
        Route::delete('mywine/{rating_id}', 'RatingController@remove');
        Route::get('total_rate/{user_id}','RatingController@get_total_rate_of_number_rate');
        Route::get('activity','RatingController@get_activity');

        Route::post('wine/search', 'WineController@search');
        Route::post('wine/{wine_unique_id}', 'WineController@upload_image_wine_scan');
        Route::post('list_wines','WineController@get_list_wine_from_rakuten_id');
        //Route::post('rating_wine','WineController@create_rating_from_wine_selected');

        Route::resource('like', 'LikeController');

        Route::get('comment/{rating_id}', 'CommentController@index');
        Route::post('comment/{rating_id}', 'CommentController@store');
        Route::put('comment/{rating_id}/{id}', 'CommentController@update');
        Route::get('comment/{rating_id}/{id}', 'CommentController@show');
        Route::delete('comment/{rating_id}/{id}', 'CommentController@destroy');

        Route::resource('follow', 'FollowController');
        Route::get('list_followers/{user_id}', 'FollowController@get_list_follower');
        Route::get('list_followings/{user_id}', 'FollowController@get_list_following');

        Route::resource('wishlist', 'WishlistController');
        Route::resource('profile', 'ProfileController');
        Route::post('profile/{user_id}', 'ProfileController@upload_image');
        Route::get('profile/basic/{user_id}', 'ProfileController@get_profile_basic_user');
        Route::get('profile/wishlist/{user_id}', 'ProfileController@get_profile_wishlist_user');
        Route::get('profile/top_rate/{user_id}', 'ProfileController@get_profile_Top_rate');
        Route::get('profile/last_rate/{user_id}', 'ProfileController@get_profile_Last_rate');
        
        Route::get('timeline', 'RatingController@timeline');
        
        Route::get('feature_users', 'UserController@feature_users');
        Route::post('message_push_notification', 'UserController@message_push_notification');
        Route::post('user/friend_fb','UserController@get_friend_fb');
        Route::post('user/search','UserController@search_user_from_name');
        Route::post('user/friend_tw','UserController@get_friend_tw');
        
        Route::get('ranking','UserController@get_ranking');

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
