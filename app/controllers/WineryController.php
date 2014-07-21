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
	    
	    $winery->brand_name = Request::get('brand_name');
	    $winery->country_id = Request::get('country_id');
	    $winery->region = Request::get('region');
	    $winery->description = Request::get('description');
	 
	    // Validation and Filtering is sorely needed!!
	    // Seriously, I'm a bad person for leaving that out.
	    $winery->save();

	    $error_code = ApiResponse::OK;
        $data = $winery->toArray();
	    
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
 
	    if ( Request::get('brand_name') ) {
	        $winery->brand_name = Request::get('brand_name');
	    }
	    if ( Request::get('country_id') ) {
	        $winery->country_id = Request::get('country_id');
	    }
	 	if ( Request::get('region') ) {
	        $winery->region = Request::get('region');
	    }
	    if (Request::get('description')) {
	        $winery->description = Request::get('description');
	    }
	    $winery->save();
 		$error_code = ApiResponse::OK;
        $data = $winery->toArray();
	    
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
