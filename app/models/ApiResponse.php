<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 15:22
 */

class ApiResponse {
    private static $_header;
    private static $_error_code = array(
        "000",
        "100",
        "101",
        "102",
        "103",
        "104",
        "105",
        "106",
        "107",
        "108",
        "109",
        "110",
        "111",
        "112"
    );

    public static function createResponse($response) {
        return Response::json(array(
            "code" => $response["code"],
            "data" => $response["data"],
        ));
    }


}