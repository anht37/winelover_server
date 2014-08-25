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
            'password' => '123456',
            'fb_id' => '807394575946717'

        ));
        User::create(array(
            'id'=> 2,
            'email' => "test_1@gmail.com",
            'password' => '123456',
            'fb_id' => '10202725915174596'

        ));
        User::create(array(
            'id'=> 3,
            'email' => "test_2@gmail.com",
            'password' => '123456',
            'fb_id' => '10202115868129316'

        ));

        for ($i = 3; $i < 10; $i ++) {
            User::create(array(
                'id' => $i + 1 ,
                'email' => "test_$i@gmail.com",
                'password' => '123456'
            ));
        }
        

    }
} 