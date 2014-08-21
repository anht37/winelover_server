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
        $user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        $user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
        $wine_1 = Wine::where('wine_id', 1)->first()->wine_unique_id;
        $wine_2 = Wine::where('wine_id', 2)->first()->wine_unique_id;
        $wine_3 = Wine::where('wine_id', 3)->first()->wine_unique_id;
        Rating::create(array(
            'id' => '1',
            'user_id' => $user_id,
            'wine_unique_id' => $wine_1,
            'rate' => '0.5',
            'content' => 'this is rating 1',
            'like_count' => '5',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));
        Rating::create(array(
            'id' => '2',
            'user_id' => $user_id,
            'wine_unique_id' => $wine_2,
            'rate' => '1.5',
            'content' => 'this is rating 2',
            'like_count' => '5',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '3',
            'user_id' => $user_id,
            'wine_unique_id' => $wine_3,
            'rate' => '4.5',
            'content' => 'this is rating 3',
            'like_count' => '8',
            'comment_count' => '5',
            'is_my_wine' => '1',
        ));

        Rating::create(array(
            'id' => '4',
            'user_id' => $user_1,
            'wine_unique_id' => $wine_1,
            'rate' => '5',
            'content' => 'this is rating 7',
            'like_count' => '6',
            'comment_count' => '15',
            'is_my_wine' => '1',
        ));
        Rating::create(array(
            'id' => '5',
            'user_id' => $user_2,
            'wine_unique_id' => $wine_2,
            'rate' => '5',
            'content' => 'this is rating 8',
            'like_count' => '14',
            'comment_count' => '8',
            'is_my_wine' => '1',
        ));
    }
} 