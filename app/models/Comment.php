<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait; 
class Comment extends Eloquent {
	use SoftDeletingTrait;
 
    protected $table = 'comments';
    protected $primaryKey = 'id';
 

 	public static function check_user($input)
    {	
    	$input['user_id'] = Uuid::generate(4);
    	
        $validator = Validator::make(
            $input,
            array(
                'user_id' => 'exists:users,user_id',
                //'rating_id' => 'exists:ratings,rating_id',
            )
        );
        //validate params
        if ($validator->fails()) {
            return "FALSE";
        } else {
			    return $input;
		}
        
 	}
 	public static function check_rating($input)
    {	
    	//$input['user_id'] = Uuid::generate(4);
    	
        $validator = Validator::make(
            $input,
            array(
                //'user_id' => 'exists:users,user_id',
                'rating_id' => 'exists:ratings,id',
            )
        );
        //validate params
        if ($validator->fails()) {
            return "FALSE";
        } else {
			    return $input;
		}
        
 	}
 	
}