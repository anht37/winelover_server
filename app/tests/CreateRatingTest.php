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
        );
        $this->_method = 'POST';
        $this->_uri = 'api/rating';
        $this->_models = array('Rating', 'User', 'Login');
    }
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
        $user = new User();
        $user->email = 'testacc@gmail.com';
        $user->password = '123456';
        $user->fb_id = '123456';
        $user->save();
        $this->_user_id = User::where('email','testacc@gmail.com')->first()->user_id;

        $login = new Login();
        $login->id = 1;
        $login->user_id = $this->_user_id ;
        $login->session_id = '3f9a362bb40714f77cadfd9f5b9d801b';
        $login->expired_at = '2019-07-30';
        $login->save();
        
        $this->_session = '3f9a362bb40714f77cadfd9f5b9d801b';
        $this->_user_id = Login::where('session_id', $this->_session)->first()->user_id;

        $winery = new Winery();
        $winery->id = 1;
        $winery->brand_name = 'Winery 1';
        $winery->country_id = '2';
        $winery->region = 'Chile';
        $winery->save();

        $wine = new Wine();
        $wine->wine_id = 1 ;
        $wine->name = 'Wine_1';
        $wine->winery_id = 1;
        $wine->year = '2009';
        $wine->wine_unique_id = '1_2009';
        $wine->average_price = "2200.00";
        $wine->average_rate = "3.5";
        $wine->save();
        $this->session(array('user_id' => $this->_user_id ));
    }
    public function testCreateRatingSuccess()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getResponse($_params);
        //get created login information
        $rating_infor = Rating::get(array('user_id','wine_unique_id','rate', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($rating_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $rating_infor->toArray())
        , json_decode($response->getContent(), true));
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
                'user_id' => '',
                'rate' => '3.5'
            )
        )), $response->getContent());
    }
}