<?php

class WishlistController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		$page = $pagination['page'];
		$limit = $pagination['limit'];
		if ($user_id) {
			if (User::where('user_id',$user_id)->first()){
				$profile = Profile::where('user_id', $user_id)->first();
				if ($profile) {
					$wishlist = Wishlist::where('user_id', $user_id)->with('wine')->forPage($page, $limit)->get();
					if (count($wishlist) == 0) {
						if($page == 1) {
							$data = 'No Wine in wishlist';
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
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
	        $data = "Missing user_id";
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
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$input = $this->_getInput();

		$wishlist = new Wishlist;
		$wishlist->user_id = $user_id;
		if (!empty($input['wine_unique_id'])) {
			$wishlist->wine_unique_id = $input['wine_unique_id'];
			$wine_wishlist = Wishlist::where('wine_unique_id', $wishlist->wine_unique_id)->where('user_id',$user_id)->first();
			if($wine_wishlist) {
				$error_code = ApiResponse::DUPLICATED_WISHLIST_ADD;
				$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WISHLIST_ADD);
			} else {
				$wishlist->save();
				$data = $wishlist->toArray();
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
	public function destroy($wine_unique_id)
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$wishlist = Wishlist::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
	    if ($wishlist) {
	 		$wishlist->delete();
	 		$data = 'wine in wishlist is deleted';

 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_WINE_WISHLIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_WINE_WISHLIST);
	    } 
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


}
