<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class FollowTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'id' => '2',
            'user_id' => '',
            'follow_id' => '',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/follow';
        $this->_models = array('Follow', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();

        $user = new User();
        $user->email = 'test_1@gmail.com';
        $user->password = '123456';
        $user->save();
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;

        $follow = new Follow();
        $follow->id = 1;
        $follow->from_id = $this->_user_id;
        $follow->to_id = $follow_id;
        $follow->save();
        
    }
    public function testGetListFollowSuccess()
    {
        $response = $this->call('GET', 'api/follow');

        $follow_infor = Follow::where('from_id', $this->_user_id)->get();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $follow_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testGetListFollowSuccessNoFollow()
    {
        $follow = Follow::destroy(1);
        $response = $this->call('GET', 'api/follow');

        $follow_infor = Follow::where('from_id', $this->_user_id)->get();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $follow_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateFollowSuccess()
    {
        $follow = Follow::destroy(1);
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['follow_id'] = $follow_id; 

        $response = $this->_getAuth($_params);
        //get created login information
        $follow_infor = Follow::get(array('from_id','to_id', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($follow_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $follow_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateFollowErrorWrongFollowId()
    {
        $follow = Follow::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['follow_id'] = "wrong_follow_id";

        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

    public function testCreateFollowErrorFollowSelf()
    {
        $follow = Follow::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['follow_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::SELF_FOLLOW_ERROR, "data" => ApiResponse::getErrorContent(ApiResponse::SELF_FOLLOW_ERROR))
        , json_decode($response->getContent(), true));
    }

    public function testCreateFollowErrorDuplicatedFollow()
    {
        $_params = $this->_params;
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['follow_id'] = $follow_id; 
        $response = $this->_getAuth($_params);
        //get created login information
        $follow_infor = Follow::get(array('from_id','to_id', 'updated_at', 'created_at','id'))->last();

        $this->assertNotNull($follow_infor);
        $this->assertEquals(array("code" => ApiResponse::DUPLICATED_FOLLOW, "data" =>  ApiResponse::getErrorContent(ApiResponse::DUPLICATED_FOLLOW))
        , json_decode($response->getContent(), true)); 
    }

    public function testDeleteFollowSuccess()
    {
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;
        $response = $this->action('delete', 'FollowController@destroy', array('follow_id' => $follow_id));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Follow deleted")
         , json_decode($response->getContent(), true));
    }

    public function testDeleteFollowWrongFollowId()
    {
        $response = $this->action('delete', 'FollowController@destroy', array('follow_id' => "wrong_follow_id"));
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_USER, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER))
        , json_decode($response->getContent(), true));
    }

    public function testDeleteFollowErrorNoFollow()
    {
        $follow = Follow::destroy(1);
        $follow_id = User::where('email','test_1@gmail.com')->first()->user_id;
        $response = $this->action('delete', 'FollowController@destroy', array('follow_id' => $follow_id));
        $this->assertEquals(array("code" => ApiResponse::NOT_EXISTED_FOLLOW, "data" => ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_FOLLOW))
         , json_decode($response->getContent(), true));
    }
}