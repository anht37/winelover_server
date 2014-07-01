<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 14:41
 */

class UserTest extends TestCase {


    //test cases for register function
    public function testRegisterSucess() {
        $params = array('auth_key' => '123','device_id' => '123', 'platform' => Device::IOS);
        $response = $this->call('POST', 'api/register', $params);
        $device = Device::where('auth_key', $params['auth_key'])
            ->where('device_id',$params['device_id'])
            ->where('platform', $params['platform'])->first();
        $this->assertNotNull($device);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "000","data" => "Register Push notification Successful")), $response->getContent());
    }

    public function testRegisterDeviceExisted() {
        $params = array('auth_key' => '123','device_id' => '123', 'platform' => Device::IOS);
        $device = new Device($params);
        $device->save();
        $response = $this->call('POST', 'api/register', $params) ;
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "106","data" => "Device id has existed")), $response->getContent());
    }

    public function testRegisterMissingAuthKey() {
        $params = array('device_id' => '123', 'platform' => Device::IOS);
        $response = $this->call('POST', 'api/register', $params) ;
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "102","data" => json_encode($params))), $response->getContent());
    }

    public function testRegisterMissingDeviceID() {
        $params = array('auth_key' => '123', 'platform' => Device::IOS);
        $response = $this->call('POST', 'api/register', $params) ;
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "102","data" => json_encode($params))), $response->getContent());
    }

    public function testRegisterMissingPlatform() {
        $params = array('auth_key' => '123', 'device_id' => '123');
        $response = $this->call('POST', 'api/register', $params) ;
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(json_encode(array("code" => "102","data" => json_encode($params))), $response->getContent());
    }

}