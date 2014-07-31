<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class FollowTableSeeder extends Seeder {
    public function run() {
        DB::table('follows')->delete();
        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        $user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        $user_2 = User::where('email','test_2@gmail.com')->first()->user_id;

        Follow::create(array(
            'id' => '1',
            'from_id' => $user_id,
            'to_id' => $user_1,
        ));

        Follow::create(array(
            'id' => '2',
            'from_id' => $user_id,
            'to_id' => $user_2,
        ));

    }
}