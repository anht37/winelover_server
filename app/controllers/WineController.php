<?php

class WineController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		$page = $pagination['page'];
		$limit = $pagination['limit'];
		$wine = Wine::with('winery')->forPage($page, $limit)->get();
		if(count($wine) == 0) {
			if($page == 1) {
				$data = "Don't have any Wine";
			} else {
				$error_code = ApiResponse::URL_NOT_EXIST;
            	$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
			}
			
		} else {
			foreach ($wine as $wines) {
				$wines->winery_id = $wines->winery->brand_name;
				if($wines->image_url != null) {
	            	$wines->image_url = URL::asset($wines->image_url);
	            }   
	            if($wines->wine_flag != null) {
	            	$wines->wine_flag = URL::asset($wines->wine_flag);
	            } 
			}
			$data = $wine->toArray();
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

		$wine = new Wine;
		$input = $this->_getInput();
		$error_code = ApiResponse::OK;
		
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

	            $data = $wine;
		 	} else {
		 		$error_code = ApiResponse::UNAVAILABLE_WINERY;
	            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
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
	public function show($wine_id)
	{
		$user_id = Session::get('user_id');
		$wine = Wine::where('wine_id', $wine_id)->with('winery')->first();
		$error_code = ApiResponse::OK;
    	if($wine) {
    		$country_name = Country::where('id',$wine->winery->country_id)->first()->country_name;
    		$wine_note = Winenote::where('wine_unique_id', $wine->wine_unique_id)->where('user_id',$user_id)->first();
    		if($wine_note) {
    			$wine->winenote = $wine_note->note;
    		} else {
    			$wine->winenote = "Don't have any note";
    		}
    		$wine->winery->country_id = $country_name;
    		$rating_user = Rating::where('wine_unique_id', $wine->wine_unique_id)->where('user_id',$user_id)->with('profile')->first();
    		if(count($rating_user) == 0) {
            	$rating_user = "Don't have any rate !";
            }
            $rating = Rating::where('wine_unique_id', $wine->wine_unique_id)->whereNotIn('user_id',[$user_id])->with('profile')->get();
            if(count($rating) == 0) {
            	$rating = "Don't have any rate !";
            }
            if($wine->image_url != null) {
            	$wine->image_url = URL::asset($wine->image_url);
            }   
            if($wine->wine_flag != null) {
            	$wine->wine_flag = URL::asset($wine->wine_flag);
            } 
			$data = array('wine' => $wine,'rate_user' => $rating_user ,'rate' => $rating );
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		}
 		return Response::json(array("code" => $error_code, "data" => $data));
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
		$error_code = ApiResponse::OK;
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
	    
	    return Response::json(array("code" => $error_code, "data" => $data));
 
	    
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
		$error_code = ApiResponse::OK;
 
	    if($wine) {
 			$wine->delete();
	 		
	 		$data = 'Wine deleted';
 		} else {
 			$error_code = ApiResponse::UNAVAILABLE_WINE;
	        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
	    } 
	 	return Response::json(array("code" => $error_code, "data" => $data));
	    
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

  //   public function getWine_detail($wine_unique_id)
  //   {
  //   	$wine = Wine::where('wine_unique_id', $wine_unique_id)->with('winery')->first();
  //   	if($wine) {
  //           $rating = Rating::where('wine_unique_id', $wine_unique_id)->with('profile')->get();
  //           if(count($rating) == 0) {
  //           	$rating = "Don't have any rate !";
  //           }
  //           if($wine->image_url != null) {
  //           	$wine->image_url = URL::asset($wine->image_url);
  //           }   
            
		// 	$error_code = ApiResponse::OK;
		// } else {
		// 	$error_code = ApiResponse::UNAVAILABLE_WINE;
  //           $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		// }
 	// 	return array("code" => $error_code, "data" => array('wine' => $wine, 'rate' => $rating ));
  //   }

}
