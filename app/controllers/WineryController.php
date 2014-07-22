<?php

class WineryController extends ApiController {

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
		$winery = Winery::all();
		
		$error_code = ApiResponse::OK;
        $data = $winery->toArray();
	    
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
		$winery = new Winery;
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

		    $error_code = ApiResponse::OK;
	        $data = $winery->toArray();
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
		$winery = Winery::where('id', $id)->first();
        if($winery) {
			$error_code = ApiResponse::OK;
       	 	$data = $winery->toArray();
		} else {
			$error_code = ApiResponse::URL_NOT_EXIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
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
		$winery = Winery::where('id', $id)->first();
		$input = $this->_getInput();
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
		 		$error_code = ApiResponse::OK;
		        $data = $winery->toArray();
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
		$winery = Winery::where('id', $id);
 
	    if($winery) {
 			$winery->delete();
	 		$error_code = ApiResponse::OK;
	 		$data = 'Winery deleted';
 		} else {
 			$error_code = ApiResponse::URL_NOT_EXIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}


}
