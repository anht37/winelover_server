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
		$input = $this->_getInput();
		$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		if(User::where('user_id',$user_id)->first()){
			$profile = Profile::where('user_id', $user_id)->first();

			if($profile) {
		 		if(!empty($input) || Input::hasFile('file')) {
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
				    if (Input::hasFile('file')) {
					    $file = Input::file('file');
					    $destinationPath    = public_path() . '/images/';
						$filename           = $file->getClientOriginalName();
						$extension          = $file->getClientOriginalExtension();
						$upload_success     = $file->move($destinationPath, $filename);
					    $profile->image = 'images/' . $filename;
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
		//
	}

	public function getProfile_basic_user($user_id)
	{
		$error_code = ApiResponse::OK;
		$user_login = Session::get('user_id');

		if(User::where('user_id',$user_id)->first()){
		$profile = Profile::where('user_id', $user_id)->first();
	
			if ($profile->image != null) {
	            $profile->image = URL::asset($profile->image);   
	        }
	        if ($profile->country_id != null) {
	            $country = Country::where('id', $profile->country_id)->first();
	            $profile->country_name = $country->country_name;
	            $profile->country_flag = URL::asset($country->flag_url);   
	        }

	        $wishlist = Wishlist::where('user_id', $user_id)->get();
	        if($wishlist){
	            $profile->wishlist_count = count($wishlist);
	        } else {
	            $profile->wishlist_count = 0;
	        }
	        if ($user_id != $user_login) {
	            $follow = Follow::where('from_id', $user_login)->where('to_id', $user_id)->first();
		        if($follow) {
		            $profile->is_follow = true;
		        } else {
		            $profile->is_follow = false;
		        }
	        }
			$data = $profile->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
			$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
		return Response::json(array("code" => $error_code, "data" => $data));
	}

	public function getProfile_wishlist_user($user_id)
	{
		$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			return Response::json(array("code" => ApiResponse::URL_NOT_EXIST, "data" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST)));
		}
		$page = $pagination['page'];
		$limit = $pagination['limit'];
		
		if(User::where('user_id',$user_id)->first()){
			$profile = Profile::where('user_id', $user_id)->first();
			
			if ($profile->image != null) {
	            $profile->image = URL::asset($profile->image);   
	        }
			$wishlist = Wishlist::where('user_id', $user_id)->with('wine')->forPage($page, $limit)->get();
			if (count($wishlist) == 0) {
				if($page == 1) {
					$data = array();
				} else {
					$error_code = ApiResponse::URL_NOT_EXIST;
    				 $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
				}
				
			} else {	
			    foreach ($wishlist as $wishlists) {
					$wishlists->winery = Winery::where('id', $wishlists->wine->winery_id)->first();

					if($wishlists->wine->image_url != null) {
	            		$wishlists->wine->image_url = URL::asset($wishlists->wine->image_url);
		            }

		            if($wishlists->wine->wine_flag != null) {
		            	$wishlists->wine->wine_flag = URL::asset($wishlists->wine->wine_flag);
		            } 
				}
				
				$data = $wishlist->toArray();
			}
		} else { 
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}

		return Response::json(array("code" => $error_code, "data" => $data));
	}

	public function getProfile_Top_rate($user_id)
	{	
		$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			return Response::json(array("code" => ApiResponse::URL_NOT_EXIST, "data" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST)));
		}
		$page = $pagination['page'];
		$limit = $pagination['limit'];

		if(User::where('user_id',$user_id)->first()) {
			$top_rate = Rating::where('user_id',$user_id)->orderBy('rate', 'desc')->with('wine')->forPage($page, $limit)->get();
			foreach ($top_rate as $top_rates) {
				$top_rates->winery = Winery::where('id',$top_rates->wine->winery_id)->first();
				if($top_rates->wine->image_url != null) {
		            $top_rates->wine->image_url = URL::asset($top_rates->wine->image_url);
			    }

			    if($top_rates->wine->wine_flag != null) {
			        $top_rates->wine->wine_flag = URL::asset($top_rates->wine->wine_flag);
			    } 
			}
			$data = $top_rate->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
		return Response::json(array("code" => $error_code, "data" => $data));
	}

}
