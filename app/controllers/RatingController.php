<?php

class RatingController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index() 
	{
		$user_id = Session::get('user_id');
		$pagination = ApiResponse::pagination();
		$page = $pagination['page'];
		$limit = $pagination['limit'];
		$rating = Rating::where('user_id', $user_id)->where('is_my_wine', 1)->with('wine')->orderBy('updated_at', 'desc')->forPage($page, $limit)->get();
		$error_code = ApiResponse::OK;
		if(count($rating) == 0 ) {
			if($page == 1) {
				$data = "";
			} else {
				$error_code = ApiResponse::URL_NOT_EXIST;
            	$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
        	}
		} else {
			foreach ($rating as $ratings) {
				$ratings->winery = Winery::where('id',$ratings->wine->winery_id)->first();
				if($ratings->wine->image_url != null) {
			        $ratings->wine->image_url = URL::asset($ratings->wine->image_url);
				}

				if($ratings->wine->wine_flag != null) {
				    $ratings->wine->wine_flag = URL::asset($ratings->wine->wine_flag);
				} 
			}
        	$data = $rating->toArray();
			
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
		$rating = new Rating;
	    $input = $this->_getInput();
	    $error_code = ApiResponse::OK;
	    $rating->user_id = Session::get('user_id');
	    if(!empty($input['wine_unique_id'])) {
	    	$rating->wine_unique_id = $input['wine_unique_id'];
	    
		    if (!empty($input['rate'])) {  
		    	$rating->rate = $input['rate'];
		    }
		    if (!empty($input['content'])) {  
		    	 $rating->content = $input['content'];
		    }	    
		    if (!empty($input['like_count'])) {
		        $rating->like_count = $input['like_count'];
		    }
		    if (!empty($input['comment_count'])) {
		        $rating->comment_count = $input['comment_count'];
		    }
		    if (!empty($input['is_my_wine'])) {
		        $rating->is_my_wine = $input['is_my_wine'];
		    }
		 	
		    // Validation and Filtering is sorely needed!!
		    // Seriously, I'm a bad person for leaving that out.
		    $check = Rating::check_validator($input);
		    if($check !== false) {

		    	$rating_profile = Profile::where('user_id',$rating->user_id)->first();
		    	if($rating_profile != null) {
		    		$rating_profile->rate_count = $rating_profile->rate_count + 1;
		    		$rating_profile->save(); 
		    	}
		    	$rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
		    	if($rating_wine != null) {
		    		$rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
		    		$rating_wine->rate_count = $rating_wine->rate_count + 1;
		    		$rating_wine->average_rate = ($rating_rate + $rating->rate)/ $rating_wine->rate_count;
		    		$rating_wine->save(); 
		    	}

		    	$rating->save();
				
				$data = $rating;	 

		    } else {
				
		    	$error_code = ApiResponse::UNAVAILABLE_RATING;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);

			}
	    } else {
	    	$error_code = ApiResponse::MISSING_PARAMS;
	        $data = $input;
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
		$rating = Rating::where('id', $id)->with('wine')->first();
		$error_code = ApiResponse::OK;
		if($rating) {
			
       	 	$data = $rating->toArray();

		} else {
			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
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
	public function update($id)
	{
		$rating = Rating::where('id', $id)->first();
		$error_code = ApiResponse::OK;
		$input = $this->_getInput();
		if($rating) {
			$rating_rate_old = $rating->rate;
	 		if(!empty($input)) {
	 			if (!empty($input['wine_unique_id'])) { 
			    	$rating->wine_unique_id = $input['wine_unique_id'];
		    	}
			    if (!empty($input['rate'])) {  
			    	$rating->rate = $input['rate'];
			    }
			    if (!empty($input['content'])) {  
			    	 $rating->content = $input['content'];
			    }	    
			    if (!empty($input['like_count'])) {
			        $rating->like_count = $input['like_count'];
			    }
			    if (!empty($input['comment_count'])) {
			        $rating->comment_count = $input['comment_count'];
			    }
			    if (!empty($input['is_my_wine'])) {
			        $rating->is_my_wine = $input['is_my_wine'];
			    }
			    
			    $check = Rating::check_validator($input);
			    if($check !== false) {
			    	if($rating->rate > 0) {
			    		$rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
					    if($rating_wine != null) {
					    	$rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
					    	if($rating_rate == 0 && $rating_wine->rate_count !== 0) {
					    		$rating_wine->average_rate = ($rating_rate + $rating->rate)/ $rating_wine->rate_count;
					    	} elseif ($rating_rate !== 0 && $rating_wine->rate_count !== 0) {
					    		$rating_wine->average_rate = ($rating_rate - $rating_rate_old + $rating->rate)/ $rating_wine->rate_count;
					    	} else {
					    		$error_code = ApiResponse::UNAVAILABLE_RATING;
			        			$data = "";
			        			return Response::json(array("code" => $error_code, "data" => $data));
					    	}
					    	$rating_wine->save(); 
					    }
			    	} 
			    	
					$rating->save();
					
					$data = $rating->toArray();	   

			    } else {
			    	
					$error_code = ApiResponse::UNAVAILABLE_RATING;
			        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING); 
				
				}
		    } else {
		    	$error_code = ApiResponse::MISSING_PARAMS;
		        $data = $input;
		    }
		} else {
			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$rating = Rating::where('id', $id)->first();
		$error_code = ApiResponse::OK;
 		if($rating) {
 			$rating_profile = Profile::where('user_id',$rating->user_id)->first();
	 		if($rating_profile != null) {
			    $rating_profile->rate_count = $rating_profile->rate_count - 1;
			    $rating_profile->save(); 
			}
			$rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
		    if($rating_wine != null) {
		    	$rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
		    	$rating_wine->rate_count = $rating_wine->rate_count - 1;
		    	if($rating_wine->rate_count > 0) {
		    		$rating_wine->average_rate = ($rating_rate - $rating->rate)/ $rating_wine->rate_count;
		    	} else {
		    		$rating_wine->average_rate = 0;
		    	}
		    	$rating_wine->save(); 
		    }

 			$rating->delete();
	 		
	 		$data = 'Rating Deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
	    } 
	    return Response::json(array("code" => $error_code, "data" => $data));
	}
	public function remove($id)
	{
		$rating = Rating::where('id', $id)->first();
		$error_code = ApiResponse::OK;
 		if($rating) {
 			$rating->is_my_wine = 0;
 			$rating->save();
	 		
			$data = 'Rating is removed from my wine';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		} 
			return Response::json(array("code" => $error_code, "data" => $data));
	}
}
