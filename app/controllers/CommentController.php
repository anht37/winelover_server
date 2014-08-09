<?php

class CommentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index($rating_id)
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$check_rating = Rating::check_rating($rating_id);
		if ($check_rating !== false) {

		 	$comment = Comment::where('rating_id', $rating_id)->get();
		 	if(count($comment) > 0){
		 		foreach ($comment as $comments) {
		 			$profile = Profile::where('user_id', $comments->user_id)->first();
		 			if($profile->image != null) {
		 				$comments->avatar_user = URL::asset($profile->image);
		 			} else {
		 				$comments->avatar_user = $profile->image;
		 			}
		 			$comments->first_name = $profile->first_name;
		 			$comments->last_name = $profile->last_name;
		 		}
		 		$data = $comment->toArray();
		 	} else {
		        $data = '';
		 	}

		} else {
		    $error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
	    return Response::json(array("code" => $error_code, "data" => $data));
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
	public function store($rating_id)
	{
		$comment = new Comment;
	    $input = $this->_getInput();
	    $error_code = ApiResponse::OK;
	    $comment->user_id = Session::get('user_id');

	    if(!empty($input['content'])) {
	    	$comment->content = $input['content'];
	    	$comment->rating_id = $rating_id;
		    $check_rating = Rating::check_rating($comment->rating_id);
		    if ($check_rating !== false) {
		    	//update comment_count on rating
				$comment_rating = Rating::where('id', $comment->rating_id)->first();
				if($comment_rating != null) {
					$comment_rating->comment_count = $comment_rating->comment_count + 1;
					$comment_rating->save();
	 				$comment->delete();
	 			}

				$comment_profile = Profile::where('user_id', $comment->user_id)->first();
            
	            if($comment_profile != null) {
	                $comment_profile->comment_count = $comment_profile->comment_count + 1;
	                $comment_profile->save();
	            }

		    	$comment->save();
				$data = $comment;
					    
			} else {
			    $error_code = ApiResponse::UNAVAILABLE_RATING;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			}
	    } else {
	    	$error_code = ApiResponse::MISSING_PARAMS;
		    $data = "Missing content";
	    }
	    
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($rating_id, $id)
	{
		$error_code = ApiResponse::OK;
		$comment = Comment::where('id', $id)->first();
	    if($comment) {
	       	$data = $comment->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_COMMENT;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
		}    
		
		
	    return Response::json(array("code" => $error_code, "data" => $data));
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
	public function update($rating_id, $id)
	{
	    $comment = Comment::where('id', $id)->first();
	    $input = $this->_getInput();
	    $error_code = ApiResponse::OK;
 		if($comment) {
 			if(!empty($input)) {
			    if(!empty($input['content'])) {
		    		$comment->content = $input['content'];
		    	}
					$comment->save();
					
					$data = $comment->toArray();	    
			} else {
				$error_code = ApiResponse::MISSING_PARAMS;
		        $data = $input;
			}
	 	} else {
	 		$error_code = ApiResponse::UNAVAILABLE_COMMENT;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
	 	}
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($rating_id, $id)
	{
		$comment = Comment::where('id', '=', $id)->first();
 		$error_code = ApiResponse::OK;
	    if($comment) {
	    	$comment_profile = Profile::where('user_id', $comment->user_id)->first();
            
            if($comment_profile != null) {
                $comment_profile->comment_count = $comment_profile->comment_count - 1;
                $comment_profile->save();
            }

	    	//update comment_count on rating

 			$comment_rating = Rating::where('id', $comment->rating_id)->first();
			if($comment_rating != null) {
				$comment_rating->comment_count = $comment_rating->comment_count - 1;
				$comment_rating->save();
 				$comment->delete();
 			}

	 		
	 		$data = 'Comment deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_COMMENT;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
	    } 
	    return Response::json(array("code" => $error_code, "data" => $data));
	}

	public function display_error($id) {

        return Response::json(
            array("code" => ApiResponse::URL_NOT_EXIST, "data" => array(
               "message" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST),
               "url"     => Request::fullUrl()
            ))
        );
    }

    protected function _getInput() {
        $input = json_decode(Input::get("data"), true);
        if($input == null) {
            $input = array();
        }
        return $input;
    }
}
