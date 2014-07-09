<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 04/07/2014
 * Time: 13:27
 */

class LogoutTest extends ApiTestCase
{

    public function __construct()
    {
        parent::__construct();
        $this->_params = array(
            'session_id' => '',
        );
        $this->_method = 'POST';
        $this->_uri = ApiResponse::$API_LIST['logout'];
        $this->_models = array('User');
    }


    public function setUp()
    {
        parent::setUp();
        $this->call('POST', ApiResponse::$API_LIST['login'], array('data' => json_encode(array('fb_id' => '123456'))));
        $login = Login::all()->last();
        $this->_params['session_id'] = $login->session_id;
        $this->client->restart();
    }


    public function testLogoutSuccess()
    {
        $response = $this->_getResponse();
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" => ApiResponse::getErrorContent(ApiResponse::OK))), $response->getContent());
    }

    public function testLogoutErrorNoSession()
    {
        $params = array();
        $response = $this->_getResponse($params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $params )), $response->getContent());
    }

    public function testLogoutErrorEmptySession()
    {
        $params = $this->_params;
        $params['session_id'] = "";
        $response = $this->_getResponse($params);

        $this->assertEquals(json_encode(array("code" => ApiResponse::SESSION_INVALID, "data" => ApiResponse::getErrorContent(ApiResponse::SESSION_INVALID) )), $response->getContent());
    }

    public function testLogoutErrorInvalidSession()
    {
        $params = $this->_params;
        $params['session_id'] = "123456";
        $response = $this->_getResponse($params);

        $this->assertEquals(json_encode(array("code" => ApiResponse::SESSION_INVALID, "data" => ApiResponse::getErrorContent(ApiResponse::SESSION_INVALID) )), $response->getContent());
    }
}