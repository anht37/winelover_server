<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class ProfileTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'user_id' => '',
        );
        $this->_method = 'GET';
        $this->_uri = 'api/profile/';
        $this->_models = array('Profile', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpProfile();

        $user = new User();
        $user->email = 'test_1@gmail.com';
        $user->password = '123456';
        $user->save();
        $other_user = User::where('email','test_1@gmail.com')->first()->user_id;

        $profile = new Profile();
        $profile->user_id = $other_user;
        $profile->follower_count = '4';
        $profile->following_count = '7';
        $profile->rate_count = '6';
        $profile->comment_count = '9';
        $profile->scan_count = '15';
        $profile->last_name = 'pro2';
        $profile->first_name = 'user other 1';
        $profile->save();

        $follow = new Follow();
        $follow->from_id = $this->_user_id;
        $follow->to_id = $other_user;
        $follow->save();
    }

    public function testGetProfileBasicSuccessUserLogin()
    {
        
        $user_id = $this->_user_id;
        $response = $this->action('GET', 'ProfileController@get_profile_basic_user', array('user_id' => $user_id));

        $profile = Profile::where('user_id', $user_id)->first();

        if(User::where('user_id',$user_id)->first()){
            $users = Profile::orderBy('rate_count', 'desc')->get();
            $i = 0;
            if ($users) {
                foreach ($users as $key) {
                    $i ++;
                    if($key['user_id'] == $user_id) {
                        break;
                    }
                }
            }
        }
        $profile->user_ranking = $i;
        if ($profile->image != null) {
            $profile->image = URL::asset($profile->image);   
        }
        $wishlist = Wishlist::where('user_id', $user_id)->get();
        if($wishlist){
            $profile->wishlist_count = count($wishlist);
        } else {
            $profile->wishlist_count = 0;
        }
        $data = $profile;
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileBasicSuccessOtherUser()
    {
        $user_id = $this->_user_id;
        $other_user = User::where('email','test_1@gmail.com')->first()->user_id;
        $response = $this->action('GET', 'ProfileController@get_profile_basic_user', array('user_id' => $other_user));
        
        $profile = Profile::where('user_id', $other_user)->first();
        
        if(User::where('user_id',$other_user)->first()){
            $users = Profile::orderBy('rate_count', 'desc')->get();
            $i = 0;
            if ($users) {
                foreach ($users as $key) {
                    $i ++;
                    if($key['user_id'] == $other_user) {
                        break;
                    }
                }
            }
        }
        $profile->user_ranking = $i;
        if ($profile->image != null) {
            $profile->image = URL::asset($profile->image);   
        }
        $wishlist = Wishlist::where('user_id', $user_id)->get();
        $profile->wishlist_count = count($wishlist);
        $follow = Follow::where('from_id', $user_id)->where('to_id', $other_user)->first();
        if($follow) {
            $profile->is_follow = true;
        } else {
            $profile->is_follow = false;
        }
        $data = $profile;

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileBasicErrorWrongUser()
    {
        $user_id = "wrong_user_id";
        $response = $this->action('GET', 'ProfileController@get_profile_basic_user', array('user_id' => $user_id));

        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileWishlishSucsses()
    {
        $this->setUpWishlist();
        $user_id = $this->_user_id;
        $response = $this->action('GET', 'ProfileController@get_profile_wishlist_user', array('user_id' => $user_id));


        $page = 1;
        $limit = 10;
    
        $profile = Profile::where('user_id', $user_id)->first();
    
        if ($profile->image != null) {
            $profile->image = URL::asset($profile->image);   
        }
        $wishlist = Wishlist::where('user_id', $user_id)->with('wine')->forPage($page, $limit)->get();

        foreach ($wishlist as $wishlists) {
           $wishlists->winery = Winery::where('id', $wishlists->wine->winery_id)->first()->toArray();

           if($wishlists->wine->image_url != null) {
               $wishlists->wine->image_url = URL::asset($wishlists->wine->image_url);
           }

           if($wishlists->wine->wine_flag != null) {
               $wishlists->wine->wine_flag = URL::asset($wishlists->wine->wine_flag);
           } 
        }
       
        $data = $wishlist;

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileWishlishErrorWrongUser()
    {
        $user_id = "wrong_user_id";
        $response = $this->action('GET', 'ProfileController@get_profile_wishlist_user', array('user_id' => $user_id));

        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileWishlishNoWishlist()
    {
        $user_id = $this->_user_id;
        $response = $this->action('GET', 'ProfileController@get_profile_wishlist_user', array('user_id' => $user_id));

        $wishlist = Wishlist::where('user_id', $user_id)->with('wine')->get();
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wishlist->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileTopRateSucess()
    {
        $this->setUpRating();
        $user_id = $this->_user_id;
        $per_page = 10;
        $page = 1;
        $response = $this->action('GET', 'ProfileController@get_profile_Top_rate', array('user_id' => $user_id));

        $top_rate = Rating::where('user_id',$user_id)->orderBy('rate', 'desc')->with('wine')->forPage($page, $per_page)->get();
        foreach ($top_rate as $top_rates) {
            $top_rates->winery = Winery::where('id',$top_rates->wine->winery_id)->first()->toArray();
            if($top_rates->wine->image_url != null) {
                $top_rates->wine->image_url = URL::asset($top_rates->wine->image_url);
            }

            if($top_rates->wine->wine_flag != null) {
                $top_rates->wine->wine_flag = URL::asset($top_rates->wine->wine_flag);
            } 
        }
        $data = $top_rate;

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }
    
    public function testGetProfileTopRateErrorWrongUserId()
    {
        $this->setUpRating();
        $user_id = "wrong_user_id";
        $per_page = 10;
        $page = 1;
        $response = $this->action('GET', 'ProfileController@get_profile_Top_rate', array('user_id' => $user_id));

        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

    public function testGetProfileLastRateSucess()
    {
        $this->setUpRating();
        $user_id = $this->_user_id;
        $per_page = 10;
        $page = 1;
        $response = $this->action('GET', 'ProfileController@get_profile_Last_rate', array('user_id' => $user_id));

        $last_rate = Rating::where('user_id',$user_id)->orderBy('updated_at', 'desc')->with('wine')->forPage($page, $per_page)->get();
        foreach ($last_rate as $last_rates) {
            $last_rates->winery = Winery::where('id',$last_rates->wine->winery_id)->first()->toArray();
            if($last_rates->wine->image_url != null) {
                $last_rates->wine->image_url = URL::asset($last_rates->wine->image_url);
            }

            if($last_rates->wine->wine_flag != null) {
                $last_rates->wine->wine_flag = URL::asset($last_rates->wine->wine_flag);
            } 
        }
        $data = $last_rate;

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }
    
    public function testGetProfileLastRateErrorWrongUserId()
    {
        $this->setUpRating();
        $user_id = "wrong_user_id";
        $per_page = 10;
        $page = 1;
        $response = $this->action('GET', 'ProfileController@get_profile_Last_rate', array('user_id' => $user_id));

        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

}