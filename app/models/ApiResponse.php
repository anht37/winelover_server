<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 15:22
 */

class ApiResponse {
    public static $API_LIST = array(
        'login' => 'api/login',
        'logout' => 'api/logout',
        'register' => 'api/register',
    );
    const OK = "0";
    const SERVER_MAINTAIN = "100";
    const SESSION_INVALID = "101";
    const MISSING_PARAMS = "102";
    const UNAVAILABLE_USER = "103";
    const UNAVAILABLE_RATING = "104";
    const UNAVAILABLE_WINE = "105";
    const EXISTED_DEVICE = "106";
    const WRONG_AUTH = "107";
    const NOT_EXISTED_EMAIL = "108";
    const DUPLICATED_LIKE = "109";
    const NOT_EXISTED_LIKE = "110";
    const DUPLICATED_WISHLIST_ADD = "111";
    const NOT_EXISTED_WINE_WISHLIST = "112";
    const EXISTED_EMAIL = "113";
    const URL_NOT_EXIST = "114";
    const UNAVAILABLE_WINERY = "115";
    const UNAVAILABLE_COMMENT = "116";
    const DUPLICATED_FOLLOW = "117";
    const NOT_EXISTED_FOLLOW = "118";
    const UNAVAILABLE_WINE_NOTE = "119";
    const DUPLICATED_WINE_NOTE_ADD = "120";
    const SELF_FOLLOW_ERROR = "121";


    private static $ERROR_LIST = array(
        "0" => "ok",
        "100" => "Server is Maintaining",
        "101" => "Session invalid",
        "102" => "Missing parameters",
        "103" => "User is not available",
        "104" => "Rating is not available",
        "105" => "Wine is not available",
        "106" => "Device ID existed",
        "107" => "Wrong Email or Password",
        "108" => "Email not exist",
        "109" => "Already Like",
        "110" => "Like not exist",
        "111" => "Duplicated Wine WishList",
        "112" => "Remove Nonexistent Wine WishList",
        "113" => "Email Existed",
        "114" => "URL not exist",
        "115" => "Winery is not available",
        "116" => "Comment is not available",
        "117" => "Already Follow",
        "118" => "Follow not exist",
        "119" => "Wine note is not available",
        "120" => "Duplicated Wine note",
        "121" => "Can't follow yourself",
    );

    public static function createResponse($response) {
        return Response::json(array(
            "code" => $response["code"],
            "data" => $response["data"],
        ));
    }

    public static function getErrorContent($error_code) {
        return self::$ERROR_LIST[$error_code];
    }
    public static function checkPagination($getPage, $getLimit) 
    {
        $paginate = array(
            'page' => $getPage,
            'limit' => $getLimit
        );
        
        $rules = array(
            'page' => 'integer',
            'limit' =>'integer'
        );
       
        $validator = Validator::make($paginate, $rules);

        if($validator->fails()) {
            return false;
        } else {
            return $paginate;
        }
        
    }
    public static function pagination() 
    {
        if(Input::get('page')) {
            $getPage = Input::get('page');
            if(Input::get('per_page')) {
                $getLimit = Input::get('per_page');
            } else {
                $getLimit = 10;     
            }
            $paginate = ApiResponse::checkPagination($getPage, $getLimit);
            if($paginate !== false) {
                $page = $paginate['page'];
                $limit = $paginate['limit'];
                
            } else {
                $error_code = ApiResponse::URL_NOT_EXIST;
                $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
                return Response::json(array("code" => $error_code, "data" => $data));
            }

        } else {
            $page = 1;
            $limit = 10;
        }
        return array('page' => $page, 'limit' => $limit);
    }


}