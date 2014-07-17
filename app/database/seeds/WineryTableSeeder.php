<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class WineryTableSeeder extends Seeder {
    public function run() {
        DB::table('wineries')->delete();
        Winery::create(array(
            'id' => '1',
            'brand_name' => 'Gnarly Head',
            'country_id' => '1',
            'region' => 'Dutch',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '2',
            'brand_name' => 'Pinot Gris/Grigio',
            'country_id' => '1',
            'region' => 'Alsace',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '3',
            'brand_name' => 'Stadlmann',
            'country_id' => '1',
            'region' => 'Austrian',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '4',
            'brand_name' => 'Chenin Blanc',
            'country_id' => '2',
            'region' => 'California',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '5',
            'brand_name' => 'Frescobaldi',
            'country_id' => '2',
            'region' => 'California',
            'description' => 'des',
        ));
    }
} 