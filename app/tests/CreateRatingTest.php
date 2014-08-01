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
    protected function _getResponse ($params = null)
    {
        $_params = $this->_params;
        if($params !== null) {
            $_params = $params;
        }
        $response = $this->call($this->_method, $this->_uri, array('data' => json_encode($_params)), array(), array("HTTP_session"=> $this->_session));
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }
    public function setUp()
    {
        parent::setUp();
        $this->_session = '3f9a362bb40714f77cadfd9f5b9d801b';
        $this->_user_id = Login::where('session_id', $session)->first()->user_id;
        $this->_params = array(
            'wine_unique_id' => '10_2009',
            'rate' => '3.5',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/rating';
        $this->_models = array('Rating');

        $rating = new Rating();
        $rating->user_id = $this->_user_id;
        $rating->wine_unique_id = $this->_params['wine_unique_id'];
        $rating->rate = $this->_params['rate'];
        $rating->save();
    }
    public function testCreateRatingSuccess()
    {
        $response = $this->_getResponse();
        //get created login information
        $rating_infor = Rating::all()->last();
        $this->assertNotNull($rating_infor);
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>
            array(
                "user_id" => $this->_user_id;
                "wine_unique_id" => $rating_infor->wine_unique_id,
                "rate" => $rating_infor->rate,
            )
        )), $response->getContent());
    }
    public function testCreateRatingErrorWrongWine()
    {
        $_params = $this->_params;
        $_params['wine_unique_id'] = 'wine_not_available';
        $response = $this->_getResponse($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorWrongRate()
    {
        $_params = $this->_params;
        $_params['rate'] = 'wrong_rate';
        $response = $this->_getResponse($_params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))), $response->getContent());
    }
    public function testCreateRatingErrorNoWine()
    {
        $_params = $this->_params;
        unset($_params['wine_unique_id']);        
        $response = $this->_getResponse($_params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" =>
            array(
                'wine_unique_id' => '',
            )
        )), $response->getContent());
    }
}