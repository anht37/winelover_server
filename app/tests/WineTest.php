<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WineTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'wine_id' => '2',
            'name' => 'Wine_2',
            'year' => '2009',
            'winery_id' => '1',
            'wine_unique_id' => '',
            'average_price' => '2',
            'average_rate' => '1'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/wine';
        $this->_models = array('Wine', 'User', 'Login');
    }
    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
    }
    public function testCreateWineSuccess()
    {
        $_params = $this->_params;
        // $_params['wine_unique_id'] = $_params['wine_id']."_".$_params['year']; 
        $response = $this->_getAuth($_params);
        //get created login information
        $wine_infor = Wine::get(array('name','wine_unique_id','year','winery_id', 'average_price', 'average_rate', 'updated_at', 'created_at','wine_id'))->last();
        $this->assertNotNull($wine_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testCreateWineErrorWrongWinery()
    {
        $_params = $this->_params;
        $_params['winery_id'] = "wrong_winery_id"; 
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))
        , json_decode($response->getContent(), true));
    }
    
    public function testCreateWineErrorMissingName()
    {
        $_params = $this->_params;
        unset($_params['name']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }
    public function testCreateWineErrorMissingYear()
    {
        $_params = $this->_params;
        unset($_params['year']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }
    public function testCreateWineErrorMissingWineryId()
    {
        $_params = $this->_params;
        unset($_params['winery_id']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }
    public function testGetListWineSuccess() 
    {
        $response = $this->call('GET', 'api/wine');
        $page = 1;
        $limit = 10;
        $wine_infor =Wine::with('winery')->forPage($page, $limit)->get();
        $error_code = ApiResponse::OK;
        foreach ($wine_infor as $wines) {
            $wines->winery_id = $wines->winery->brand_name;
            if($wines->image_url != null) {
                $wines->image_url = URL::asset($wines->image_url);
            }   
            if($wines->wine_flag != null) {
                $wines->wine_flag = URL::asset($wines->wine_flag);
            } 
        }
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));

            
    }
    public function testGetListWineSuccessNoWine()
    {  
        $wine_infor = Wine::destroy(1);
        $response = $this->call('GET', 'api/wine');

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "")
        , json_decode($response->getContent(), true));
    }

    public function testGetListRatingMyWineWrongPage() 
    {
        $response = $this->call('GET', 'api/wine?page=2');
        $this->assertEquals(array("code" => ApiResponse::URL_NOT_EXIST, "data" =>  ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST))
        , json_decode($response->getContent(), true)); 
    }

    public function testGetWineDetailSuccess()
    {
        $response = $this->call('GET', 'api/wine/1');
        $wine_infor = Wine::where('wine_id', 1)->with('winery')->first();
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));

    }
}