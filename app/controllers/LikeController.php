<?php

class LikeController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{	
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$like = Like::where('user_id', $user_id)->get();
		if (count($like) > 0) {
			$data = $like->toArray();
		} else {	
			$data = 'No Like';
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
	public function store()
	{
			$like = new Like;
		    $error_code = ApiResponse::OK;
		    $like->user_id = Session::get('user_id');
			$input = $this->_getInput();

			if(!empty($input['rating_id'])) {
	    		$like->rating_id = $input['rating_id'];

			    $check_rating = Rating::check_rating($like->rating_id);
			    if ($check_rating !== false) {

			    	if (Like::where('rating_id', $like->rating_id)->having('user_id', '=', $like->user_id)->first()) {
						$error_code = ApiResponse::DUPLICATED_LIKE;
					 	$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_LIKE);
					} else {
						//update like_count on rating
						$like_rating = Rating::where('id', $like->rating_id)->first();
						if($like_rating != null) {

							$like_rating->like_count = $like_rating->like_count + 1;
							$like_rating->save();
						}						
						$like->save();
						
						$data = $like->toArray();
					}		   

				} else {
			    	 
			    	$error_code = ApiResponse::UNAVAILABLE_RATING;
			        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			
				}
			}
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{	
		$user_id = Session::get('user_id');
		$like = Like::where('id', $id)->first();
	    if($like) {
			$error_code = ApiResponse::OK;
	       	$data = $like->toArray();
		} else {
			$error_code = ApiResponse::URL_NOT_EXIST;
		    $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
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
		
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{	

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($rating_id)
	{
		$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		$like = Like::where('rating_id', $rating_id)->where('user_id',$user_id)->first();
	    if($like) {

	    	//update like_count on rating
 			$like_rating = Rating::where('id', $like->rating_id)->first();
			if($like_rating != null) {
				$like_rating->like_count = $like_rating->like_count - 1;
				$like_rating->save();
			}
 			$like->delete();
	 		$data = 'like deleted';
 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_LIKE;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_LIKE);
	    } 
	    return Response::json(array("code" => $error_code, "data" => $data));
	}
	// public function getLike_rating()
	// {
	// 	$user_id = Session::get('user_id');
	// 	$error_code = ApiResponse::OK;
	// 	$input = $this->_getInput();

	// 	if(!empty($input['rating_id'])) {
	//     	$rating_id = $input['rating_id'];

	//     	$check_rating = Rating::check_rating($rating_id);
            
	// 		if ($check_rating !== false) {
		    	
	// 			$like = Like::where('rating_id', $rating_id)->get();
				
	// 			if (count($like) > 0) {
	// 				$data = $like->toArray();
	// 			} else {	
	// 	        	$data = 'No Like';
	// 			}
	// 		} else {
	// 			$error_code = ApiResponse::UNAVAILABLE_RATING;
	// 	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);

	// 	    }
	// 	} else {
	// 		$error_code = ApiResponse::MISSING_PARAMS;
	//         $data = "Missing rating_id";
	// 	}
		
	//     return array("code" => $error_code, "data" => $data);
	// }
	// public function display_error($rating_id, $id) {

 //        return Response::json(
 //            array("code" => ApiResponse::URL_NOT_EXIST, "data" => array(
 //               "message" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST),
 //               "url"     => Request::fullUrl()
 //            ))
 //        );
 //    }

 //    protected function _getInput() {
 //        $input = json_decode(Input::get("data"), true);
 //        if($input == null) {
 //            $input = array();
 //        }
 //        return $input;
 //    }

}
