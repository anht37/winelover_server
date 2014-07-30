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
        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        Rating::create(array(
            'id' => '21',
            'user_id' => $user_id,
            'wine_unique_id' => '1_2009',
            'rate' => '1.5',
            'content' => 'this is rating 1',
            'like_count' => '5',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));
        Rating::create(array(
            'id' => '2',
            'user_id' => $user_id,
            'wine_unique_id' => '2_2009',
            'rate' => '1.5',
            'content' => 'this is rating 2',
            'like_count' => '5',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '3',
            'user_id' => $user_id,
            'wine_unique_id' => '3_2009',
            'rate' => '4.5',
            'content' => 'this is rating 3',
            'like_count' => '8',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '4',
            'user_id' => 'b990decf-8c5a-4199-8d39-69760bc35200',
            'wine_unique_id' => '4_2009',
            'rate' => '3.0',
            'content' => 'this is rating 4',
            'like_count' => '3',
            'comment_count' => '3',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '5',
            'user_id' => 'b990decf-8c5a-4199-8d39-69760bc35200',
            'wine_unique_id' => '5_2009',
            'rate' => '2.5',
            'content' => 'this is rating 5',
            'like_count' => '4',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '6',
            'user_id' => '2a420d2f-d33a-4691-a144-9192b69f34ae',
            'wine_unique_id' => '6_2009',
            'rate' => '2.5',
            'content' => 'this is rating 6',
            'like_count' => '4',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));
    }
} 