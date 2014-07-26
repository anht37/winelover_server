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
		
		$wishlist = Wishlist::where('user_id', $user_id)->get();
		if (count($wishlist) > 0) {
			$data = $wishlist->toArray();
		} else {	
		    $data = 'No Wine in wishlist';
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
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$input = $this->_getInput();

		$wishlist = new Wishlist;
		$wishlist->user_id = $user_id;
		if(!empty($input['wine_unique_id'])) {
			$wishlist->wine_unique_id = $input['wine_unique_id'];
			if(Wishlist::where('wine_unique_id', $wishlist->wine_unique_id)->first()) {
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
		$wishlist = Wishlist::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
	    if($wishlist) {
	 		
	 		$error_code = ApiResponse::OK;
	 		$data = 'wine in wishlist is deleted';

 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_WINE_WISHLIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_WINE_WISHLIST);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}


}
