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
             //$connection = new TcpConnetion();
        //$result =  $connection->sendRequest($file_path,"PRED");
        //if($result === -2) {
        //    return $result;
        //}
        //$wine = Wine::where("rakuten_id", $result)->first();
        //if($wine) {
        //   return $wine->wine_id;
        //}else {
        //   return -2;
        // }
        $wine_id = rand(0,10000);
        if($wine_id == 0){
            $wine_id = -2;
        }
        return $wine_id;
    }
    public function winery()
	{
    	return $this->belongsTo('Winery','winery_id');
	}

    public static function getWineType($input)
    {
        if($input == 1) {
            $wine_type = '赤ワイン';
        } elseif($input == 2) {
            $wine_type = '白ワイン';
        } elseif($input == 3) {
            $wine_type = 'ロゼワイン';
        } elseif($input == 4) {
            $wine_type = '発泡系・シャンパン';
        } elseif($input == 5) {
            $wine_type = 'ワインセット';
        } else {
            $wine_type = 'その他';
        }
        return $wine_type;
    }

    public static function getListWine()
    {
        $user_id = Session::get('user_id');
        $error_code = ApiResponse::OK;
        $pagination = ApiResponse::pagination();
        if($pagination == false) {
            $error_code = ApiResponse::URL_NOT_EXIST;
            $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
        } else {
            $page = $pagination['page'];
            $limit = $pagination['limit'];
            $wines = Wine::with('winery')->forPage($page, $limit)->get();
            if(count($wines) == 0) {
                    $data = array();
            } else {
                foreach ($wines as $wine) {
                    $wine->winery_id = $wine->winery->brand_name;
                    $wine->image_url = Wine::getImageWineFromServer($user_id, $wine->wine_unique_id, $wine->image_url);  
                    if($wine->wine_flag != null) {
                        $wine->wine_flag = URL::asset($wine->wine_flag);
                    } 
                }
                $data = $wines->toArray();
            }
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
            if($wine->wine_type != null) {
                $wine->wine_type = Wine::getWineType($wine->wine_type);
            }
            
            $wine->image_url = Wine::getImageWineFromServer($user_id, $wine->wine_unique_id, $wine->image_url);

            if($wine->wine_flag != null) {
                $wine->wine_flag = URL::asset($wine->wine_flag);
            } 
            

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

            $all_wines_winery = Wine::where('winery_id', $wine->winery_id)
                                    ->whereNotIn('wine_id', [$wine_id])
                                    ->where('year','>',0)
                                    ->where('average_rate','>',0)
                                    ->orderBy('year', 'desc')
                                    ->take(10)->get();
            $wine->winery->count_wine = count($all_wines_winery) + 1 ;
            $rate_winery = $wine->rate_count;
            if(count($all_wines_winery) !== 0) {
                
                $sum_rate_winery = $wine->average_rate;
                foreach ($all_wines_winery as $wine_winery) {
                    $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                    $wine_on_winery->image_url = Wine::getImageWineFromServer($user_id, $wine_on_winery->wine_unique_id, $wine_on_winery->image_url);
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
            $ratings = Rating::where('wine_unique_id', $wine->wine_unique_id)->whereNotIn('user_id',[$user_id])->with('profile')->get();
            if(count($ratings) == 0) {
                $ratings = array();
            } else {
                foreach ($ratings as $rating) {
                    if ($rating->profile->image != null) {
                        $rating->profile->image = URL::asset($rating->profile->image);   
                    }
                    $follow = Follow::where('from_id', $user_id)->where('to_id', $rating->user_id)->first();
                    if($follow) {
                        $rating->is_follow = true;
                    } else {
                        $rating->is_follow = false;
                    }
                    $wine->total_like = $wine->total_like + $rating->like_count;
                }
            }
            $data = array('wine' => $wine,'rate_user' => $rating_user ,'rate' => $ratings ,'wine_related' => $all_wines_winery);
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
        $wine_unique_id = array();
        $data = array();
        $user_id = Session::get('user_id');
        if(!empty($input['text'])) {
            $text = $input['text'];
            $ratings = Rating::where('user_id', $user_id)->where('is_my_wine', 1)->get();
            if($ratings) {
                foreach ($ratings as $rating) {
                    $wine_unique_id[] = $rating->wine_unique_id;
                }
                if($wine_unique_id != null) {
                    $wishlists = Wishlist::where('user_id', $user_id)->whereNotIn('wine_unique_id', $wine_unique_id)->get();
                } else {
                    $wishlists = Wishlist::where('user_id', $user_id)->get();
                }                     
                if ($wishlists) {
                    foreach ($wishlists as $wishlist) {
                        $wine_unique_id[] = $wishlist->wine_unique_id;
                    }  
                }
            }
            if($wine_unique_id != null) {
                $wines = Wine::where('name','LIKE','%'.$text.'%')->whereIn('wine_unique_id', $wine_unique_id)->with('winery')->get();
                if($wines) {
                    foreach ($wines as $wine) {
                        if($wine->image_url != null) {
                            $wine->image_url = URL::asset($wine->image_url);
                        }

                        if($wine->wine_flag != null) {
                            $wine->wine_flag = URL::asset($wine->wine_flag);
                        }
                        $data[] = $wine;    
                    }
                }
            }
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }
        
        return array("code" => $error_code, "data" => $data);
    }

    public static function getWineRelated($input)
    {
        $error_code = ApiResponse::OK;
        $pagination = ApiResponse::pagination();
        if($pagination == false) {
            $error_code = ApiResponse::URL_NOT_EXIST;
            $data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
        } else {
            $page = $pagination['page'];
            $limit = $pagination['limit'];
            if(!empty($input['wine_id'])) {
                $wine_id = $input['wine_id'];
                $wine = Wine::where('wine_id',$wine_id)->first();
                if($wine) {
                    $wine_related = Wine::where('winery_id',$wine->winery_id)->whereNotIn('wine_id', [$wine_id])->forPage($page, $limit)->get();
                    $data = $wine_related->toArray();
                } else {
                    $error_code = ApiResponse::UNAVAILABLE_WINE;
                    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
                }
            } else {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = $input;
            }
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function getListWineFromRakutenId($input)
    {
        $error_code = ApiResponse::OK;
        $data = array();
        if(!empty($input)) {
            $i = 0;
            foreach ($input as $wine_app) {
                $wine_app_id = explode( '_', $wine_app);
                //print_r($wine_app_id);
                if(count($wine_app_id) > 2 ) {
                    if($wine_app_id[0] == 'app1') {
                        $rakuten_id = 'rakuten_' . $wine_app_id[1] . '_' . $wine_app_id[2];
                    } else {
                        $rakuten_id = 'rakuten_' . $wine_app_id[0] . '_' . $wine_app_id[1];
                    }
                    
                    $wine = Wine::where('rakuten_id', $rakuten_id)->get(array('rakuten_id', 'name', 'image_url', 'wine_unique_id'))->first();
                    if($wine) {
                        if($wine->image_url != null) {
                            $wine->image_url = URL::asset($wine->image_url);
                        }
                        $data[] = $wine->toArray();
                    } 
                    $i ++;
                }
            }
            
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }
   
        return array("code" => $error_code, "data" => $data);
    }

    public static function uploadImageWineScan($wine_unique_id)
    {
        $error_code = ApiResponse::OK;
        $user_id = Session::get('user_id');
        $wine = Wine::where('wine_unique_id', $wine_unique_id)->first();
        if($wine) {
            if (Input::hasFile('file')) {

                $file = Input::file('file');
                $destinationPath = public_path() . '/images/' . $user_id . '/wine/' . $wine->wine_unique_id;
                $filename = date('YmdHis').'_'.$file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                if (!File::isDirectory($destinationPath))
                {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);    
                } else {
                    File::cleanDirectory($destinationPath);
                }
                $upload_success     = $file->move($destinationPath, $filename);
                
                $data = URL::asset('images/'. $user_id . '/wine/' . $wine_unique_id . '/' . $filename);
            } else {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = null;
            }
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINE;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function getImageWineFromServer($user_id, $wine_unique_id, $image_url)
    {
        $destinationPath = public_path() . '/images/' . $user_id . '/wine/' . $wine_unique_id;
        if (File::isDirectory($destinationPath))
        {
            $file = File::files($destinationPath);

            $image_paths = explode('/', $file[0]);
            $image_name = $image_paths[count($image_paths) -1];
            $image_url = URL::asset('/images/' . $user_id . '/wine/' . $wine_unique_id . '/' . $image_name);
        } else {
            if($image_url != null) {
                $image_url = URL::asset($image_url);
            } 
        }
        
        return $image_url;
    }
}
