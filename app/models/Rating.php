<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Rating extends Eloquent {
    
    use SoftDeletingTrait;
    protected $table = 'ratings';
    protected $primaryKey = 'id';

    public function wine()
    {
        return $this->belongsTo('Wine','wine_unique_id', 'wine_unique_id');
    }
    public function profile()
    {
        return $this->belongsTo('Profile','user_id', 'user_id');
    }

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
                    //TODO : Fix all "false" string to false value
			    	return "FALSE";
	    		}
	    	} else {
			    return $input;
		    }
        	
        }
 	}

    public static function check_rating($rating_id)
    {   
        $rating = Rating::where('id', $rating_id)->first();
        
        if ($rating) {
            return $rating_id;
        } else {
            //TODO : Fix all "false" string to false value
            return "FALSE";
                
        }
        
    }
    public static function timeline($user_id) {
        $error_code = ApiResponse::OK;
        $rating_user = Rating::where('user_id', $user_id)->with('profile')->with('wine')->get();
        if (isset($rating_user)) {
            $user_rating = $rating_user;
        } else {
            $user_rating = "Don't have any rating";
        }

        $user_follow = Follow::where('from_id', $user_id)->orderBy('updated_at', 'asc')->get();
        
        if(isset($user_follow)) {
            $timeline = array();
            foreach($user_follow as $user) {
                $profiles = Profile::where('user_id', $user->to_id)->with('rating')->first();
                if($profiles){
                    foreach ($profiles->rating as $rating) {

                        $rating_wine = Wine::where('wine_unique_id', $rating->wine_unique_id)->first();
                        $rating->wine_unique_id = $rating_wine;
                    }
                    $timeline[] = $profiles;
                }
            } 
        } else {
            $timeline = "Don't have any user is followed";
        }
        
        $data = array('user_timeline' => $user_rating, 'user_follow_rating' => $timeline);
        
        // $paginator = $timeline;
     //    $perPage = Input::get('per_Page', 15);   
     //    $page = Input::get('page', 1);
     //    if ($page > count($paginator) or $page < 1) { $page = 1; }
     //    $offset = ($page * $perPage) - $perPage;
     //    $articles = array_slice($paginator,$offset,$perPage);
     //    $datas = Paginator::make($articles, count($paginator), $perPage);

        return array("code" => $error_code, "data" => $data);
    }

}