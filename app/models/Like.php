<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Like extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'likes';
    protected $primaryKey = 'id';
 

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