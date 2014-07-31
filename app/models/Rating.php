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
        	if(!empty($input['rate'])) {

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
    public static function timeline($user_id) 
    {
        $error_code = ApiResponse::OK;
        $user_timeline = array();
        $user_timeline[] = $user_id;
        $user_follow = Follow::where('from_id', $user_id)->orderBy('updated_at', 'asc')->get();
        
        if(isset($user_follow)) {
            foreach($user_follow as $user) {
                $user_timeline[] = $user->to_id;
            }
        }
        if(Input::get('page')) {
            $getPage = Input::get('page');
            if(Input::get('per_page')) {
                $getLimit = Input::get('per_page');
            } else {
                $getLimit = 10;     
            }
            $paginate = Wine::paginate($getPage, $getLimit);
            if($paginate !== false) {
                $page = $paginate['page'];
                $limit = $paginate['limit'];
                
            } else {
                $error_code = ApiResponse::URL_NOT_EXIST;
                $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
                return array("code" => $error_code, "data" => $data);
            }

        } else {
            $page = 1;
            $limit = 15;
        }

        $ratings = Rating::whereIn('user_id', $user_timeline)->whereNotNull('wine_unique_id')->with('profile')->with('wine')->forPage($page, $limit)->get();

        if ($ratings) {
            foreach ($ratings as $rating) {   
                $winery = Winery::where('id', $rating->wine->winery_id)->first();
                $rating->winery= $winery;
                $country = Country::where('id', $rating->winery->country_id)->first();
                $winery->country_name = $country->country_name; 
                $like = Like::where('user_id',$user_id)->where('rating_id', $rating->id)->first();
                if($like) {
                    $rating->liked = true;
                } else {
                    $rating->liked = false;
                }
                $wishlist = Wishlist::where('user_id',$rating->user_id)->where('wine_unique_id',$rating->wine_unique_id)->first();
                if($wishlist) {
                    $rating->wishlist = true;
                } else {
                    $rating->wishlist = false;
                }
                if ($rating->wine->image_url != null) {
                    $rating->wine->image_url = URL::asset($rating->wine->image_url);
                }
                if ($rating->wine->wine_flag != null) {
                    $rating->wine->wine_flag = URL::asset($rating->wine->wine_flag);
                }
                if ($rating->profile->image != null) {
                    $rating->profile->image = URL::asset($rating->profile->image);   
                }
            }
            $data = $ratings;
        } else {
            $data = "Don't have any rating !";
        }
        return array("code" => $error_code, "data" => $data);
    }
}