<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class UpdateRatingTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            
            'rate' => '0.5',
            'comment_count' => '2',
            'like_count' => '0',
            'is_my_wine' => '0',
        );
        $this->_method = 'PUT';
        $this->_uri = 'api/rating/';
        $this->_models = array('Rating', 'User', 'Login');

    }
    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
    }
    public function testUpdateRatingSuccess()
    {
        $this->setUpRating();
        $_params = $this->_params;
        //dd(json_encode($_params));
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $rating_infor = Rating::where('id', 1)->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $rating_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testUpdateNoRating()
    {  
        $_params = $this->_params;
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }
    public function testUpdateRatingErrorWrongRate()
    {
        $this->setUpRating();
        $_params = $this->_params;
        $_params['rate'] = 'wrong_rate';
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testUpdateRatingErrorWrongCommentCount()
    {
        $this->setUpRating();
        $_params = $this->_params;
        $_params['comment_count'] = 'wrong_comment_count';
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testUpdateRatingErrorWrongLikeCount()
    {
        $this->setUpRating();
        $_params = $this->_params;
        $_params['like_count'] = 'wrong_like_count';
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testUpdateRatingErrorWrongIsMyWine()
    {
        $this->setUpRating();
        $_params = $this->_params;
        $_params['is_my_wine'] = 'wrong_is_my_wine';
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    
    }
    public function testUpdateRatingErrorNoInput()
    {
        $this->setUpRating();
        $_params = $this->_params;
        unset($_params['rate']);    
        unset($_params['comment_count']);    
        unset($_params['like_count']);    
        unset($_params['is_my_wine']);   
        $response = $this->action('POST', 'RatingController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)), $response->getContent());
    
    }
}