<?php

class CommentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function __construct()
    {
        $this->beforeFilter('session');
    }

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
	public function store($rating_id)
	{
		$comment = new Comment;
	    $input = $this->_getInput();
	    $comment->rating_id = $rating_id;
	    $comment->user_id = Rating::getUser_id(Request::header('session'));
	    if(!empty($input['content'])) {
	    	$comment->content = $input['content'];
	    }
	    $rating = array('rating_id' => $rating_id);
	    $check_rating = Comment::check_rating($rating);
	    if ($check_rating == 'FALSE') {
		    $error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		
		} else {
			$comment->save();
			$error_code = ApiResponse::OK;
			$data = $comment;
				    
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
		
		$comment = Comment::where('id', $id)->first();
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
	    $comment = Comment::where('id', $id)->first();
	    $input = $this->_getInput();
 		if($comment) {
 			if(!empty($input)) {
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
		$comment = Comment::where('id', '=', $id)->first();
 
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
