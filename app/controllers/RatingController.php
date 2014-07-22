<?php

class RatingController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function __construct()
    {
        $this->beforeFilter('session');
    }

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
	    $input = $this->_getInput();
	    
	    $rating->user_id = Rating::getUser_id(Request::header('session'));
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
		    $check = Rating::check_validator(Input::all());
		    if($check == 'FALSE') {
		    	$error_code = ApiResponse::UNAVAILABLE_RATING;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		    } else {
				$rating->save();
				$error_code = ApiResponse::OK;
				$data = $rating->toArray();	    
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
		$input = $this->_getInput();
		if($rating) {
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
