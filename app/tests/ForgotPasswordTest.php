<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 02/07/2014
 * Time: 16:06
 */
class ForgotPasswordTest extends TestCase
{
    protected $_params;

    protected $_method;
    protected $_uri;

    public function __construct()
    {
        parent::__construct();
        $this->_params = array(
            'email' => 'test@gmail.com'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/forgot_password';
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetEvents();
        $user = new User();
        $user->email = $this->_params['email'];
        $user->password = '123456';
        $user->fb_id = '123456';
        $user->save();
    }

    public function tearDown()
    {
        User::truncate();
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


    private function _getResponse($params = null)
    {
        if ($params) {
            $response = $this->call($this->_method, $this->_uri, $params);
        } else {
            $response = $this->call($this->_method, $this->_uri, $this->_params);
        }
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
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