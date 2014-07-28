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
        Follow::create(array(
            'id' => '1',
            'from_id' => $user_id,
            'to_id' => '2a420d2f-d33a-4691-a144-9192b69f34ae',
        ));

        Follow::create(array(
            'id' => '2',
            'from_id' => $user_id,
            'to_id' => 'b990decf-8c5a-4199-8d39-69760bc35200',
        ));

    }
}