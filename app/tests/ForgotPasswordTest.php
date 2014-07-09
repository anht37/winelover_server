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
        $this->assertEquals(json_encode(array("code" => "000", "data" => "New password has sent to your email")), $response->getContent());
    }

    public function testForgotPasswordErrorEmailNotExisted()
    {
        $response = $this->_getResponse(array('email' => '123'));
        $this->assertEquals(json_encode(array("code" => "108", "data" => "Email is not existed")), $response->getContent());
    }

    public function testForgotPasswordErrorEmptyEmail()
    {
        $response = $this->_getResponse(array('email' => ''));
        $this->assertEquals(json_encode(array("code" => "102", "data" => array('email' => ''))), $response->getContent());
    }

    public function testForgotPasswordErrorMissingEmail()
    {
        $response = $this->_getResponse(array());
        $this->assertEquals(json_encode(array("code" => "102", "data" => array('email' => ''))), $response->getContent());
    }

} 