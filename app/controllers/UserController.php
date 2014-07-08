<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
        return $this->_createResponse("index");
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
        return $this->_createResponse("create");
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
        return $this->_createResponse("store");
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
        return $this->_createResponse("show");
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
        return $this->_createResponse("edit");
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
        return $this->_createResponse("update");
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
        return $this->_createResponse("destroy");
	}


    private function _createResponse($action) {
        return Response::json(array(
            'data' => array(
                'fromAction' => $action,
                'method' => Request::method(),
                'header' => Request::header(),
                'uri' => Request::path(),
                'body' => Input::all(),
            ),
        ));
    }

    public function register() {
        $result = User::register($this->_getInput());
        return Response::json($result);
    }

    public function push_notification() {
        $result = Device::push_notification($this->_getInput());
        return Response::json($result);
    }

    public function login() {
        $result = User::login($this->_getInput());
        return Response::json($result);
    }

    public function logout() {
        $result = Login::logout($this->_getInput());
        return Response::json($result);
    }

    public function forgot_password() {

    }

    private function _getInput() {
        $input = json_decode(Input::get("data"), true);
        if($input == null) {
            $input = array();
        }
        return $input;
    }

}
