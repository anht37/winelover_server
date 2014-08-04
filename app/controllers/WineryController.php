<?php

class WineryController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */


	public function index()
	{
		$winery = Winery::all();
		
		$error_code = ApiResponse::OK;
        $data = $winery->toArray();
	    
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
		$winery = new Winery;
		$error_code = ApiResponse::OK;
		$input = $this->_getInput();
	    
	    if(!empty($input['brand_name'])) {
	    	$winery->brand_name = $input['brand_name'];

		    if (!empty($input['country_id'])) {
		        $winery->country_id = $input['country_id'];
		    }
		 	if (!empty($input['region'])) {
		        $winery->region = $input['region'];
		    }
		    if (!empty($input['description'])) {
		        $winery->description = $input['description'];
		    }
		 
		    // Validation and Filtering is sorely needed!!
		    // Seriously, I'm a bad person for leaving that out.
		    $winery->save();
	        $data = $winery->toArray();
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
		$error_code = ApiResponse::OK;
		$winery = Winery::where('id', $id)->first();
        if($winery) {
       	 	$data = $winery->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINERY;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
		}
 		
	    return Response::json(array("code" => $error_code, "data" => $data));
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
		$winery = Winery::where('id', $id)->first();
		$input = $this->_getInput();
		$error_code = ApiResponse::OK;
		if($winery) {
	 		if(!empty($input)) {
			    if (!empty($input['brand_name'])) {
			        $winery->brand_name = $input['brand_name'];
			    }
			    if (!empty($input['country_id'])) {
			        $winery->country_id = $input['country_id'];
			    }
			 	if (!empty($input['region'])) {
			        $winery->region = $input['region'];
			    }
			    if (!empty($input['description'])) {
			        $winery->description = $input['description'];
			    }
			    $winery->save();
		 		
		        $data = $winery->toArray();
		    } else {
		    	$error_code = ApiResponse::MISSING_PARAMS;
		        $data = $input;
		    }
	   	} else {
	   		$error_code = ApiResponse::UNAVAILABLE_WINERY;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
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
		$winery = Winery::where('id', $id);
 		$error_code = ApiResponse::OK;
	    if($winery) {
 			$winery->delete();
	 		
	 		$data = 'Winery deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_WINERY;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
	    } 
	    return Response::json(array("code" => $error_code, "data" => $data));
	}


}
