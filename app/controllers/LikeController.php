<?php

class LikeController extends \BaseController {

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
		
		$check_rating = Rating::check_rating($rating_id);
		if ($check_rating == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		} else {
			$like = Like::where('rating_id', $rating_id)->get();
			$error_code = ApiResponse::OK;
			if (count($like) > 0) {
				$data = $like->toArray();
			} else {	
	        	$data = 'No Like';
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
			$like = new Like;
		    $like->rating_id = $rating_id;
		    $like->user_id = Session::get('user_id');
		    
		    $check_rating = Rating::check_rating($like->rating_id);
		    if ($check_rating != 'FALSE') {

		    	if (Like::where('rating_id', $like->rating_id)->having('user_id', '=', $like->user_id)->first()) {
					$error_code = ApiResponse::DUPLICATED_LIKE;
				 	$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_LIKE);
				} else {
					
					//update like_count on rating
					$like_rating = Rating::where('id', $rating_id)->first();
					$like_rating->like_count = $like_rating->like_count + 1;
					$like_rating->save();
					
					$like->save();
					$error_code = ApiResponse::OK;
					$data = $like->toArray();
				}		   

			} else {
		    	 
		    	$error_code = ApiResponse::UNAVAILABLE_RATING;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		
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
		$user_id = Session::get('user_id');
		$like = Like::where('rating_id', $rating_id)->having('user_id', '=', $user_id)->first();
	    if($like) {
			$error_code = ApiResponse::OK;
	       	$data = $like->toArray();
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
		
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($rating_id)
	{	

		// $like = Like::where('rating_id', $rating_id)->having('id', '=', $id)->first();
 	//  	if($like) {
	 //        $like->rating_id = $rating_id;
	 //    }
		//     if ( Request::get('user_id') ) {
		//         $like->user_id = Request::get('user_id');
	 // 	    }
		//     $input = array('rating_id' => $rating_id);
		//     $check_user = Like::check_user($input);
		//     $check_rating = Like::check_rating($input);
		//     if ($check_rating == 'FALSE' && $check_user == 'FALSE') {
	 //     		$error_code = ApiResponse::UNAVAILABLE_RATING . ' and ' . ApiResponse::UNAVAILABLE_USER;
	 //         	$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING) . ' and ' . ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		//  	} elseif ($check_rating == 'FALSE') {
		//      	$error_code = ApiResponse::UNAVAILABLE_RATING;
		//         $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		//  	}  elseif($check_user == 'FALSE') {
		//      	$error_code = ApiResponse::UNAVAILABLE_USER;
		//          $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		//  	} else {
		//  		$like->save();
		//  		$error_code = ApiResponse::OK;
		//  		$data = $like->toArray();	    
		//  	}
	 //    return array("code" => $error_code, "data" => $data);
	    
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($rating_id)
	{
		$user_id = Session::get('user_id');
		$like = Like::where('rating_id', $rating_id)->having('user_id', '=', $user_id)->first();
	    if($like) {
 			$like->delete();
 			
 			//update like_count on rating
 			$like_rating = Rating::where('id', $rating_id)->first();
			$like_rating->like_count = $like_rating->like_count - 1;
			$like_rating->save();
	 		
	 		$error_code = ApiResponse::OK;
	 		$data = 'like deleted';
 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_LIKE;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_LIKE);
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
