<?php


class User extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
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
}
