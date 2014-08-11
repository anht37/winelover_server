<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WineryTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'brand_name' => 'Sweet Lips',
            'country_id' => '1',
            'region' => 'Spain',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/winery';
        $this->_models = array('Winery', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpCountry();
    }

    public function testGetListWinerySuccess()
    {
       $response = $this->call('GET','api/winery');
       $winery_infor = Winery::all();   
       $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    
    public function testGetListWineryErrorNoWinery()
    {
        $winery_destroy = Winery::destroy(1);
        $response = $this->call('GET','api/winery');
        $winery_infor = Winery::all();   
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
    }   

    public function testGetWineryDetailSuccess()
    {
        $response = $this->call('GET','api/winery/1');
        $winery_infor = Winery::where('id',1)->first();   
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetWineryDetailErrorNoWinery() 
    {
        $winery_destroy = Winery::destroy(1);
        $response = $this->call('GET','api/winery/1');
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinerySuccess()
    {
        
        $_params = $this->_params;
        $response = $this->_getAuth($_params);
        $winery_infor = Winery::get(array('brand_name','country_id', 'region', 'updated_at', 'created_at','id'))->last();   
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinerySuccessWrongCountryId()
    {
        
        $_params = $this->_params;
        $_params['country_id'] = "wrong_country_id";
        $response = $this->_getAuth($_params);
        $winery_infor = Winery::get(array('brand_name','country_id', 'region', 'updated_at', 'created_at','id'))->last();   
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWineryErrorMissingBrandName()
    {
        $_params = $this->_params;
        unset($_params['brand_name']);
        $response = $this->_getAuth($_params);
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }
    
    public function testUpdateWinerySuccess()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'WineryController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $winery_infor = Winery::where('id', 1)->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
       
    }

    public function testUpdateWinerySuccessWrongCountryId()
    {
        $_params = $this->_params;
        $_params['country_id'] = "wrong_country_id";
        $response = $this->action('POST', 'WineryController@update', array('id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $winery_infor = Winery::where('id', 1)->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winery_infor->toArray())
        , json_decode($response->getContent(), true));
       
    }

    public function testUpdateWineryErrorNoWinery()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'WineryController@update', array('id' => 2), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))
        , json_decode($response->getContent(), true));
       
    }

    public function testDeleteWinerySuccess()
    {
       $response = $this->action('delete', 'WineryController@destroy', array('id' => 1));
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => 'Winery deleted')
        , json_decode($response->getContent(), true));
    }

    public function testDeleteWineryErrorNoWinery()
    {
        $response = $this->action('delete', 'WineryController@destroy', array('id' => 2));
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))
        , json_decode($response->getContent(), true));
    }

}