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

    public function like() {
        return $this->hasMany('Like', 'id', 'rating_id');
    }

    public function comment() {
        return $this->hasMany('Comment', 'id', 'rating_id');
    }

    public function wishlist() {
        return $this->hasMany('Wishlist', 'wine_unique_id', 'wine_unique_id');
    }

    public static function check_validator($input)
    {	   	

        $validator = Validator::make(
            $input,
            array(
                'wine_unique_id' => 'exists:wines,wine_unique_id,deleted_at,NULL',
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
    public static function timeline() 
    {
        $user_id = Session::get('user_id');
        $error_code = ApiResponse::OK;
        $user_timeline = array();
        $user_timeline[] = $user_id;
        $user_follow = Follow::where('from_id', $user_id)->orderBy('updated_at', 'asc')->get();
        
        if(isset($user_follow)) {
            foreach($user_follow as $user) {
                $user_timeline[] = $user->to_id;
            }
        }
        $pagination = ApiResponse::pagination();
        if($pagination == false) {
            $error_code = ApiResponse::URL_NOT_EXIST;
            $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
        } else {
            $page = $pagination['page'];
            $limit = $pagination['limit'];

            $ratings = Rating::whereIn('user_id', $user_timeline)->whereNotNull('wine_unique_id')->with('profile')->with('wine')->orderBy('updated_at', 'desc')->forPage($page, $limit)->get();
            if (count($ratings) == 0) {
                    $data = array();
                
            } else {
                foreach ($ratings as $rating) {   
                    $winery = Winery::where('id', $rating->wine->winery_id)->first();
                    $rating->winery = $winery;
                    $country = Country::where('id', $rating->winery->country_id)->first();
                    if($country) {
                        $rating->winery->country_name = $country->country_name; 
                    } else {
                        $rating->winery->country_name = null;
                    }
                    $like = Like::where('user_id',$user_id)->where('rating_id', $rating->id)->first();
                    if($like) {
                        $rating->liked = true;
                    } else {
                        $rating->liked = false;
                    }
                    $wishlist = Wishlist::where('user_id',$user_id)->where('wine_unique_id',$rating->wine_unique_id)->first();
                    if($wishlist) {
                        $rating->wishlist = true;
                    } else {
                        $rating->wishlist = false;
                    }
                    $rating->wine->image_url = Wine::getImageWineFromServer($user_id, $rating->wine->wine_unique_id, $rating->wine->image_url);
                    if ($rating->wine->wine_flag != null) {
                        $rating->wine->wine_flag = URL::asset($rating->wine->wine_flag);
                    }
                    if ($rating->profile->image != null) {
                        $rating->profile->image = URL::asset($rating->profile->image);   
                    }
                }
                $data = $ratings->toArray();
            }
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function getListRatingMyWine()
    {
        $user_id = Session::get('user_id');
        $pagination = ApiResponse::pagination();
        if($pagination == false) {
            $error_code = ApiResponse::URL_NOT_EXIST;
            $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
        } else {
            $page = $pagination['page'];
            $limit = $pagination['limit'];
            $ratings = Rating::where('user_id', $user_id)->where('is_my_wine', 1)->with('wine')->orderBy('updated_at', 'desc')->forPage($page, $limit)->get();
            $error_code = ApiResponse::OK;
            if(count($ratings) == 0 ) {
                    $data = array();
            } else {
                foreach ($ratings as $rating) {
                    $rating->winery = Winery::where('id',$rating->wine->winery_id)->first();

                    $rating->wine->image_url = Wine::getImageWineFromServer($user_id, $rating->wine->wine_unique_id, $rating->wine->image_url);

                    if($rating->wine->wine_flag != null) {
                        $rating->wine->wine_flag = URL::asset($rating->wine->wine_flag);
                    } 
                }
                $data = $ratings->toArray();
            }
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function createNewRating($input)
    {

        $rating = new Rating;
        $error_code = ApiResponse::OK;
        $rating->user_id = Session::get('user_id');
        if(!empty($input['wine_unique_id'])) {
            if(Wine::where('wine_unique_id', $input['wine_unique_id'])->first()) {
                $rating_old = Rating::where('wine_unique_id', $input['wine_unique_id'])->where('user_id',$rating->user_id)->first();
                if($rating_old) {
                    $result = Rating::updateRatingDetail($rating_old->id, $input);
                    return $result;
                } else {
                    $rating->wine_unique_id = $input['wine_unique_id'];
                
                    if (!empty($input['rate'])) {  
                        $rating->rate = $input['rate'];
                    }
                    if (!empty($input['content'])) {  
                         $rating->content = $input['content'];
                    }       
                    if (!empty($input['like_count'])) {
                        $rating->like_count = $input['like_count'];
                    }
                    if (!empty($input['comment_count'])) {
                        $rating->comment_count = $input['comment_count'];
                    }
                    if (!empty($input['is_my_wine'])) {
                        $rating->is_my_wine = $input['is_my_wine'];
                    }
                    
                    // Validation and Filtering is sorely needed!!
                    // Seriously, I'm a bad person for leaving that out.
                    $check = Rating::check_validator($input);
                    if($check !== false) {

                        $rating_profile = Profile::where('user_id',$rating->user_id)->first();
                        if($rating_profile != null) {
                            $rating_profile->rate_count = $rating_profile->rate_count + 1;
                            $rating_profile->save(); 
                        }
                        $rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
                        if($rating_wine != null) {
                            $rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
                            $rating_wine->rate_count = $rating_wine->rate_count + 1;
                            $rating_wine->average_rate = ($rating_rate + $rating->rate)/ $rating_wine->rate_count;
                            $rating_wine->save(); 
                        }

                        $rating->save();
                        
                        $data = $rating->toArray();     

                    } else {
                        
                        $error_code = ApiResponse::UNAVAILABLE_RATING;
                        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);

                    }
                }
            } else {
                $error_code = ApiResponse::UNAVAILABLE_WINE;
                $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
            }
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }

        return array("code" => $error_code, "data" => $data);
    }

    public static function updateRatingDetail($id, $input)
    {
        $rating = Rating::where('id', $id)->first();
        $error_code = ApiResponse::OK;
        if($rating) {
            $rating_rate_old = $rating->rate;
            if(!empty($input)) {
                if (!empty($input['rate'])) {  
                    $rating->rate = $input['rate'];
                }
                if (!empty($input['content'])) {  
                     $rating->content = $input['content'];
                }       
                if (!empty($input['like_count'])) {
                    $rating->like_count = $input['like_count'];
                }
                if (!empty($input['comment_count'])) {
                    $rating->comment_count = $input['comment_count'];
                }
                if (!empty($input['is_my_wine'])) {
                    $rating->is_my_wine = $input['is_my_wine'];
                }
                
                $check = Rating::check_validator($input);
                if($check !== false) {
                    if($rating->rate > 0) {
                        $rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
                        if($rating_wine) {
                            $rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
                            if($rating_rate == 0 && $rating_wine->rate_count !== 0) {
                                $rating_wine->average_rate = ($rating_rate + $rating->rate)/ $rating_wine->rate_count;
                            } elseif ($rating_rate !== 0 && $rating_wine->rate_count !== 0) {
                                $rating_wine->average_rate = ($rating_rate - $rating_rate_old + $rating->rate)/ $rating_wine->rate_count;
                            } else {
                                $error_code = ApiResponse::UNAVAILABLE_RATING;
                                $data = null;
                                return array("code" => $error_code, "data" => $data);
                            }
                            $rating_wine->save(); 
                        }
                    } 
                    
                    $rating->save();
                    
                    $data = $rating->toArray();    

                } else {
                    
                    $error_code = ApiResponse::UNAVAILABLE_RATING;
                    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING); 
                
                }
            } else {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = $input;
            }
        } else {
            $error_code = ApiResponse::UNAVAILABLE_RATING;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function showRatingDetail($id)
    {
        $rating = Rating::where('id', $id)->with('wine')->first();
        $error_code = ApiResponse::OK;
        if($rating) {
            
            $data = $rating->toArray();

        } else {
            $error_code = ApiResponse::UNAVAILABLE_RATING;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function deleteRating($id)
    {
        $rating = Rating::where('id', $id)->first();
        $error_code = ApiResponse::OK;
        if($rating) {
            $rating_profile = Profile::where('user_id',$rating->user_id)->first();
            if($rating_profile != null) {
                $rating_profile->rate_count = $rating_profile->rate_count - 1;
                $rating_profile->save(); 
            }
            $rating_wine = Wine::where('wine_unique_id',$rating->wine_unique_id)->first();
            if($rating_wine != null) {
                $rating_rate = $rating_wine->average_rate * $rating_wine->rate_count;
                $rating_wine->rate_count = $rating_wine->rate_count - 1;
                if($rating_wine->rate_count > 0) {
                    $rating_wine->average_rate = ($rating_rate - $rating->rate)/ $rating_wine->rate_count;
                } else {
                    $rating_wine->average_rate = 0;
                }
                $rating_wine->save(); 
            }

            $rating->delete();
            
            $data = 'Rating Deleted';
        } else {
            $error_code = ApiResponse::UNAVAILABLE_RATING;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
        } 
        return array("code" => $error_code, "data" => $data);
    }
    
    public static function removeWineFromMyWine($id)
    {
        $rating = Rating::where('id', $id)->first();
        $error_code = ApiResponse::OK;
        if($rating) {
            $rating->is_my_wine = 0;
            $rating->save();
            
            $data = 'Rating is removed from my wine';
        } else {
            $error_code = ApiResponse::UNAVAILABLE_RATING;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
        } 
            return array("code" => $error_code, "data" => $data);
    }

    public static function getTotalRateOfNumberRate($user_id)
    {
        $error_code = ApiResponse::OK;
        $data = array();
        $rate = array();
        $number_rate = 5;
        for($i = 0; $i < 11; $i++) {
            $ratings = Rating::where('user_id', $user_id)->where('rate', $number_rate)->get();
            $rate['rate'] = $number_rate;
            $rate['total_rate'] = count($ratings);
            $data[] = $rate;
            $number_rate = $number_rate - 0.5;
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function orderBy($data, $field)
    {
        $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
        usort($data, create_function('$b,$a', $code));
        return $data;
    }

    public static function getProfile($user_id, $title, $id, $rating_id, $updated_at)
    {
        $profile = Profile::where('user_id', $user_id)->first();
        $data = array(
            'first_name' => $profile->first_name,
            'last_name' => $profile->last_name,
            'avatar' => URL::asset($profile->image),
            'title' => $title,
            'id' => $id,
            'rating_id' =>$rating_id,
            'updated_at' => $updated_at,
        );
        return $data;
    }

    public static function getActivity()
    {
        $user_id = Session::get('user_id');
        $error_code = ApiResponse::OK;
        $act = array();

        $ratings = Rating::where('user_id', $user_id)->get();
        foreach ($ratings as $rating) {
            $likes = Like::where('rating_id', $rating->id)->whereNotIn('user_id', [$user_id])->get();
            if($likes) {
                $title = 'like';
                foreach ($likes as $like) {
                    $act[] = Rating::getProfile($like->user_id, $title, $like->id, $rating->id, $like->updated_at);
                }
            }

            $comments = Comment::where('rating_id', $rating->id)->whereNotIn('user_id', [$user_id])->get();
            if($comments) {
                $title = 'comment';
                foreach ($comments as $comment) {
                    $act[] = Rating::getProfile($comment->user_id, $title, $comment->id, $rating->id, $comment->updated_at);
                    
                }
            }

            $wishlists = Wishlist::where('wine_unique_id', $rating->wine_unique_id)->whereNotIn('user_id', [$user_id])->get();
            if($wishlists) {
                $title = 'wishlist';
                foreach ($wishlists as $wishlist) {
                    $act[] = Rating::getProfile($wishlist->user_id, $title, $wishlist->id, $rating->id, $wishlist->updated_at);
                    
                }
            }
        }
        $user_follow = Follow::where('to_id', $user_id)->get();
        if($user_follow) {
            $title = 'follow';
            foreach ($user_follow as $user) {
                $act[] = Rating::getProfile($user->from_id, $title, $user->id, null, $user->updated_at);
                
            }
        }
        $data = Rating::orderBy($act, 'updated_at');
        
        return array("code" => $error_code, "data" => $data);

    }

    // public static function getListRateOfNumberRate($number_rate)
    // {
    //     $error_code = ApiResponse::OK;
    //     $user_id = Session::get('user_id');
    //     $check_rate = Rating::check_validator($input);
    //     if()
    //         $ratings = Rating::where('user_id', $user_id)->where('rate', $number_rate)->get();
    //         $data = $ratings->toArray();
    //     return array("code" => $error_code, "data" => $data);
    // }
}
