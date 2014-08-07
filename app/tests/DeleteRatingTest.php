<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class DeleteRatingTest extends ApiTestCase
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
    //test cases for Login by email - password successfully
    public function testDeleteNoRating()
    {  
        $response = $this->action('delete', 'RatingController@destroy', array('id' => 1));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testDeleteRatingSuccess() 
    {
        $this->setUpRating();
        $response = $this->action('delete', 'RatingController@destroy', array('id' => 1));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Rating Deleted")
         , json_decode($response->getContent(), true));
    }
    public function testRemoveRatingFromMyWineSuccess() 
    {
        $this->setUpRating();
        $response = $this->action('delete', 'RatingController@remove', array('id' => 1));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Rating is removed from my wine")
         , json_decode($response->getContent(), true));
    }
    public function testRemoveNoRating()
    {  
        $response = $this->action('delete', 'RatingController@remove', array('id' => 1));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

}
