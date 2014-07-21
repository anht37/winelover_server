<?php

class CommentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($rating_id)
	{
		$input = array('rating_id' => $rating_id);
		$check_rating = Comment::check_rating($input);
		if ($check_rating == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
	
		} else {
			$comment = Comment::where('rating_id', $rating_id)->get();
			$error_code = ApiResponse::OK;
			if(isset($comment)){
				$data = 'No Comment';
			} else {
	        	$data = $comment->toArray();
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
	public function store($rating_id)
	{
		$comment = new Comment;
	    
	    $comment->rating_id = $rating_id;
	    $comment->user_id = Request::get('user_id');
	    $comment->content = Request::get('content');

	    $input = array('rating_id' => $rating_id, 'user_id' => Request::get('user_id'), 'content' => Request::get('content'));
	    $check_user = Comment::check_user($input);
	    $check_rating = Comment::check_rating($input);
	    if ($check_rating == 'FALSE' && $check_user == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING . ' and ' . ApiResponse::UNAVAILABLE_USER;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING) . ' and ' . ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER) ;
		}
	     elseif ($check_rating == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING) ;
	
		}  elseif($check_user == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_USER;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER) ;
	    } else {
	    	$comment->user_id = $check_user['user_id'];
			$comment->save();
			$error_code = ApiResponse::OK;
			$data = $comment->toArray();	    
		}

	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($rating_id, $id)
	{
		
		$comment = Comment::where('rating_id', $rating_id)->having('id', '=', $id)->first();
	    if($comment) {
			$error_code = ApiResponse::OK;
	       	$data = $content->toArray();
		} else {
			$error_code = ApiResponse::URL_NOT_EXIST;
		    $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
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
	public function update($rating_id, $id)
	{
	    $comment = Comment::where('rating_id', $rating_id)->having('id', '=', $id)->first();
 		if($comment) {
 			//if ( Request::get('rating_id') ) {
	        	$comment->rating_id = $rating_id;
		    //}
		    if ( Request::get('user_id') ) {
		        $comment->user_id = Request::get('user_id');
		    }
		    if ( Request::get('content') ) {
		        $comment->content = Request::get('content');
		    }
		    $input = $input = array('rating_id' => $rating_id, 'user_id' => Request::get('user_id'), 'content' => Request::get('content'));
		    $check_user = Comment::check_user($input);
		    $check_rating = Comment::check_rating($input);
		    if ($check_rating == 'FALSE' && $check_user == 'FALSE') {
		    	$error_code = ApiResponse::UNAVAILABLE_RATING . ' and ' . ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING) . ' and ' . ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		     elseif ($check_rating == 'FALSE') {
		    	$error_code = ApiResponse::UNAVAILABLE_RATING;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING) ;
		
			}  elseif($check_user == 'FALSE') {
		    	$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER) ;
			} else {
		    	$comment->user_id = $check_user['user_id'];
				$comment->save();
				$error_code = ApiResponse::OK;
				$data = $comment->toArray();	    
			}
	 	} else {
	 		$error_code = ApiResponse::URL_NOT_EXIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
	 	}
	    
	    return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($rating_id, $id)
	{
		$comment = Comment::where('rating_id', $rating_id)->having('id', '=', $id)->first();
 
	    if($comment) {
 			$comment->delete();
	 		$error_code = ApiResponse::OK;
	 		$data = 'Comment deleted';
 		} else {
 			$error_code = ApiResponse::URL_NOT_EXIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}

	public function display_error($rating_id, $id) {

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
