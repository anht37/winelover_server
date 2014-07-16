<?php

class WineController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$wine = Wine::all();

	    return Response::json(array(
	        'error' => false,
	        'wines' => $wine->toArray()),
	        200
	    );
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
	    
	    $wine->year = Request::get('year');
	    $wine->winery_id = Request::get('winery_id');
	    $wine->image_url = Request::get('image_url');
	    $wine->average_price = Request::get('average_price');
	    $wine->average_rate = Request::get('average_rate');
	 
	    // Validation and Filtering is sorely needed!!
	    // Seriously, I'm a bad person for leaving that out.
	 
	    $wine->save();

	    return Response::json(array(
	        'error' => false,
	        'wines' => $wine->toArray()),
	        200
	    );
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($wine_id)
	{
		$wine = Wine::where('wine_id', $wine_id)
            ->take(1)
            ->get();
 		//dd($wine);
	    return Response::json(array(
	        'error' => false,
	        'wines' => $wine->toArray()),
	        200
	    );
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
 
	    if ( Request::get('year') )
	    {
	        $wine->year = Request::get('year');
	    }
	    if ( Request::get('winery_id') )
	    {
	        $wine->winery_id = Request::get('winery_id');
	    }
	 	if ( Request::get('image_url') )
	    {
	        $wine->image_url = Request::get('image_url');
	    }
	    if (Request::get('average_price'))
	    {
	        $wine->average_price = Request::get('average_price');
	    }
	    if ( Request::get('average_rate') )
	    {
	        $wine->average_rate = Request::get('average_rate');
	    }

	    $wine->wine_unique_id = $wine->wine_id . '_' . $wine->year;

	    $wine->save();
 
	    return Response::json(array(
	        'error' => false,
	        'message' => 'wine updated'),
	        200
	    );
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
 
	    $wine->delete();
	 
	    return Response::json(array(
	        'error' => false,
	        'message' => 'wine deleted'),
	        200
	        );
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
