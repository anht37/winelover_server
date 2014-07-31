<?php

	class LikeTableSeeder extends Seeder {
	    public function run() {
	        DB::table('likes')->delete();
	        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        	$user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        	$user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
	        Like::create(array(
	            'id' => '1',
	            'rating_id' => '1',
	            'user_id' => $user_id,
	            
	        ));
	        Like::create(array(
	            'id' => '2',
	            'rating_id' => '2',
	            'user_id' => $user_id,
	            
	        ));
	        Like::create(array(
	            'id' => '3',
	            'rating_id' => '2',
	            'user_id' => $user_1,
	            
	        ));
	        Like::create(array(
	            'id' => '4',
	            'rating_id' => '3',
	            'user_id' => $user_1,
	            
	        ));
	        Like::create(array(
	            'id' => '5',
	            'rating_id' => '2',
	            'user_id' => $user_2,
	            
	        ));
	        Like::create(array(
	            'id' => '6',
	            'rating_id' => '1',
	            'user_id' => $user_2,
	            
	        ));
	    }
    }
