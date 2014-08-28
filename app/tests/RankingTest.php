<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class RankingTest extends ApiTestCase
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
        $this->_uri = 'api/ranking';
        $this->_models = array('Follow', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();
        $this->setUpProfile();

        $user = new User();
        $user->email = 'test_1@gmail.com';
        $user->password = '123456';
        $user->save();
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;

        $profile = new Profile();
        $profile->user_id = $follow_id;
        $profile->follower_count = '13';
        $profile->following_count = '2';
        $profile->rate_count = '32';
        $profile->comment_count = '12';
        $profile->scan_count = '8';
        $profile->last_name = 'pro3';
        $profile->first_name = 'user login';
        $profile->save();

        $follow = new Follow();
        $follow->id = 1;
        $follow->from_id = $this->_user_id;
        $follow->to_id = $follow_id;
        $follow->save();
        
    }

    public function testGetRankingSuccess()
    {
        $user_id = $this->_user_id;
        $_params = $this->_params;
        $_params['user_id'] = $user_id;
        $response = $this->_getAuth($_params);
        //get created login information
        
        $page = 1;
        $limit = 10;

        $user = Profile::orderBy('rate_count', 'desc')->forPage($page, $limit)->get();

        if(count($user) != 0) {
            foreach ($user as $users) {
                $follow = Follow::where('from_id', $user_id)->where('to_id', $users->user_id)->first();
                if($follow) {
                        $users->is_follow = true;
                    } else {
                        if($users->user_id != $user_id) {
                            $users->is_follow = false;
                        }
                    }
                if($users->image != null) {
                    $users->image = URL::asset($users->image);
                }
            }
        }

        $this->assertNotNull($user);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $user->toArray())
        , json_decode($response->getContent(), true));
    }
}