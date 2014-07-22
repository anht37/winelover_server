<?php

	class CommentTableSeeder extends Seeder {
	    public function run() {
	        DB::table('comments')->delete();
	        Comment::create(array(
	            'id' => '1',
	            'rating_id' => '1',
	            'user_id' => '1c896322-8be2-4582-a3e1-bc7b8947c9db',
	            'content' => 'this is comment 1',
	            
	        ));
	        Comment::create(array(
	            'id' => '2',
	            'rating_id' => '2',
	            'user_id' => '8bd7e2b9-8206-4eca-9c84-831094f5b2d4',
	            'content' => 'this is comment 2',   
	            
	        ));
	    }
    }