<?php

	class WishlistTableSeeder extends Seeder {
	    public function run() {
	        DB::table('wishlists')->delete();
	        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        	$user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        	$user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
        	$wine_1 = Wine::where('wine_id', 1)->first()->wine_unique_id;
        	$wine_2 = Wine::where('wine_id', 2)->first()->wine_unique_id;
        	$wine_3 = Wine::where('wine_id', 3)->first()->wine_unique_id;
	        Wishlist::create(array(
	            'id' => '1',
	            'wine_unique_id' => $wine_1,
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '2',
	            'wine_unique_id' => $wine_2,
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '3',
	            'wine_unique_id' => $wine_3,
	            'user_id' => $user_id,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '4',
	            'wine_unique_id' => $wine_1,
	            'user_id' => $user_1,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '5',
	            'wine_unique_id' => $wine_3,
	            'user_id' => $user_1,
	            
	        ));
	        Wishlist::create(array(
	            'id' => '6',
	            'wine_unique_id' => $wine_1,
	            'user_id' => $user_2,
	            
	        ));
	        
	        Wishlist::create(array(
	            'id' => '7',
	            'wine_unique_id' => $wine_3,
	            'user_id' => $user_2,
	            
	        ));
	    }
	}