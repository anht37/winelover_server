<?php

class WineController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$result = Wine::getListWine();
        return Response::json($result);
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

		$result = Wine::createNewWine($this->_getInput());
        return Response::json($result);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($wine_id)
	{
		$result = Wine::getWineDetail($wine_id);
        return Response::json($result);
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
		$result = Wine::updateWineDetail($wine_id, $this->_getInput());
        return Response::json($result);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($wine_id)
	{
		$result = Wine::deleteWine($wine_id);
        return Response::json($result);
	}

	public function scan()
    {
    	$user_id = Session::get('user_id');
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
            if($result > 0) 
            {
            	$wine = Wine::where('wine_id', $result)->first();
            	$rating = new Rating;
            	$rating->user_id = $user_id;
            	$rating->wine_unique_id = $wine->wine_unique_id;

            	$rating_profile = Profile::where('user_id',$rating->user_id)->first();
                if($rating_profile != null) {
                    $rating_profile->rate_count = $rating_profile->rate_count + 1;
                    $rating_profile->save(); 
                }
                
                $rating_rate = $wine->average_rate * $wine->rate_count;
                $wine->rate_count = $wine->rate_count + 1;
                $wine->save(); 

            	$rating->save();
            }
        }
        return Response::json(array("result" => $result));
    }
    
    public function search()
	{
		$input = $this->_getInput();
		if (!empty($input['text'])) { 
		  	$result = Wine::searchWinefromMywine($input['text']);
		  	return Response::json($result);
		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
		    $data = $input;
		    return Response::json(array("code" => $error_code, "data" => $data));
		}
	}
}
