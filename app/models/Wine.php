<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait; 
class Wine extends Eloquent {
    
    use SoftDeletingTrait;
    protected $table = 'wines';
    protected $primaryKey = 'wine_id';
    protected $guarded = array();

    public static $rules = array(
		'year' => 'required',
	);	 

	public static function scan($file_path) {
        $connection = new TcpConnetion();
        $result = $connection->sendRequest($file_path,"PRED");
        if($result == -2) {
            return $result;
        }
        $wine = Wine::where("rakuten_id", $result)->first();
        if($wine) {
           return $wine->wine_id;
        }else {
           return -2;
        }
    }
    public function winery()
	{
    	return $this->belongsTo('Winery','winery_id');
	}

    public static function getListWine()
    {
        $error_code = ApiResponse::OK;
        $pagination = ApiResponse::pagination();
        if($pagination == false) {
            return array("code" => ApiResponse::URL_NOT_EXIST, "data" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST));
        }
        $page = $pagination['page'];
        $limit = $pagination['limit'];
        $wine = Wine::with('winery')->forPage($page, $limit)->get();
        if(count($wine) == 0) {
                $data = array();
        } else {
            foreach ($wine as $wines) {
                $wines->winery_id = $wines->winery->brand_name;
                if($wines->image_url != null) {
                    $wines->image_url = URL::asset($wines->image_url);
                }   
                if($wines->wine_flag != null) {
                    $wines->wine_flag = URL::asset($wines->wine_flag);
                } 
            }
            $data = $wine->toArray();
        }

        return array("code" => $error_code, "data" => $data);
    }

    public static function createNewWine($input)
    {
        $wine = new Wine;
        $error_code = ApiResponse::OK;
        
        if(!empty($input['name']) && !empty($input['year']) && !empty($input['winery_id'])) {
            $wine->name = $input['name'];
            $wine->year = $input['year'];
            $wine->winery_id = $input['winery_id'];
            if (!empty($input['image_url'])) {
                $wine->image_url = $input['image_url'];
            }
            if (!empty($input['average_price'])) {
                $wine->average_price = $input['average_price'];
            }
            if ( !empty($input['average_rate']) ) {
                $wine->average_rate = $input['average_rate'];
            }
            if (!empty($input['wine_type']) ) {
                $wine->wine_type = $input['wine_type'];
            }
            
            // Validation and Filtering is sorely needed!!
            // Seriously, I'm a bad person for leaving that out.



            if(Winery::where('id',$wine->winery_id)->first()) {
                $wine->save();
                
                $wine->wine_unique_id = $wine->wine_id . '_' . $wine->year;
                $wine->save();

                $data = $wine;
            } else {
                $error_code = ApiResponse::UNAVAILABLE_WINERY;
                $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
            }
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }
        
        return array("code" => $error_code, "data" => $data);
    }

    public static function getWineDetail($wine_id)
    {
        $user_id = Session::get('user_id');
        $wine = Wine::where('wine_id', $wine_id)->with('winery')->first();
        $error_code = ApiResponse::OK;
        if($wine) {
            $country = Country::where('id',$wine->winery->country_id)->first();
            if($country) {
                $wine->winery->country_id = $country->country_name;
            } else {
                $wine->winery->country_id = null;
            }
            $wine_note = Winenote::where('wine_unique_id', $wine->wine_unique_id)->where('user_id',$user_id)->first();
            if($wine_note) {
                $wine->winenote = $wine_note->note;
            } else {
                $wine->winenote = null;
            }
            
            
            $wishlist = Wishlist::where('user_id', $user_id)->where('wine_unique_id', $wine->wine_unique_id)->first();
            if($wishlist) {
                $wine->is_wishlist = true;
            } else {
                $wine->is_wishlist = false;
            }

            $all_wines_winery = Wine::where('winery_id', $wine->winery_id)->whereNotIn('wine_id', [$wine_id])->get();
            $wine->winery->count_wine = count($all_wines_winery) + 1 ;
            $rate_winery = $wine->rate_count;
            if(count($all_wines_winery) !== 0) {
                
                $sum_rate_winery = $wine->average_rate;
                foreach ($all_wines_winery as $wine_winery) {
                    $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                    
                    $rate_count = $wine_on_winery->rate_count;
                    $rate_winery = $rate_winery + $rate_count;
                    
                    $average_rate = $wine_on_winery->average_rate;
                    $sum_rate_winery = $sum_rate_winery + $average_rate;

                }

                $wine->winery->total_rate = $rate_winery;
                $wine->winery->average_rate_winery = $sum_rate_winery/count($all_wines_winery);
            } else {
                $wine->winery->total_rate = $rate_winery;
                $wine->winery->average_rate_winery = $wine->average_rate;
            }
            $wine->total_like = 0;

            $rating_user = Rating::where('wine_unique_id', $wine->wine_unique_id)->where('user_id',$user_id)->with('profile')->first();
            if(count($rating_user) == 0) {
                $rating_user = null;
            } else {
                if ($rating_user->profile->image != null) {
                    $rating_user->profile->image = URL::asset($rating_user->profile->image);   
                }
                $wine->total_like = $wine->total_like + $rating_user->like_count;
            }
            $rating = Rating::where('wine_unique_id', $wine->wine_unique_id)->whereNotIn('user_id',[$user_id])->with('profile')->get();
            if(count($rating) == 0) {
                $rating = array();
            } else {
                foreach ($rating as $ratings) {
                    if ($ratings->profile->image != null) {
                        $ratings->profile->image = URL::asset($ratings->profile->image);   
                    }
                    $follow = Follow::where('from_id', $user_id)->where('to_id', $ratings->user_id)->first();
                    if($follow) {
                        $ratings->is_follow = true;
                    } else {
                        $ratings->is_follow = false;
                    }
                    $wine->total_like = $wine->total_like + $ratings->like_count;
                }
            }
            if($wine->image_url != null) {
                $wine->image_url = URL::asset($wine->image_url);
            }   
            if($wine->wine_flag != null) {
                $wine->wine_flag = URL::asset($wine->wine_flag);
            } 

            $data = array('wine' => $wine,'rate_user' => $rating_user ,'rate' => $rating ,'wine_related' => $all_wines_winery);
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function updateWineDetail($wine_id, $input)
    {
        $error_code = ApiResponse::OK;
        $wine = Wine::where('wine_id', $wine_id)->first();
        if($wine) {
            if(!empty($input)) {
                if ( !empty($input['name']) ) {
                $wine->name = $input['name'];
                }
                if ( !empty($input['year']) ) {
                    $wine->year = $input['year'];
                }
                if ( !empty($input['winery_id']) ) {
                    $wine->winery_id = $input['winery_id'];
                }
                if ( !empty($input['image_url']) ) {
                    $wine->image_url = $input['image_url'];
                }
                if (!empty($input['average_price'])) {
                    $wine->average_price = $input['average_price'];
                }
                if ( !empty($input['average_rate']) ) {
                    $wine->average_rate = $input['average_rate'];
                }
                if ( !empty($input['wine_type']) ) {
                    $wine->wine_type = $input['wine_type'];
                }
                $wine->wine_unique_id = $wine->wine_id . '_' . $wine->year;

                if(Winery::where('id',$wine->winery_id)->first()) {
                    $wine->save();
                    $data = $wine;
                } else {
                    $error_code = ApiResponse::UNAVAILABLE_WINERY;
                    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
                }
            } else {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = $input;
            }
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
        }
        
        return array("code" => $error_code, "data" => $data);
    }
    
    public static function deleteWine($wine_id)
    {
        $wine = Wine::where('wine_id', $wine_id)->first();
        $error_code = ApiResponse::OK;
        if($wine) {
            $wine->delete();
            
            $data = 'Wine deleted';
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
        } 
        return array("code" => $error_code, "data" => $data);
    }

    public static function searchWinefromMywine($input)
    {   
        $error_code = ApiResponse::OK;
        $data = array();
        $user_id = Session::get('user_id');
        if(!empty($input['text'])) {
            $text = $input['text'];
            $wine = Wine::where('name','LIKE','%'.$text.'%')->with('winery')->get();
            if($wine) {
                foreach ($wine as $wines) {
                    if($wines->image_url != null) {
                        $wines->image_url = URL::asset($wines->image_url);
                    }

                    if($wines->wine_flag != null) {
                        $wines->wine_flag = URL::asset($wines->wine_flag);
                    } 

                    $ratings = Rating::where('user_id', $user_id)->where('wine_unique_id',$wines->wine_unique_id)->where('is_my_wine', 1)->first();
                    if($ratings) {
                        $data[] = $wines;
                    } else {
                        $wishlist = Wishlist::where('user_id', $user_id)->where('wine_unique_id',$wines->wine_unique_id)->first();
                        if ($wishlist) {
                            $data[] = $wines;
                        }
                    }
                }
            }
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }
        
        return array("code" => $error_code, "data" => $data);
    }

    
}
