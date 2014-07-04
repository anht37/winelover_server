<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class LoginTest extends TestCase
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
        );
        $this->_method = 'POST';
        $this->_uri = 'api/login';
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

    public function setUp()
    {
        parent::setUp();
        $this->resetEvents();
        $user = new User();
        $user->email = $this->_params['email'];
        $user->password = $this->_params['password'];
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


    //test cases for Login by email - password successfully
    public function testLoginByEmailSuccess()
    {
        $response = $this->_getResponse();
        //get created login information
        $login_infor = Login::all()->last();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    //test case for Login by Facebook ID successfully
    public function testLoginByFacebookExistedUserSuccess()
    {
        $params = $this->_params;
        $params['fb_id'] = '123456';
        $response = $this->_getResponse($params);

        //get created login information
        $login_infor = Login::all()->last();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    public function testLoginByFacebookNewUserSuccess()
    {
        $_params = $this->_params;
        $_params['fb_id'] = '1234567';
        $response = $this->_getResponse($_params);

        $login_infor = Login::all()->last();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    public function testLoginByEmailErrorWrongPassword()
    {
        $_params = $this->_params;
        $_params['password'] = 'wrong_password';
        $response = $this->_getResponse($_params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::WRONG_AUTH, "data" => ApiResponse::getErrorContent(ApiResponse::WRONG_AUTH))), $response->getContent());
    }

    public function testLoginByEmailErrorWrongEmail()
    {
        $_params = $this->_params;
        $_params['email'] = 'wrong_email@gmail.com';
        $response = $this->_getResponse($_params);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::WRONG_AUTH, "data" => ApiResponse::getErrorContent(ApiResponse::WRONG_AUTH))), $response->getContent());
    }

    public function testLoginByEmailErrorNoEmail()
    {
        $_params = $this->_params;
        $_params['email'] = '';
        $response = $this->_getResponse($_params);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" =>
            array(
                'email' => '',
                'password' => $this->_params['password'],
            )
        )), $response->getContent());
    }

    public function testLoginByEmailErrorNoPassword()
    {
        $_params = $this->_params;
        $_params['password'] = '';

        $response = $this->_getResponse($_params);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS, "data" =>
            array(
                'email' => $this->_params['email'],
                'password' => '',
            )
        )), $response->getContent());
    }

}