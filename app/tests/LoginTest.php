<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class LoginTest extends TestCase
{

    protected $params;

    public function __construct()
    {
        parent::__construct();
        $this->params = array(
            'email' => 'test@gmail.com',
            'password' => '123456',
        );
    }

    //test cases for Login by email - password successfully
    public function testLoginByEmailSuccess()
    {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', $this->params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $login_infor = Login::where('user_id', $user->user_id)->first();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => "000", "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    public function testLoginByFacebookExistedUserSuccess() {
        $_params = $this->params;
        $_params['fb_id'] = '123456';
        $user = new User();
        $user->email = $_params['email'];
        $user->password = $_params['password'];
        $user->fb_id = $_params['fb_id'];
        $user->save();
        $response = $this->call('POST', 'api/login', $this->params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $login_infor = Login::where('user_id', $user->user_id)->first();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => "000", "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    public function testLoginByFacebookNewUserSuccess() {
        $_params = $this->params;
        $_params['fb_id'] = '123456';
        $response = $this->call('POST', 'api/login', $this->params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $login_infor = Login::where('fb_id', $_params['fb_id'])->first();
        $this->assertNotNull($login_infor);
        $this->assertEquals(json_encode(array("code" => "000", "data" =>
            array(
                "session" => $login_infor->session_id,
                "user_id" => $login_infor->user_id
            )
        )), $response->getContent());
    }

    public function testLoginByEmailErrorWrongPassword() {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', array(
            'email' => $this->params['email'],
            'password' => 'wrongpassword'
        ));
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "107", "data" => "Email or password is wrong")), $response->getContent());
    }

    public function testLoginByEmailErrorWrongEmail() {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', array(
            'email' => 'wrongemail@gmail.com',
            'password' => $this->params['password']
        ));
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "107", "data" => "Email or password is wrong")), $response->getContent());
    }

    public function testLoginByEmailErrorNoEmail() {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', array(
            'email' => '',
            'password' => $this->params['password']
        ));
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "102", "data" =>
            array(
                'email' => '',
                'password' => $this->params['password']
            )
        )), $response->getContent());
    }

    public function testLoginByEmailErrorNoPassword() {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', array(
            'email' => '',
            'password' => $this->params['password']
        ));
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "102", "data" =>
            array(
                'email' => $this->params['email'],
                'password' => ''
            )
        )), $response->getContent());
    }

}