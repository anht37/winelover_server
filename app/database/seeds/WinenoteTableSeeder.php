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
        $wine_1 = Wine::where('wine_id', 1)->first()->wine_unique_id;
        $wine_2 = Wine::where('wine_id', 2)->first()->wine_unique_id;
        $wine_3 = Wine::where('wine_id', 3)->first()->wine_unique_id;
        
        Winenote::create(
	            $data = array(
	                'id' => 1,
	                'wine_unique_id' => $wine_1,
	                'user_id' => $user_id,
	                'note' => 'This is note 1' ,
	            )
        	);
        
        
        Winenote::create(
                $data = array(
                    'id' => 2 ,
                    'wine_unique_id' => $wine_2,
                    'user_id' => $user_1,
                    'note' => 'This is note 2',
                )
            );
        
        Winenote::create(
                $data = array(
                    'id' => 3,
                    'wine_unique_id' => $wine_3,
                    'user_id' => $user_2,
                    'note' => 'This is note 3',
                )
            );
        
 	}
}