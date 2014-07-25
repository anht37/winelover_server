<?php

class UserController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
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
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
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
        $result = User::forgot_password($this->_getInput());
        return Response::json($result);
    }
    public function timeline() {
    	$user_id = Session::get('user_id');
    	$result = Rating::timeline($user_id);
        return Response::json($result);
    }

}
