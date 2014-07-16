<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class UserTableSeeder extends Seeder {
    public function run() {
        DB::table('users')->delete();
        for ($i = 1; $i < 10; $i ++) {
            User::create(array(
                'email' => "test_$i@gmail.com",
                'password' => Hash::make('123456')
            ));
        }

    }
} 