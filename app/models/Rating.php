<?php
 
class Rating extends Eloquent {
 
    protected $table = 'ratings';
    protected $primaryKey = 'id';

    public static function check_validator($input)
    {	
    	$input['user_id'] = Uuid::generate(4);
    	
        $validator = Validator::make(
            $input,
            array(
                'user_id' => 'exists:users,user_id',
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
}