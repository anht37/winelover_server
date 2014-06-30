<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 16:42
 */

class Device extends Eloquent {
    protected $table = 'devices';
    const IOS = 1;
    const ANDROID = 0;
}