<?php

class CommentController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$input = $this->_getInput();

		if(!empty($input['rating_id'])) {
	    	$rating_id = $input['rating_id'];

			$check_rating = Rating::check_rating($rating_id);
			if ($check_rating != 'FALSE') {

				$comment = Comment::where('rating_id', $rating_id)->get();
				
				if(count($comment) > 0){
					$data = $comment->toArray();
				} else {
		        	$data = 'No Comment';
				}

			} else {
				
		    	$error_code = ApiResponse::UNAVAILABLE_RATING;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			}
		} else {
			$comment = Comment::where('user_id', $user_id)->get();
			
			if(count($comment) > 0){
				$data = $comment->toArray();
			} else {
		        $data = 'No Comment';
			}
		}
	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$comment = new Comment;
	    $input = $this->_getInput();
	    $comment->user_id = Session::get('user_id');

	    if(!empty($input['content'] && !empty($input['rating_id'])) {
	    	$comment->content = $input['content'];
	    	$comment->rating_id = $input['rating_id'];

		    $check_rating = Rating::check_rating($comment->rating_id);
		    if ($check_rating != 'FALSE') {
		    	//update comment_count on rating
				$comment_rating = Rating::where('id', $comment->rating_id)->first();
				$comment_rating->comment_count = $comment_rating->comment_count + 1;
				$comment_rating->save();

				$comment_profile = Profile::where('user_id', $comment->user_id)->first();
				$comment_profile->comment_count = $comment_profile->comment_count + 1;
				$comment_profile->save();

		    	$comment->save();
				
				$error_code = ApiResponse::OK;
				$data = $comment;
					    
			} else {
			    $error_code = ApiResponse::UNAVAILABLE_RATING;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			}
	    } else {
	    	$error_code = ApiResponse::MISSING_PARAMS;
		    $data = $input;
	    }
	    
	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
		$comment = Comment::where('id', $id)->first();
	    if($comment) {
			$error_code = ApiResponse::OK;
	       	$data = $comment->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_COMMENT;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
		}    
		
		
	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
	    $comment = Comment::where('id', $id)->first();
	    $input = $this->_getInput();
 		if($comment) {
 			if(!empty($input)) {
 				if(!empty($input['rating_id'])) {
		    		
		    		$check_rating = Rating::check_rating($input['rating_id']);
		    		if($check_rating != 'FALSE') {
		    			$comment->rating_id = $input['rating_id'];
		    		} else {
		    			$error_code = ApiResponse::UNAVAILABLE_RATING;
			    		$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			    		return array("code" => $error_code, "data" => $data);
		    		}
		    	}
			    if(!empty($input['content'])) {
		    		$comment->content = $input['content'];
		    	}
					$comment->save();
					$error_code = ApiResponse::OK;
					$data = $comment->toArray();	    
			} else {
				$error_code = ApiResponse::MISSING_PARAMS;
		        $data = $input;
			}
	 	} else {
	 		$error_code = ApiResponse::UNAVAILABLE_COMMENT;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
	 	}
	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$comment = Comment::where('id', '=', $id)->first();
 
	    if($comment) {
	    	$comment_profile = Profile::where('user_id', $comment->user_id)->first();
			$comment_profile->comment_count = $comment_profile->comment_count - 1;
			$comment_profile->save();

	    	//update comment_count on rating
 			$comment_rating = Rating::where('id', $rating_id)->first();
			$comment_rating->comment_count = $comment_rating->comment_count - 1;
			$comment_rating->save();
 			$comment->delete();

	 		$error_code = ApiResponse::OK;
	 		$data = 'Comment deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_COMMENT;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}
}
