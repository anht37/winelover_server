<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class WineTableSeeder extends Seeder {
    public function run() {
        DB::table('wines')->delete();
        Wine::create(array(
        	'name' => 'カベルネ・ソーヴィニヨン',
            'year' => '',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/1',
            'wine_unique_id' => '',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '',
        ));

    }
} 