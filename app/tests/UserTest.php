<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class UserTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'user_id' => '',
            'text' => 'us',
            'input' => array('10202725915174596'),

        );
        $this->_method = 'POST';
        $this->_uri = 'api/user/search';
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
        $user->fb_id = '10202725915174596';
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

    public function testSearchUserSuccess()
    {
        $data = array();
        $user_id = $this->_user_id;
        $_params = $this->_params;
        $_params['user_id'] = $user_id;
        $response = $this->_getAuth($_params);
        $text = $_params['text'];
        //get created login information
        $user = Profile::where('first_name','LIKE','%'.$text.'%')->orWhere('last_name', 'LIKE', '%'.$text.'%')->get();
        if($user) {
            foreach ($user as $users) {
                $follow = Follow::where('from_id', $user_id)->where('to_id', $users->user_id)->first();
                if($follow) {
                        $users->is_follow = true;
                    } else {
                        $users->is_follow = false;
                    }
                if($users->image != null) {
                    $users->image = URL::asset($users->image);
                }
                if($users->user_id != $user_id) {
                    $data[] = $users->toArray();
                }
            }
        }

        $this->assertNotNull($data);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));
    }

    public function testGetFriend_FbUserSuccess()
    {
        $data = array();
        $user_id = $this->_user_id;
        $_params = $this->_params;
        $_params['user_id'] = $user_id;
        $this->_uri = 'api/user/friend_fb';
        $response = $this->_getAuth($_params['input']);
        $input = $_params['input'];
        //get created login information
       
        foreach ($input as $fb_id) {
            $user = User::where('fb_id', $fb_id)->with('profile')->first();
            if($user && $user->user_id != $user_id) {
                $follow = Follow::where('from_id', $user_id)->where('to_id', $user->user_id)->first();
                if($follow) {
                        $user->is_follow = true;
                    } else {
                        $user->is_follow = false;
                    }
                if($user->image != null) {
                    $user->image = URL::asset($users->image);
                }
                $data[] = $user->toArray();
            }
        }
       
        $this->assertNotNull($data);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));
    }

    public function testGetFriend_FbUserNoInput()
    {
        $data = array();
        $user_id = $this->_user_id;
        $_params = $this->_params;
        $_params['user_id'] = $user_id;
        $this->_uri = 'api/user/friend_fb';
        $response = $this->_getAuth(empty($_params['input']));
       
        $this->assertNotNull($data);
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $data)
        , json_decode($response->getContent(), true));
    }
}