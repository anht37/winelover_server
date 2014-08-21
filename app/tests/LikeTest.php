<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class LikeTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'id' => '2',
            'user_id' => '',
            'rating_id' => '1',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/like';
        $this->_models = array('Like', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();
        $like = new Like();
        $like->id = 1;
        $like->user_id = $this->_user_id;
        $like->rating_id = 1;
        $like->save();
        
    }
    public function testGetListLikeSuccess()
    {
        $response = $this->call('GET', 'api/like');

        $like_infor = Like::where('user_id', $this->_user_id)->get();;
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $like_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testGetListLikeSuccessNoLike()
    {
        $like = Like::destroy(1);
        $response = $this->call('GET', 'api/like');

        $like_infor = Like::where('user_id', $this->_user_id)->get();;
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $like_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateLikeSuccess()
    {
        $like = Like::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $like_infor = Like::get(array('user_id','rating_id', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($like_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $like_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateLikeErrorWrongRating()
    {
        $like = Like::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['rating_id'] = "wrong_rating_id";

        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testCreateLikeErrorDuplicatedLike()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $response = $this->_getAuth($_params);
        //get created login information
        $like_infor = Like::get(array('user_id','rating_id', 'updated_at', 'created_at','id'))->last();

        $this->assertNotNull($like_infor);
        $this->assertEquals(array("code" => ApiResponse::DUPLICATED_LIKE, "data" =>  ApiResponse::getErrorContent(ApiResponse::DUPLICATED_LIKE))
        , json_decode($response->getContent(), true)); 
    }

    public function testDeleteLikeSuccess()
    {
        $response = $this->action('delete', 'LikeController@destroy', array('rating_id' => 1));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Like deleted")
         , json_decode($response->getContent(), true));
    }

    public function testDeleteLikeWrongRating()
    {
        $response = $this->action('delete', 'LikeController@destroy', array('rating_id' => "wrong_rating_id"));
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testDeleteLikeErrorNoLike()
    {
        $like = Like::destroy(1);
        $response = $this->action('delete', 'LikeController@destroy', array('rating_id' => 1));
        $this->assertEquals(array("code" => ApiResponse::NOT_EXISTED_LIKE, "data" => ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_LIKE))
         , json_decode($response->getContent(), true));
    }
}