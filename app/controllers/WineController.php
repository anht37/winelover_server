<?php

class WineController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		if(Input::get('page')) {
			$getPage = Input::get('page');
			if(Input::get('per_page')) {
				$getLimit = Input::get('per_page');
			} else {
				$getLimit = 10;		
			}
			$paginate = Wine::paginate($getPage, $getLimit);
			if($paginate !== false) {
				$page = $paginate['page'];
				$limit = $paginate['limit'];
				
			} else {
				$error_code = ApiResponse::URL_NOT_EXIST;
		       	$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
		     	return array("code" => $error_code, "data" => $data);
			}

		} else {
			$page = 1;
			$limit = 10;
		}

		$wine = Wine::with('winery')->forPage($page, $limit)->get();
		
		foreach ($wine as $wines) {
			$wines->winery_id = $wines->winery->brand_name;
		}
		
		$error_code = ApiResponse::OK;
        $data = $wine->toArray();
	    
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

		$wine = new Wine;
		$input = $this->_getInput();
		
		if(!empty($input['name']) && !empty($input['year']) && !empty($input['winery_id'])) {
			$wine->name = $input['name'];
		    $wine->year = $input['year'];
		    $wine->winery_id = $input['winery_id'];
		    if (!empty($input['image_url'])) {
		        $wine->image_url = $input['image_url'];
		    }
		    if (!empty($input['average_price'])) {
		        $wine->average_price = $input['average_price'];
		    }
		    if ( !empty($input['average_rate']) ) {
		        $wine->average_rate = $input['average_rate'];
		    }
		    if (!empty($input['wine_type']) ) {
		        $wine->wine_type = $input['wine_type'];
		    }
		 	
		    // Validation and Filtering is sorely needed!!
		    // Seriously, I'm a bad person for leaving that out.



		 	if(Winery::where('id',$wine->winery_id)->first()) {
		 		$wine->save();
		 		
		 		$wine->wine_unique_id = $wine->wine_id . '_' . $wine->year;
		    	$wine->save();

		 		$error_code = ApiResponse::OK;
	            $data = $wine;
		 	} else {
		 		$error_code = ApiResponse::UNAVAILABLE_WINERY;
	            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
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
	public function show($wine_id)
	{
		if($wine = Wine::where('wine_id', $wine_id)->first()) {
            $winery = Winery::where('id',$wine->winery_id)->first();
            if($winery) {
                $wine->winery = Winery::where('id',$wine->winery_id)->first()->brand_name;
            }else {
                $wine->winery = '';
            }
            $wine->image_url = URL::asset($wine->image_url);

			$error_code = ApiResponse::OK;
            $data = $wine->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		}
 		return array("code" => $error_code, "data" => $data);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($wine_id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($wine_id)
	{
		$wine = Wine::where('wine_id', $wine_id)->first();
		$input = $this->_getInput();
		if($wine) {
	 		if(!empty($input)) {
	 			if ( !empty($input['name']) ) {
		        $wine->name = $input['name'];
			    }
			    if ( !empty($input['year']) ) {
			        $wine->year = $input['year'];
			    }
			    if ( !empty($input['winery_id']) ) {
			        $wine->winery_id = $input['winery_id'];
			    }
			 	if ( !empty($input['image_url']) ) {
			        $wine->image_url = $input['image_url'];
			    }
			    if (!empty($input['average_price'])) {
			        $wine->average_price = $input['average_price'];
			    }
			    if ( !empty($input['average_rate']) ) {
			        $wine->average_rate = $input['average_rate'];
			    }
			    if ( !empty($input['wine_type']) ) {
			        $wine->wine_type = $input['wine_type'];
			    }
			    $wine->wine_unique_id = $wine->wine_id . '_' . $wine->year;

			    if(Winery::where('id',$wine->winery_id)->first()) {
			 		$wine->save();

			 		$error_code = ApiResponse::OK;
		            $data = $wine;
			 	} else {
			 		$error_code = ApiResponse::UNAVAILABLE_WINERY;
		            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
			 	}
	 		} else {
	 		 	$error_code = ApiResponse::MISSING_PARAMS;
		        $data = $input;
	 		}
	 	} else {
	 		$error_code = ApiResponse::UNAVAILABLE_WINE;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
	 	}
	    
	    return array("code" => $error_code, "data" => $data);
 
	    
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($wine_id)
	{
		$wine = Wine::where('wine_id', $wine_id);
 
	    if($wine) {
 			$wine->delete();
	 		$error_code = ApiResponse::OK;
	 		$data = 'Wine deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_WINE;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
	    } 
	 	return array("code" => $error_code, "data" => $data);
	    
	}
	public function scan()
    {
        if(!Input::hasFile('file')) {
            return Response::json(array("result" => "No file"));
        }
        $result = "";
        $file = Input::file('file');
        $destinationPath = public_path()."/uploads/";
        $file_name = date('YmdHis').'_'.$file->getClientOriginalName();
        $uploadSuccess = $file->move($destinationPath,$file_name);
        if($uploadSuccess) {
            $result = Wine::scan($destinationPath.$file_name);
        }
        return Response::json(array("result" => $result));
    }

}
