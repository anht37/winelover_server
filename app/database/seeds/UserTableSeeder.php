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
        
        User::create(array(
        	'id'=> 1,
            'email' => "testacc@gmail.com",
            'password' => '123456'

        ));
        for ($i = 1; $i < 10; $i ++) {
            User::create(array(
                'email' => "test_$i@gmail.com",
                'password' => '123456'
            ));
        }
        

    }
} 