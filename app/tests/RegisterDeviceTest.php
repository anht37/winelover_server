<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 14:41
 */

class RegisterDeviceTest extends TestCase {

    protected $_params;

    protected $_method;
    protected $_uri;

    public function setUp()
    {
        parent::setUp();
        $this->_params = array(
            'auth_key' => '123456',
            'device_id' => '123456',
            'platform' => Device::IOS,
        );
        $this->_method = 'POST';
        $this->_uri = 'api/push_notification';
    }

    private function _getResponse($params = null) {
        if($params) {
            $response = $this->call($this->_method, $this->_uri, $params);
        }else {
            $response = $this->call($this->_method, $this->_uri, $this->_params);
        }
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }

    //test case for register successfully
    public function testRegisterSucess() {
        $response = $this->_getResponse();
        $device = Device::where('auth_key', $this->_params['auth_key'])
            ->where('device_id',$this->_params['device_id'])
            ->where('platform', $this->_params['platform'])->first();
        $this->assertNotNull($device);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::OK, "data" => ApiResponse::getErrorContent(ApiResponse::OK))), $response->getContent());
    }

    //test case for register existed device
    public function testRegisterDeviceExisted() {
        Device::create($this->_params);
        $response = $this->_getResponse();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::EXISTED_DEVICE,"data" => ApiResponse::getErrorContent(ApiResponse::EXISTED_DEVICE))), $response->getContent());
    }

    //test case for missing auth_key parameter
    public function testRegisterMissingAuthKey() {
        $params = $this->_params;
        unset($params['auth_key']);
        $response = $this->_getResponse();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS,"data" => json_encode($params))), $response->getContent());
    }

    //test case for missing device_id parameter
    public function testRegisterMissingDeviceID() {
        $params = $this->_params;
        unset($params['device_id']);
        $response = $this->_getResponse();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS,"data" => json_encode($params))), $response->getContent());
    }

    //test case for missing platform parameter
    public function testRegisterMissingPlatform() {
        $params = $this->_params;
        unset($params['platform']);
        $response = $this->_getResponse();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => ApiResponse::MISSING_PARAMS,"data" => json_encode($params))), $response->getContent());
    }

}