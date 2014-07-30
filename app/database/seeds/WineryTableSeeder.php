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
            'country_id' => '2',
            'region' => 'Alsace',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '3',
            'brand_name' => 'Stadlmann',
            'country_id' => '3',
            'region' => 'Austrian',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '4',
            'brand_name' => 'Chenin Blanc',
            'country_id' => '4',
            'region' => 'California',
            'description' => 'des',
        ));

        Winery::create(array(
            'id' => '5',
            'brand_name' => 'Frescobaldi',
            'country_id' => '5',
            'region' => 'Chile',
            'description' => 'des',
        ));
        Winery::create(array(
            'id' => '6',
            'brand_name' => 'Rice',
            'country_id' => '6',
            'region' => 'VietNam',
            'description' => 'des',
        ));
        Winery::create(array(
            'id' => '7',
            'brand_name' => 'ComerSir',
            'country_id' => '7',
            'region' => 'Brazil',
            'description' => 'des',
        ));
        Winery::create(array(
            'id' => '8',
            'brand_name' => 'Sweet Lips',
            'country_id' => '8',
            'region' => 'France',
            'description' => 'des',
        ));
        Winery::create(array(
            'id' => '9',
            'brand_name' => 'Roney Worker',
            'country_id' => '9',
            'region' => 'USA',
            'description' => 'des',
        ));
         Winery::create(array(
            'id' => '10',
            'brand_name' => 'Rum',
            'country_id' => '10',
            'region' => 'UK',
            'description' => 'des',
        ));
    }
} 