<?php

class WinenoteController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
    	$winenote = new Winenote;
		$input = $this->_getInput();
		$error_code = ApiResponse::OK;
		
		if(!empty($input['wine_unique_id']) && !empty($input['note'])) {
			$winenote->wine_unique_id = $input['wine_unique_id'];
			$winenote->note = $input['note'];
		 	$winenote->user_id = $user_id;
		    // Validation and Filtering is sorely needed!!
		    // Seriously, I'm a bad person for leaving that out.
		 	if(Wine::where('wine_unique_id',$winenote->wine_unique_id)->first()) {
		 		if(Winenote::where('wine_unique_id',$winenote->wine_unique_id)->where('user_id', $winenote->user_id)->first()) {
		 			$error_code = ApiResponse::DUPLICATED_WINE_NOTE_ADD;
	            	$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WINE_NOTE_ADD);
		 		} else {
		 			$winenote->save();
	            	$data = $winenote;
		 		}
		 		
		 	} else {
		 		$error_code = ApiResponse::UNAVAILABLE_WINE;
	            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
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
	public function update($wine_unique_id)
	{
		$user_id = Session::get('user_id');
		if(Wine::where('wine_unique_id',$wine_unique_id)->first()) {
	    	$winenote = Winenote::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
			$input = $this->_getInput();
			$error_code = ApiResponse::OK;
			if ($winenote) {
				if(!empty($input)) {
					if(!empty($input['note'])) {
						$winenote->note = $input['note'];
						$winenote->save();
				        $data = $winenote;
					 	
					} else {
						$error_code = ApiResponse::MISSING_PARAMS;
				        $data = $input;
					}
				} else {
					$error_code = ApiResponse::MISSING_PARAMS;
				    $data = "Missing note";
				}
			} else {
				$error_code = ApiResponse::UNAVAILABLE_WINE_NOTE;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE;
			$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		}
	    return Response::json(array("code" => $error_code, "data" => $data));
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
    	$winenote = Winenote::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
		$error_code = ApiResponse::OK;
		if($winenote) {
			$winenote->delete();
			$data = 'Wine note is deleted';
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE_NOTE;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE);
		}
		return Response::json(array("code" => $error_code, "data" => $data));
	}


}
