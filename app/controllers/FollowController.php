<?php

class FollowController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    	$result = Follow::getLishFollow();
        return Response::json($result); 	   
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
		$result = Follow::createNewFollow($this->_getInput());
        return Response::json($result); 	   
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// $follow = Follow::where('id', $id)->first();
  //       $error_code = ApiResponse::OK;
  //       if($follow) {
  //      	 	$data = $follow->toArray();
		// } else {
		// 	$error_code = ApiResponse::NOT_EXISTED_FOLLOW;
	 //        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_FOLLOW);
		// }
 		
	 //    return Response::json(array("code" => $error_code, "data" => $data));
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
	public function destroy($follow_id)
	{	
		$result = Follow::deleteFollow($follow_id);
        return Response::json($result); 	   
	}

	public function get_list_follower()
	{
		$result = Follow::getListFollower();
        return Response::json($result); 	   
	}

	public function get_list_following()
	{
		$result = Follow::getListFollowing();
        return Response::json($result); 	   
	}

}
