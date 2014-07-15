e<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 15/07/2014
 * Time: 09:39
 */

class Wine {
    public static function scan($file_path) {
        $query = Config::get('winedetect.script').' predict '.Config::get('winedetect.config').' '.$file_path;
        return exec($query);
    }
} 