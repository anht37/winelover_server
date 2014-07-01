<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */

class LoginTest extends TestCase {

    protected $params;
    public function __construct() {
        parent::__construct();
        $this->params = array(
            'email' => 'test@gmail.com',
            'password' => '123456',
        );
    }

    //test cases for Login by email - password successfully
    public function testLoginSuccess() {
        $user = new User();
        $user->email = $this->params['email'];
        $user->password = $this->params['password'];
        $user->save();
        $response = $this->call('POST', 'api/login', $this->params);
        $this->assertTrue($this->client->getResponse()->isOk());
        $login_infor = Login::where('user_id',$user->user_id)->first();
        $this->assertNotNull($login_infor);
        if($login_infor != null) {
            $this->assertEquals(json_encode(array("code" => "000","data" => json_encode(
                array(
                    "session" => $login_infor->session_id,
                    "user_id" => $login_infor->user_id
                )
            ))), $response->getContent());
        }
    }
}