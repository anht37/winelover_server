<?php


class User extends Eloquent {

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

    public static function boot() {
        parent::boot();
        self::creating(function($user) {
            $user->user_id = Uuid::generate(4);
            if(!empty($user->password)) {
                $user->password = Hash::make($user->password);
            }
        });
    }
}
