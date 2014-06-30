<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/06/2014
 * Time: 16:17
 */

return array(

    'default' => 'sqlite',

    'connections' => array(
        'sqlite' => array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ),
    )
);