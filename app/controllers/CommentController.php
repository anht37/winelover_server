<?php

class CommentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index($rating_id)
	{
		$result = Comment::getListComment($rating_id);
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
	public function store($rating_id)
	{
		$result = Comment::createNewComment($rating_id, $this->_getInput());
        return Response::json($result);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($rating_id, $id)
	{
		$result = Comment::showCommentDetail($rating_id, $id);
        return Response::json($result);
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
	public function update($rating_id, $id)
	{
	    $result = Comment::updateCommentDetail($rating_id, $id, $this->_getInput());
        return Response::json($result);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($rating_id, $id)
	{
		$result = Comment::deleteComment($rating_id, $id);
        return Response::json($result);
	}

	public function display_error($id) {

        return Response::json(
            array("code" => ApiResponse::URL_NOT_EXIST, "data" => array(
               "message" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST),
               "url"     => Request::fullUrl()
            ))
        );
    }

    protected function _getInput() {
        $input = json_decode(Input::get("data"), true);
        if($input == null) {
            $input = array();
        }
        return $input;
    }
}
