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
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/1',
            'wine_unique_id' => '1_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '赤',
        ));
        Wine::create(array(
        	'name' => 'メルロー ',
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/2',
            'wine_unique_id' => '2_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '赤',
        ));
        Wine::create(array(
        	'name' => 'カルメネール',
            'year' => '1993',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/3',
            'wine_unique_id' => '3_1993',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '赤',
        ));
        Wine::create(array(
        	'name' => 'シラー',
            'year' => '1993',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/4',
            'wine_unique_id' => '4_1993',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '赤',
        ));
        Wine::create(array(
        	'name' => 'ピノ・ノワール',
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/5',
            'wine_unique_id' => '5_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '赤',
        ));
        Wine::create(array(
        	'name' => 'ピノノワール・ロゼ ',
            'year' => '1993',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/6',
            'wine_unique_id' => '6_1993',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => 'ロゼ',
        ));
        Wine::create(array(
        	'name' => 'シャルドネ',
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/7',
            'wine_unique_id' => '7_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '白',
        ));
        Wine::create(array(
        	'name' => 'ゲヴュルツトラミネール ',
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/8',
            'wine_unique_id' => '8_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '白',
        ));
        Wine::create(array(
        	'name' => 'リースリング ',
            'year' => '1990',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/9',
            'wine_unique_id' => '9_1990',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '白',
        ));
        Wine::create(array(
        	'name' => 'ソーヴィニヨン・ブラン ',
            'year' => '1993',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/10',
            'wine_unique_id' => '10_1993',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '白',
        ));
        Wine::create(array(
        	'name' => 'ヴィオニエ',
            'year' => '1993',
            'winery_id' => '1',
            'image_url' => public_path() . '/wines/11',
            'wine_unique_id' => '11_1993',
            'average_price' => '0.0',
            'average_rate' => '0.0',
            'wine_type' => '白',
        ));

    }
} 