<?php


class User extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $user->user_id = Uuid::generate(4);
        });
        self::saving(function ($user) {
            if (!empty($user->password)) {
                $user->password = Hash::make($user->password);
            }
        });
    }

    public static function register($input)
    {
        $error_code = ApiResponse::OK;
        $validator = Validator::make(
            $input,
            array(
                'email' => 'required|email',
                'password' => 'required',
                'device_id' => 'required'
            )
        );
        //validate params
        if ($validator->fails()) {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }else {

            //check email existed
            if (User::where('email', $input['email'])->first() != null) {
                $error_code = ApiResponse::EXISTED_EMAIL;
                $data = ApiResponse::getErrorContent(ApiResponse::EXISTED_EMAIL);
            } else {
                $user = new User();
                $user->email = $input['email'];
                $user->password = $input['password'];
                $user->device_id = $input['device_id'];
                if($user->save()){
                    $data = "ok";
                }
            }
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function login($input) {
        $error_code = ApiResponse::OK;
        $new_user = false;
        $user = null;
        if(array_key_exists('fb_id', $input) && !empty($input['fb_id'])) {
            $user = User::where('fb_id', $input['fb_id'])->first();
            if($user == null) {
                $user = new User();
                $user->fb_id = $input['fb_id'];
                $user->save();
                $user = User::find($user->id);
                $new_user = true;
            }
        } else {
            $validator = Validator::make(
                $input,
                array(
                    'email' => 'required|email',
                    'password' => 'required',
                )
            );
            //validate param
            if ($validator->fails()) {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = $input;
            }else {
                $user = User::where('email', $input['email'])->first();
                if ($user == null || (Hash::check($input['password'], $user->password) == false)) {
                    $error_code = ApiResponse::WRONG_AUTH;
                    $data = ApiResponse::getErrorContent(ApiResponse::WRONG_AUTH);
                }
            }
        }

        if($error_code == ApiResponse::OK) {
            $login = new Login();
            $login->user_id = $user->user_id;
            $login->session_id = md5($user->user_id.microtime());
            $login->expired_at = \Carbon\Carbon::now()->addYears(5);
            if($login->save()){
                $data = array(
                    "session" => $login->session_id,
                    "user_id" => $login->user_id,
                    "new_user" => $new_user
                );
            }
        }
        return array("code" => $error_code, "data" => $data);
    }
}
