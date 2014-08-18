<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class FeatureUserTest extends ApiTestCase
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
        $this->_uri = 'api/feature_users';
        $this->_models = array('User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();

        $user = new User();
        $user->email = 'test_1@gmail.com';
        $user->password = '123456';
        $user->save();
        $other_user1 = User::where('email','test_1@gmail.com')->first()->user_id;

        $user = new User();
        $user->email = 'test_2@gmail.com';
        $user->password = '123456';
        $user->save();
        $other_user2 = User::where('email','test_2@gmail.com')->first()->user_id;

        $profile = new Profile();
        $profile->user_id = $other_user1;
        $profile->follower_count = '4';
        $profile->following_count = '7';
        $profile->rate_count = '6';
        $profile->comment_count = '9';
        $profile->scan_count = '15';
        $profile->last_name = 'pro2';
        $profile->first_name = 'user other 1';
        $profile->save();

        $profile = new Profile();
        $profile->user_id = $other_user2;
        $profile->follower_count = '4';
        $profile->following_count = '7';
        $profile->rate_count = '6';
        $profile->comment_count = '9';
        $profile->scan_count = '15';
        $profile->last_name = 'pro3';
        $profile->first_name = 'user other 2';
        $profile->save();

        $follow = new Follow();
        $follow->from_id = $this->_user_id;
        $follow->to_id = $other_user1;
        $follow->save();

    }

    public function testGetFeatureUserSuccess()
    {

        $user_id = $this->_user_id;
        $_params = $this->_params;
        $_params['user_id'] = $user_id;
        $response = $this->_getAuth($_params);
        $page = 1;
        $limit = 10;
        $user = User::whereNotIn('user_id', [$user_id])->with('profile')->forPage($page, $limit)->get();
        foreach ($user as $users) {
            if ($users->profile->image != null) {
                    $users->profile->image = URL::asset($users->profile->image);   
                }
            $follow = Follow::where('from_id', $user_id)->where('to_id', $users->user_id)->first();
            if($follow) {
                    $users->is_follow = true;
                } else {
                    $users->is_follow = false;
                }
        }
        $data = $user;
        
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
    }
}