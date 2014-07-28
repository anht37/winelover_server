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
            return false;
        } else {
        	if($input['rate']) {

	    		if(($input['rate']*10)%5==0) {
			    	return $input;
	    		} else {
                    
			    	return false;
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
            
            return false;
                
        }
        
    }
    public static function timeline($user_id) {
        $rating_userlogin = Rating::where('user_id', $user_id)->with('profile')->with('wine')->get();
        $timeline = array();
        if (isset($rating_userlogin)) {
            foreach ($rating_userlogin as $rating) {
                $winery = Winery::where('id', $rating->wine->winery_id)->first();
                $rating->wine->winery_id = $winery;
            }
            $timeline[] = $rating_userlogin;
                
        } else {
            $timeline['user_login'] = "Don't have any rating";
        }

        $user_follow = Follow::where('from_id', $user_id)->orderBy('updated_at', 'asc')->get();
        
        if(isset($user_follow)) {
            foreach($user_follow as $user) {
                $rating_user = Rating::where('user_id', $user->to_id)->with('profile')->with('wine')->get();
                if (isset($rating_user)) {
                    foreach ($rating_user as $rating) {
                        $winery = Winery::where('id', $rating->wine->winery_id)->first(); 
                        $rating->wine->winery_id = $winery; 
                    }
                    $timeline[] = $rating_user;
                } else {
                    $timeline[] = "Don't have any rating";
                }
            }
        } 
        $paginator = $timeline;
        $perPage = Input::get('per_Page', 15);   
        $page = Input::get('page', 1);
        if ($page > count($paginator) or $page < 1) { $page = 1; }
        $offset = ($page * $perPage) - $perPage;
        $articles = array_slice($paginator,$offset,$perPage);
        $data = Paginator::make($articles, count($paginator), $perPage);

        return  $data;
    }

}