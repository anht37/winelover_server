<?php

class TimelineController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		$rating_user = Rating::where('user_id', $user_id)->with('profile')->with('wine')->get();

		$user_follow = Follow::where('from_id', $user_id)->orderBy('updated_at', 'asc')->get();
		$timeline = array();
		foreach($user_follow as $user) {
			$profiles = Profile::where('user_id', $user->to_id)->with('rating')->first();
			if($profiles){
				foreach ($profiles->rating as $rating) {

					$rating_wine = Wine::where('wine_unique_id', $rating->wine_unique_id)->first();
					$rating->wine_unique_id = $rating_wine;
				}
				$timeline[] = $profiles;
			}
		}
		$data = array('user_timeline' => $rating_user, 'user_follow_rating' => $timeline);
		// $paginator = $timeline;
	 //    $perPage = Input::get('per_Page', 15);   
	 //    $page = Input::get('page', 1);
	 //    if ($page > count($paginator) or $page < 1) { $page = 1; }
	 //    $offset = ($page * $perPage) - $perPage;
	 //    $articles = array_slice($paginator,$offset,$perPage);
	 //    $datas = Paginator::make($articles, count($paginator), $perPage);

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
	public function destroy($id)
	{
		//
	}


}
