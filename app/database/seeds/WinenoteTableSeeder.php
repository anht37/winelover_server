<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 26/06/2014
 * Time: 14:36
 */

class WinenoteTableSeeder extends Seeder {
    public function run() {
        DB::table('wine_notes')->delete();
        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        $user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        $user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
        for ($i = 1; $i < 11; $i ++) {
        Winenote::create(
	            $data = array(
	                'id' => "$i",
	                'wine_unique_id' => $i . "_2009",
	                'user_id' => $user_id,
	                'note' => 'This is note' . $i ,
	            )
        	);
        }
        for ($i = 11; $i < 21; $i ++) {
        Winenote::create(
                $data = array(
                    'id' => $i ,
                    'wine_unique_id' => $i . "_2009",
                    'user_id' => $user_1,
                    'note' => 'This is note' . $i ,
                )
            );
        }
        for ($i = 21; $i < 31; $i ++) {
        Winenote::create(
                $data = array(
                    'id' => $i ,
                    'wine_unique_id' => $i . "_2009",
                    'user_id' => $user_2,
                    'note' => 'This is note' . $i ,
                )
            );
        }
 	}
}