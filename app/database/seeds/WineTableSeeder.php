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
        // Wine::create(array(
        //     'wine_id' => '1',
        // 	'name' => 'Gnarly Head Authentic White',
        //     'year' => '2011',
        //     'winery_id' => '1',
        //     'image_url' => 'wines/1.jpg',
        //     'wine_unique_id' => '1_2011',
        //     'average_price' => '1200',
        //     'average_rate' => '3.0',
        //     'wine_type' => '2',
        // ));

        // Wine::create(array(
        //     'wine_id' => '2',
        //     'name' => 'Trimbach Riesling',
        //     'year' => '2010',
        //     'winery_id' => '2',
        //     'image_url' => 'wines/2.png',
        //     'wine_unique_id' => '2_2010',
        //     'average_price' => '1700',
        //     'average_rate' => '4.0',
        //     'wine_type' => '2',
        // ));
        // Wine::create(array(
        //     'wine_id' => '3',
        //     'name' => 'Gruner Love Featuring the Stadlmann Gruner Veltliner ',
        //     'year' => '2011',
        //     'winery_id' => '3',
        //     'image_url' => 'wines/3.png',
        //     'wine_unique_id' => '3_2011',
        //     'average_price' => '1600',
        //     'average_rate' => '2.5',
        //     'wine_type' => '1',
        // ));
        // Wine::create(array(
        //     'wine_id' => '4',
        //     'name' => 'Pine Ridge Chenin Blanc + Viognier ',
        //     'year' => '2013',
        //     'winery_id' => '4',
        //     'image_url' => 'wines/4.png',
        //     'wine_unique_id' => '4_2013',
        //     'average_price' => '1200',
        //     'average_rate' => '2.0',
        //     'wine_type' => '3',
        // ));
        // Wine::create(array(
        //     'wine_id' => '5',
        //     'name' => 'Frescobaldi Nipozzano Chianti Rufina Riserva',
        //     'year' => '2009',
        //     'winery_id' => '5',
        //     'image_url' => 'wines/5.png',
        //     'wine_unique_id' => '5_2009',
        //     'average_price' => '2200',
        //     'average_rate' => '4.5',
        //     'wine_type' => '1',
        // ));
        for ($i = 1; $i < 11; $i ++) {
        Wine::create(array(
            'wine_id' => "$i",
            'name' => "Wine_$i",
            'year' => '2009',
            'winery_id' => '5',
            'image_url' =>  URL::asset('wines/'.$i.'.png'),
            'wine_unique_id' => $i . "_2009",
            'average_price' => '2200',
            'average_rate' => '4.5',
            'wine_type' => '1',
            ));
        }
//        Wine::create(array(
//        	'name' => 'カベルネ・ソーヴィニヨン',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/1',
//            'wine_unique_id' => '1_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'メルロー ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/2',
//            'wine_unique_id' => '2_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'カルメネール',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/3',
//            'wine_unique_id' => '3_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'シラー',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/4',
//            'wine_unique_id' => '4_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'ピノ・ノワール',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/5',
//            'wine_unique_id' => '5_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '赤',
//        ));
//        Wine::create(array(
//        	'name' => 'ピノノワール・ロゼ ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/6',
//            'wine_unique_id' => '6_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => 'ロゼ',
//        ));
//        Wine::create(array(
//        	'name' => 'シャルドネ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/7',
//            'wine_unique_id' => '7_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ゲヴュルツトラミネール ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/8',
//            'wine_unique_id' => '8_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'リースリング ',
//            'year' => '1990',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/9',
//            'wine_unique_id' => '9_1990',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ソーヴィニヨン・ブラン ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/10',
//            'wine_unique_id' => '10_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));
//        Wine::create(array(
//        	'name' => 'ヴィオニエ',
//            'year' => '1993',
//            'winery_id' => '1',
//            'image_url' => public_path() . '/images/11',
//            'wine_unique_id' => '11_1993',
//            'average_price' => '0.0',
//            'average_rate' => '0.0',
//            'wine_type' => '白',
//        ));

    }
} 