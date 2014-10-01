<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 30/09/2014
 * Time: 07:41
 */

class Wine2TableSeeder extends Seeder {
    public function run() {
        $files = File::files("/Users/anhtd/Desktop/img");
        $url = 'curl -v -X POST -F "img=$img_link" http://sheezer.com:8888/api/match';
        foreach($files as $file) {
            $start = time();
            $request = curl_init('http://sheezer.com:8888/api/match');

            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt(
                $request,
                CURLOPT_POSTFIELDS,
                array(
                    'img' => new CurlFile($file)
                ));

            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            $result =  json_decode(curl_exec($request));
            var_dump($result);

            echo PHP_EOL;
            echo "Cost : ".(time() - $start)." seconds.".PHP_EOL;
            curl_close($request);
        }

    }
} 