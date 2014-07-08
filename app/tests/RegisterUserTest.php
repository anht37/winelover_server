<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class RegisterUserTest extends TestCase
{

    protected $_params;

    protected $_method;
    protected $_uri;
    public function __construct()
    {
        parent::__construct();
        $this->_params = array(
            'email' => 'test@gmail.com',
            'password' => '123456',
            'device_id' => '123456',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/register';
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetEvents();
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

    private function _getResponse($params = null) {
        $_params = $this->_params;
        if($params !== null) {
            $_params = $params;
        }
        $response = $this->call($this->_method, $this->_uri, array('data' => json_encode($_params)));
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }

    //test case for Register with full information
    public function testRegisterSuccess()
    {
        $response = $this->_getResponse();
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" => ApiResponse::getErrorContent(ApiResponse::OK))), $response->getContent());
    }

    //test case for Register with no email
    public function testRegisterErrorNoEmail()
    {
        $params = $this->_params;
        $params['email'] = "";
        $response = $this->_getResponse($params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $params)), $response->getContent());
    }

    //test case for Register with no password
    public function testRegisterErrorNoPassword()
    {
        $params = $this->_params;
        $params['password'] = "";
        $response = $this->_getResponse($params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $params)), $response->getContent());
    }

    //test case for Register with no DeviceID
    public function testRegisterErrorNoDeviceID()
    {
        $params = $this->_params;
        $params['device_id'] = "";
        $response = $this->_getResponse($params);
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => $params)), $response->getContent());
    }

    //test case for Register with existed email
    public function testRegisterErrorExistedEmail()
    {
        $user = new User();
        $user->email = $this->_params['email'];
        $user->password = $this->_params['password'];
        $user->device_id = $this->_params['device_id'];
        $user->save();
        $response = $this->_getResponse();
        $this->assertEquals(json_encode(array("code" => ApiResponse::EXISTED_EMAIL, "data" => ApiResponse::getErrorContent(ApiResponse::EXISTED_EMAIL))), $response->getContent());
    }
}