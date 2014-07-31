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
        
 	}
}