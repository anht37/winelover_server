<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class RatingTest extends ApiTestCase
{
    public function __construct()
    {
        parent::__construct();
        $session = Login::all()->last()->session_id;
        $this->call('GET', '/api', array(), array(), array("HTTP_HEADER"=> $session));
        $this->_params = array(
            'wine_unique_id' => '10_2009',
            'rate' => '3.5',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/rating';
        $this->_models = array('Rating');
    }
    //test cases for Login by email - password successfully
    public function testCreateRating()
    {
        $response = $this->_getResponse();
        //get created login information
        $rating_infor = Rating::all()->last();
        $this->assertNotNull($session);
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>
            array(
                "wine_unique_id" => $rating_infor->wine_unique_id,
                "rate" => $rating_infor->rate,
            )
        )), $response->getContent());
    }
}