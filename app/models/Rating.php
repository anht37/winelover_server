<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Rating extends Eloquent {
    
    use SoftDeletingTrait;
    protected $table = 'ratings';
    protected $primaryKey = 'id';

    public static function check_validator($input)
    {	   	
        $validator = Validator::make(
            $input,
            array(
                'like_count' => 'integer',
                'comment_count' => 'integer',
                'is_my_wine' => 'in:0,1',
                'rate' => 'between:0,5',

            )
        );
        //validate params
        if ($validator->fails()) {
            return "FALSE";
        } else {
        	if($input['rate']) {
	    		if(($input['rate']*10)%5==0) {
			    	return $input;
	    		} else {
			    	return "FALSE";
	    		}
	    	} else {
			    return $input;
		    }
        	
        }
 	}

    public static function getUser_id($input)
    {
        $sess_user = Login::where('session_id', $input)->first();
        return $sess_user['user_id'];
    }

    public static function check_rating($input)
    {   
        
        
        $validator = Validator::make(
            $input,
            array(
                
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