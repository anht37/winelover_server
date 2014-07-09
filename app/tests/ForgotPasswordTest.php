<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 02/07/2014
 * Time: 16:06
 */
class ForgotPasswordTest extends ApiTestCase
{

    public function setUp()
    {
        $this->_params = array(
            'email' => 'test@gmail.com'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/forgot_password';
        $this->_models = array('User');
        parent::setUp();
        $user = new User();
        $user->email = $this->_params['email'];
        $user->password = '123456';
        $user->fb_id = '123456';
        $user->save();
    }

    public function testForgotPasswordSuccess()
    {
        $response = $this->_getResponse();
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>  "ok")), $response->getContent());
    }

    public function testForgotPasswordErrorEmailNotExisted()
    {
        $response = $this->_getResponse(array('email' => '123@gmail.com'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::NOT_EXISTED_EMAIL, "data" => ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_EMAIL))), $response->getContent());
    }

    public function testForgotPasswordErrorEmptyEmail()
    {
        $response = $this->_getResponse(array('email' => ''));
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => array('email' => ''))), $response->getContent());
    }

    public function testForgotPasswordErrorMissingEmail()
    {
        $response = $this->_getResponse(array());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" => array())), $response->getContent());
    }

} 