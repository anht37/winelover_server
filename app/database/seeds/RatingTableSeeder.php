<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class RatingTableSeeder extends Seeder {
    public function run() {
        DB::table('ratings')->delete();
        Rating::create(array(
            'id' => '1',
            'user_id' => '1c896322-8be2-4582-a3e1-bc7b8947c9db',
            'wine_unique_id' => '1_2009',
            'rate' => '4.5',
            'like_count' => '3',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '2',
            'user_id' => '8bd7e2b9-8206-4eca-9c84-831094f5b2d4',
            'wine_unique_id' => '2_2009',
            'rate' => '1.5',
            'like_count' => '5',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '3',
            'user_id' => '1c896322-8be2-4582-a3e1-bc7b8947c9db',
            'wine_unique_id' => '3_2009',
            'rate' => '4.5',
            'like_count' => '8',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '4',
            'user_id' => '8bd7e2b9-8206-4eca-9c84-831094f5b2d4',
            'wine_unique_id' => '4_2009',
            'rate' => '3.0',
            'like_count' => '3',
            'comment_count' => '3',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '5',
            'user_id' => '1c896322-8be2-4582-a3e1-bc7b8947c9db',
            'wine_unique_id' => '5_2009',
            'rate' => '2.5',
            'like_count' => '4',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));
    }
} 