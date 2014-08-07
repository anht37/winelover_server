<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class CreateRatingTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'user_id' => '',
            'wine_unique_id' => '1_2009',
            'rate' => '3.5',
            'comment_count' => '3',
            'like_count' => '2',
            'is_my_wine' => '1'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/rating';
        $this->_models = array('Rating', 'User', 'Login');
    }
    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
    }
    public function testCreateRatingSuccess()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $rating_infor = Rating::get(array('user_id','wine_unique_id','rate','comment_count', 'like_count', 'is_my_wine', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($rating_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $rating_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testCreateRatingErrorWrongWine()
    {
        $_params = $this->_params;
        $_params['wine_unique_id'] = 'wine_not_available';
        $response = $this->_getAuth($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorWrongRate()
    {
        $_params = $this->_params;
        $_params['rate'] = 'wrong_rate';
        $response = $this->_getAuth($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorWrongCommentCount()
    {
        $_params = $this->_params;
        $_params['comment_count'] = 'wrong_comment_count';
        $response = $this->_getAuth($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorWrongLikeCount()
    {
        $_params = $this->_params;
        $_params['like_count'] = 'wrong_like_count';
        $response = $this->_getAuth($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorWrongIsMyWine()
    {
        $_params = $this->_params;
        $_params['is_my_wine'] = 'wrong_is_my_wine';
        $response = $this->_getAuth($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }

    public function testCreateRatingErrorNoWine()
    {
        $_params = $this->_params;
        unset($_params['wine_unique_id']);        
        $response = $this->_getAuth($_params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params
        )), $response->getContent());
    }

    // public function testUpdateRatingError()
    // {
    //     $_params = $this->_params;
    //     $response = $this->_getAuth($_params);
    //     dd($response);
    //     $rating_infor = Rating::get(array('user_id','wine_unique_id','rate','comment_count', 'like_count', 'is_my_wine', 'updated_at', 'created_at','id'))->last();
    //     $this->assertNotNull($rating_infor);
    //     $this->assertEquals(
    //         array("code" => ApiResponse::OK, "data" => $rating_infor->toArray())
    //     , json_decode($response->getContent(), true));
    // }

}