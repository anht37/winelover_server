<?php

class FollowController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$follow = Follow::all();
		
		$error_code = ApiResponse::OK;
        $data = $follow->toArray();
	    
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
		$follow = new Follow;
		$input = $this->_getInput();
	    $follow->from_id = Session::get('user_id');
	    if(!empty($input['follow_id'])) {
	    	$follow->to_id = $input['follow_id'];
	    	$user = User::where('user_id', $follow->to_id)->first(); 
		 	if ($user) {
		 		if(Follow::where('from_id',$follow->from_id)->having('to_id', '=', $follow->to_id)->first()) {
		 			
		 			$error_code = ApiResponse::DUPLICATED_FOLLOW;
	        		$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_FOLLOW);
		 		} else {

			 		$follow->save();

				    $error_code = ApiResponse::OK;
			        $data = $follow->toArray();
		 		}

		 	} else {
		 		$error_code = ApiResponse::UNAVAILABLE_USER;
	        	$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
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
		$follow = Follow::where('id', $id)->first();
        if($follow) {
			$error_code = ApiResponse::OK;
       	 	$data = $follow->toArray();
		} else {
			$error_code = ApiResponse::NOT_EXISTED_FOLLOW;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_FOLLOW);
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
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{	
		$from_id = Session::get('user_id');
		$follow = Follow::where('to_id', $id)->having('from_id', '=' , $from_id)->first();

	    if($follow) {
 			$follow->delete();
	 		$error_code = ApiResponse::OK;
	 		$data = 'Follow deleted';
 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_FOLLOW;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_FOLLOW);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}


}
