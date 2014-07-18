<?php

class RatingController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() 
	{
		$rating = Rating::all();
		
		$error_code = ApiResponse::OK;
        $data = $rating->toArray();
	    
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
		$rating = new Rating;
	    
	    $rating->user_id = Request::get('user_id');

	    $rating->wine_unique_id = Request::get('wine_unique_id');
	    
	    if (Request::get('rate')) {  
	    	$rating->rate = Request::get('rate');
	    }

	    $rating->content = Request::get('content');
	    
	    if (Request::get('like_count')) {
	        $rating->like_count = Request::get('like_count');
	    }
	    if (Request::get('comment_count')) {
	        $rating->comment_count = Request::get('comment_count');
	    }
	    if (Request::get('is_my_wine')) {
	        $rating->is_my_wine = Request::get('is_my_wine');
	    }
	 	
	    // Validation and Filtering is sorely needed!!
	    // Seriously, I'm a bad person for leaving that out.
	    $check = Rating::check_validator(Input::all());
	    if($check == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
	    } else {
	    	$rating->user_id = $check['user_id'];
			$rating->save();
			$error_code = ApiResponse::OK;
			$data = $rating->toArray();	    
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
		$rating = Rating::where('id', $id)->first();
		if($rating) {
			$error_code = ApiResponse::OK;
       	 	$data = $rating->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
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
		$rating = Rating::where('id', $id)->first();
 
	    if ( Request::get('user_id') ) {
	        $rating->user_id = Request::get('user_id');
	    }
	    if ( Request::get('wine_unique_id') ) {
	        $rating->wine_unique_id = Request::get('wine_unique_id');
	    }
	 	if (Request::get('rate')) {  
	    	$rating->rate = Request::get('rate');
	    }
	    if (Request::get('content')) {
	        $rating->content = Request::get('content');
	    }
	    if (Request::get('like_count')) {
	        $rating->like_count = Request::get('like_count');
	    }
	    if (Request::get('comment_count')) {
	        $rating->comment_count = Request::get('comment_count');
	    }
	    if (Request::get('is_my_wine')) {
	        $rating->is_my_wine = Request::get('is_my_wine');
	    }
	    
	    $check = Rating::check(Input::all());
	    if($check == 'FALSE') {
	    	$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
	    } else {
	    	$rating->user_id = $check['user_id'];
			$rating->save();
			$error_code = ApiResponse::OK;
			$data = $rating->toArray();	    
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
		$rating = Rating::where('id', $id)->first();
 		if($rating) {
 			$rating->delete();
	 		$error_code = ApiResponse::OK;
	 		$data = 'Rating Deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_RATING;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}
}
