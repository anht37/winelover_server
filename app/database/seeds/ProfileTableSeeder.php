<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class ProfileTableSeeder extends Seeder {
    public function run() {
        DB::table('profiles')->delete();
        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        $user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        $user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
        Profile::create(array(
            'id' => '1',
            'user_id' => $user_id,
            'follower_count' => '3',
            'following_count' => '5',
            'rate_count' => '5',
            'comment_count' => '5',
            'scan_count' => '1',
            'last_name' => 'pro1',
            'first_name' => 'user login',
        ));
        Profile::create(array(
            'id' => '2',
            'user_id' => $user_1,
            'follower_count' => '3',
            'following_count' => '4',
            'rate_count' => '5',
            'comment_count' => '5',
            'scan_count' => '1',
            'last_name' => 'pro2',
            'first_name' => 'user follow1',
        ));
        Profile::create(array(
            'id' => '3',
            'user_id' => $user_2,
            'follower_count' => '4',
            'following_count' => '3',
            'rate_count' => '5',
            'comment_count' => '5',
            'scan_count' => '1',
            'last_name' => 'pro3',
            'first_name' => 'user follow2',
        ));
    }
}