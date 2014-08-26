<?php

class ProfileController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
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
		
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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
	public function update($user_id)
	{
		$result = Profile::updateProfile($user_id, $this->_getInput());
        return Response::json($result);
	}

	public function uploadImage($user_id)
	{
		$result = Profile::uploadImage($user_id);
        return Response::json($result);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getProfile_basic_user($user_id)
	{
		$result = Profile::getProfileBasicUser($user_id);
        return Response::json($result);
	}

	public function getProfile_wishlist_user($user_id)
	{
		$result = Profile::getProfileWishlistUser($user_id);
        return Response::json($result);
	}

	public function getProfile_Top_rate($user_id)
	{	
		$result = Profile::getProfieTopRate($user_id);
        return Response::json($result);
	}

	public function getProfile_Last_rate($user_id)
	{	
		$result = Profile::getProfieLastRate($user_id);
        return Response::json($result);
	}
}
