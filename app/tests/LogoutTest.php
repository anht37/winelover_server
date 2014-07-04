<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 04/07/2014
 * Time: 13:27
 */

class LogoutTest extends TestCase
{

    protected $_params;

    protected $_method;
    protected $_uri;

    public function __construct()
    {
        parent::__construct();
        $this->_params = array(
            'session_id' => '',
        );
        $this->_method = 'POST';
        $this->_uri = ApiResponse::$API_LIST['logout'];
    }


    private function _getResponse($params = null)
    {

        if ($params !== null) {
            $response = $this->call($this->_method, $this->_uri, $params);
        } else {
            $response = $this->call($this->_method, $this->_uri, $this->_params);
        }
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetEvents();
        $this->call('POST', ApiResponse::$API_LIST['login'], array('fb_id' => '123456'));
        $login = Login::all()->last();
        $this->_params['session_id'] = $login->session_id;
        $this->client->restart();
    }

    public function tearDown()
    {
        User::truncate();
        Login::truncate();
    }

    private function resetEvents()
    {
        // Define the models that have event listeners.
        $models = array('User');

        // Reset their event listeners.
        foreach ($models as $model) {

            // Flush any existing listeners.
            call_user_func(array($model, 'flushEventListeners'));

            // Reregister them.
            call_user_func(array($model, 'boot'));
        }
    }


    //test cases for Login by email - password successfully
    public function testLogoutSuccess()
    {
        $response = $this->_getResponse();
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" => ApiResponse::getErrorContent(ApiResponse::OK))), $response->getContent());
    }

    //test case for Login by Facebook ID successfully
    public function testLogoutErrorNoSession()
    {
        $params = $this->_params;
        unset($params['session_id']);
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