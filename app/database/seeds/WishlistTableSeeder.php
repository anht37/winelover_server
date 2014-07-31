<?php

	class WishlistTableSeeder extends Seeder {
	    public function run() {
	        DB::table('wishlists')->delete();
	        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        	$user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        	$user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
	        Wishlist::create(array(
	            'id' => '1',
	            'wine_unique_id' => '1_2009',
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '2',
	            'wine_unique_id' => '2_2009',
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '3',
	            'wine_unique_id' => '3_2009',
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '4',
	            'wine_unique_id' => '1_2009',
	            'user_id' => $user_1,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '5',
	            'wine_unique_id' => '3_2009',
	            'user_id' => $user_1,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '6',
	            'wine_unique_id' => '1_2009',
	            'user_id' => $user_2,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '7',
	            'wine_unique_id' => '5_2009',
	            'user_id' => $user_2,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '8',
	            'wine_unique_id' => '3_2009',
	            'user_id' => $user_2,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '9',
	            'wine_unique_id' => '8_2009',
	            'user_id' => $user_1,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '10',
	            'wine_unique_id' => '6_2009',
	            'user_id' => $user_id,
	            
	        ));
	    }
	}