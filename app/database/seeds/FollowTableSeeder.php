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
        Follow::create(array(
            'id' => '1',
            'from_id' => '1719d374-5017-4d67-9e4c-e2ee6ff743e4',
            'to_id' => '2a420d2f-d33a-4691-a144-9192b69f34ae',
        ));

        Follow::create(array(
            'id' => '2',
            'from_id' => '1719d374-5017-4d67-9e4c-e2ee6ff743e4',
            'to_id' => 'b990decf-8c5a-4199-8d39-69760bc35200',
        ));

    }
}