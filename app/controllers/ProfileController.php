<?php

class ProfileController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		if(User::where('user_id',$user_id)->first()){
			$profile = Profile::where('user_id', $user_id)->first();
			if($profile) {
				$data = $profile;
			} else { 
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
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
	public function store()
	{
		// $input = $this->_getInput();
		// $error_code = ApiResponse::OK;
		// $user_id = Session::get('user_id');
		// $profile = Profile::where('user_id', $user_id)->first();

		// if($profile) {
		// 	$error_code = ApiResponse::UNAVAILABLE_USER;
	 //        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);

		// } else {
		// 	if (!empty($input['follower_count'])) { 
		// 	    $profile->follower_count = $input['follower_count'];
		//     }
		//     if (!empty($input['following_count'])) { 
		// 	    $profile->following_count = $input['following_count'];
		//     }
		//     if (!empty($input['rate_count'])) { 
		// 	    $profile->rate_count = $input['rate_count'];
		//     }
		//     if (!empty($input['comment_count'])) { 
		// 	    $profile->comment_count = $input['comment_count'];
		//     }
		//     if (!empty($input['scan_count'])) { 
		// 	    $profile->scan_count = $input['scan_count'];
		//     }
	 // 		if (!empty($input['last_name'])) { 
		// 	    $profile->last_name = $input['last_name'];
		//     }
		// 	if (!empty($input['first_name'])) {  
		// 	    $profile->first_name = $input['first_name'];
		// 	}
		// 	if (!empty($input['bio'])) {  
		// 	    $profile->bio = $input['bio'];
		// 	}	    
		// 	if (!empty($input['country_id'])) {
		// 	    $profile->country_id = $input['country_id'];
		// 	}
		// 	if (!empty($input['pref_id'])) {
		// 	    $profile->pref_id = $input['pref_id'];
		// 	}
		// 	if (!empty($input['alias'])) {
		// 	    $profile->alias = $input['alias'];
		// 	}
		// 	if (!empty($input['image'])) {
		// 	    $profile->image = $input['image'];
		// 	}
		// 	if (!empty($input['website'])) {
		// 	    $profile->website = $input['website'];
		// 	}

		// 	$profile->save();
		// 	$error_code = ApiResponse::OK;
		// 	$data = $profile->toArray();
		// }
	 //    return array("code" => $error_code, "data" => $data);
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
		$input = $this->_getInput();
		$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		if(User::where('user_id',$user_id)->first()){
			$profile = Profile::where('user_id', $user_id)->first();

			if($profile) {
		 		if(!empty($input)) {
		 			if (!empty($input['last_name'])) { 
				    	$profile->last_name = $input['last_name'];
			    	}
				    if (!empty($input['first_name'])) {  
				    	$profile->first_name = $input['first_name'];
				    }
				    if (!empty($input['bio'])) {  
				    	 $profile->bio = $input['bio'];
				    }	    
				    if (!empty($input['country_id'])) {
				        $profile->country_id = $input['country_id'];
				    }
				    if (!empty($input['pref_id'])) {
				        $profile->pref_id = $input['pref_id'];
				    }
				    if (!empty($input['alias'])) {
				        $profile->alias = $input['alias'];
				    }
				    if (!empty($input['image'])) {
				        $profile->image = $input['image'];
				    }
				    if (!empty($input['website'])) {
				        $profile->website = $input['website'];
				    }

					$profile->save();
					$error_code = ApiResponse::OK;
					$data = $profile->toArray();	   
			    } else {
			    	$error_code = ApiResponse::MISSING_PARAMS;
			        $data = $input;
			    }
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
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
		//
	}

	public function getProfile_basic_user($user_id)
	{
		$error_code = ApiResponse::OK;
		if($user_id) {
			if(User::where('user_id',$user_id)->first()){
				$profile = Profile::where('user_id', $user_id)->first();
				if($profile) {
					$data = $profile;
				} else { 
					$error_code = ApiResponse::UNAVAILABLE_USER;
			        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
				}
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
	        $data = "Missing user_id";
		}
		return array("code" => $error_code, "data" => $data);
	}

	public function getProfile_wishlist_user($user_id)
	{
		$error_code = ApiResponse::OK;
		if($user_id) {
			if(User::where('user_id',$user_id)->first()){
				$profile = Profile::where('user_id', $user_id)->first();
				if($profile) {
					$wishlist = Wishlist::where('user_id', $user_id)->get();
					if (count($wishlist) > 0) {
						$data = $wishlist->toArray();
					} else {	
					    $data = 'No Wine in wishlist';
					}
				} else { 
					$error_code = ApiResponse::UNAVAILABLE_USER;
			        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
				}
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
	        $data = "Missing user_id";
		}

		return array("code" => $error_code, "data" => $data);
	}

	public function getProfile_Top_rate($user_id, $per_page)
	{	
		$error_code = ApiResponse::OK;
		$page = 1;
		if($user_id && $per_page) {
			if(User::where('user_id',$user_id)->first()) {
				$check_paginate = Wine::paginate($page, $per_page);
				if ($check_paginate != 'FALSE') {
					$top_rate = Rating::where('user_id',$user_id)->orderBy('rate', 'desc')->forPage($page, $per_page)->get();
					$data = $top_rate;
				} else {
					$error_code = ApiResponse::URL_NOT_EXIST;
		       		$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
				}
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
	        $data = "Missing user_id or per_page";
		}
		return array("code" => $error_code, "data" => $data);
	}

}
