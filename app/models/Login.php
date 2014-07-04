<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 16:42
 */

class Login extends Eloquent {
    protected $table = 'logins';

    public static function logout($input) {
        $error_code = ApiResponse::OK;
        //validate params
        if (!array_key_exists('session_id', $input)) {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }else {
            //check email existed
            $login_information = self::where('session_id', $input['session_id'])->first();
            if ($login_information == null) {
                $error_code = ApiResponse::SESSION_INVALID;
                $data = ApiResponse::getErrorContent(ApiResponse::SESSION_INVALID);
            } else {
                $login_information->delete();
                $data = "ok";
            }
        }
        return array("code" => $error_code, "data" => $data);
    }


}