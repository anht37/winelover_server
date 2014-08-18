<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 16:42
 */

class Device extends Eloquent
{
    protected $table = 'devices';
    const IOS = 1;
    const ANDROID = 0;
    protected $fillable = array('auth_key', 'device_id', 'platform');

    public static function push_notification($input) {
        $error_code = ApiResponse::OK;
        $validator = Validator::make(
            $input,
            array(
                'auth_key' => 'required',
                'device_id' => 'required',
                'platform' => 'required'
            )
        );
        //validate params
        if ($validator->fails()) {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }else {
            //check device existed
            if (Device::where('auth_key', $input['auth_key'])->first() != null) {
                $error_code = ApiResponse::EXISTED_DEVICE;
                $data = ApiResponse::getErrorContent(ApiResponse::EXISTED_DEVICE);
            } else {
                $device = Device::create($input);
                if($device){
                    $data = "ok";
                }

            }
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function message_push_notification($device_token)
    {
        $error_code = ApiResponse::OK;       
        $devices = PushNotification::DeviceCollection(array(
            PushNotification::Device($device_token, array('badge' => 5)),
        ));
        $message = PushNotification::Message('This is message test',array(
            'badge' => 1,

            'actionLocKey' => 'Action button title!',
            'locKey' => 'localized key',
            'custom' => array('custom data' => array(
                'we' => 'want', 'send to app'
            ))
        ));
        $data = PushNotification::app('YineLover')
                ->to($devices)
                ->send($message);
        return $data;

    } 
}