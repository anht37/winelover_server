<?php


class User extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    protected $guarded = array();
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
            $profile = Profile::where('user_id', $user->user_id)->first();
            if(empty($profile)) {
                $profile = new Profile;
                $profile->user_id = $user->user_id;
                $profile->save();
            }
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

    public static function forgot_password($input) {
        $error_code = ApiResponse::OK;
        $validator = Validator::make(
            $input,
            array(
                'email' => 'required|email',
            )
        );
        //validate params
        if ($validator->fails()) {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }else {
            //check email existed
            if (User::where('email', $input['email'])->first() == null) {
                $error_code = ApiResponse::NOT_EXISTED_EMAIL;
                $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_EMAIL);
            } else {
                //TODO need implement send email
                $data = "ok";
            }
        }
        return array("code" => $error_code, "data" => $data);
    }
    public function profile()
    {
        return $this->belongsTo('Profile','user_id', 'user_id');
    }

    public static function feature_users($user_id)
    {
        $error_code = ApiResponse::OK;
        // $pagination = ApiResponse::pagination();
        // $page = $pagination['page'];
        // $limit = $pagination['limit'];
        $user = User::whereNotIn('user_id', [$user_id])->orderBy(DB::raw("RAND()"))->with('profile')->take(10)->get();
        if($user) {
            foreach ($user as $users) {
                if ($users->profile->image != null) {
                        $users->profile->image = URL::asset($users->profile->image);   
                    }
                $follow = Follow::where('from_id', $user_id)->where('to_id', $users->user_id)->first();
                if($follow) {
                        $users->is_follow = true;
                    } else {
                        $users->is_follow = false;
                    }
            }  
        }
        $data = $user;
        return array("code" => $error_code, "data" => $data);
    }    

    public static function getFriendFB($input)
    {
        $user_id = Session::get('user_id');
        $error_code = ApiResponse::OK;
        $data = array();
        if(!empty($input)) {
            foreach ($input as $fb_id) {
                $user = User::where('fb_id', $fb_id)->with('profile')->first();
                if($user && $user->user_id != $user_id) {
                    $data[] = $user->toArray();
                }
            }
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
        }
        return array("code" => $error_code, "data" => $data);
    }
}
