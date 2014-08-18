<?php

return array(

    'YineLover'     => array(
        'environment' => 'development',
        'certificate' => Config::get('app.pem'),
        'passPhrase'  => '123456',
        'service'     => 'apns'
    ),
    'appNameAndroid' => array(
        'environment' => 'production',
        'apiKey'      => 'yourAPIKey',
        'service'     => 'gcm'
    )

);