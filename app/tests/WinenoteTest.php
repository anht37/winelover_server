<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WinenoteTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'user_id' => '',
            'wine_unique_id' => '1_2009',
            'note' => 'This is note test',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/winenote';
        $this->_models = array('Winenote', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();
        $winenote = new Winenote();
        $winenote->id = 1;
        $winenote->user_id = $this->_user_id;
        $winenote->wine_unique_id = '1_2009';
        $winenote->note = "this is note 1";
        $winenote->save();
        
    }
    public function testGetListWinenoteSuccess()
    {
        $response = $this->call('GET', 'api/winenote');

        $winenote_infor = Winenote::all();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winenote_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testGetListWinenoteSuccessNoWinenote()
    {
        $winenote = Winenote::destroy(1);
        $response = $this->call('GET', 'api/winenote');

        $winenote_infor = Winenote::all();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winenote_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinenoteSuccess()
    {
        $winenote = Winenote::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $winenote_infor = Winenote::get(array('user_id','wine_unique_id', 'note', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($winenote_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winenote_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinenoteErrorWrongWine()
    {
        $winenote = Winenote::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['wine_unique_id'] = "wrong_wine_unique_id";

        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
        , json_decode($response->getContent(), true));
    }
    public function testCreateWinenoteErrorMissingNote()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        unset($_params['note']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinenoteErrorMissingWine()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        unset($_params['wine_unique_id']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testCreateWinenoteErrorDuplicatedWinenote()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $response = $this->_getAuth($_params);
        //get created login information
        $winenote_infor = Winenote::get(array('user_id','wine_unique_id', 'note', 'updated_at', 'created_at','id'))->last();

        $this->assertNotNull($winenote_infor);
        $this->assertEquals(array("code" => ApiResponse::DUPLICATED_WINE_NOTE_ADD, "data" =>  ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WINE_NOTE_ADD))
        , json_decode($response->getContent(), true)); 
    }

    public function testUpdateWinenoteSuccess()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'WinenoteController@update', array('wine_unique_id' => "1_2009"), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $winenote_infor = Winenote::where('user_id', $this->_user_id)->where('wine_unique_id',  "1_2009")->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $winenote_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testUpdateWinenoteErrorWine()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'WinenoteController@update', array('wine_unique_id' => "wrong_wine_unique_id"), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
       $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
        , json_decode($response->getContent(), true));
    }

    public function testUpdateWinenoteErrorNoWinenote()
    {
        $winenote_destroy = Winenote::destroy(1);
        $_params = $this->_params;
        $response = $this->action('POST', 'WinenoteController@update', array('wine_unique_id' => "1_2009"), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
       $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE_NOTE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE))
        , json_decode($response->getContent(), true));
    }

    public function testUpdateWinenoteErrorMissingNote()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        unset($_params['note']);
        $response = $this->action('POST', 'WinenoteController@update', array('wine_unique_id' => "1_2009"), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testDeleteWinenoteSuccess()
    {
        $response = $this->action('delete', 'WinenoteController@destroy', array('wine_unique_id' => "1_2009"));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Wine note is deleted")
         , json_decode($response->getContent(), true));
    }

    public function testDeleteWinenoteErrorNoWinenote()
    {
        $winenote = Winenote::destroy(1);
        $response = $this->action('delete', 'WinenoteController@destroy', array('wine_unique_id' => "1_2009"));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE_NOTE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE))
         , json_decode($response->getContent(), true));
    }
    public function testDeleteWinenoteErrorWrongWine()
    {
        $response = $this->action('delete', 'WinenoteController@destroy', array('wine_unique_id' => "wrong_wine_unique_id"));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
         , json_decode($response->getContent(), true));
    }
}