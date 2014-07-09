<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class RegisterUserTest extends ApiTestCase
{

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
        $this->_models = array('User');
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