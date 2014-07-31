<?php

	class CommentTableSeeder extends Seeder {
	    public function run() {
	        DB::table('comments')->delete();
	        $user_id = User::where('email','testacc@gmail.com')->first()->user_id;
        	$user_1 = User::where('email','test_1@gmail.com')->first()->user_id;
        	$user_2 = User::where('email','test_2@gmail.com')->first()->user_id;
	        Comment::create(array(
	            'id' => '1',
	            'rating_id' => '1',
	            'user_id' => $user_id,
	            'content' => 'this is comment 1',
	            
	        ));
	        Comment::create(array(
	            'id' => '2',
	            'rating_id' => '2',
	            'user_id' => $user_id,
	            'content' => 'this is comment 2',   
	            
	        ));
	        Comment::create(array(
	            'id' => '3',
	            'rating_id' => '2',
	            'user_id' => $user_id,
	            'content' => 'this is comment 3',   
	            
	        ));
	        Comment::create(array(
	            'id' => '4',
	            'rating_id' => '2',
	            'user_id' => $user_1,
	            'content' => 'this is comment 4',   
	            
	        ));
	        Comment::create(array(
	            'id' => '5',
	            'rating_id' => '2',
	            'user_id' => $user_1,
	            'content' => 'this is comment 5',   
	            
	        ));
	        Comment::create(array(
	            'id' => '6',
	            'rating_id' => '1',
	            'user_id' => $user_2,
	            'content' => 'this is comment 6',   
	            
	        ));
	        Comment::create(array(
	            'id' => '7',
	            'rating_id' => '2',
	            'user_id' => $user_2,
	            'content' => 'this is comment 7',   
	            
	        ));
	        Comment::create(array(
	            'id' => '8',
	            'rating_id' => '3',
	            'user_id' => $user_id,
	            'content' => 'this is comment 8',   
	            
	        ));
	        Comment::create(array(
	            'id' => '9',
	            'rating_id' => '3',
	            'user_id' => $user_1,
	            'content' => 'this is comment 9',   
	            
	        ));
	        Comment::create(array(
	            'id' => '10',
	            'rating_id' => '3',
	            'user_id' => $user_2,
	            'content' => 'this is comment 10',   
	            
	        ));
	    }
    }