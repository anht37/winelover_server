<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Profile extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'profiles';
    protected $primaryKey = 'id';

    public function rating()
    {
        return $this->hasMany('Rating','user_id', 'user_id');
    }
    
    public static function updateProfile($user_id, $input) 
    {
    	$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		if(User::where('user_id',$user_id)->first()){
			$profile = Profile::where('user_id', $user_id)->first();

			if($profile) {
		 		if (!empty($input)) {
		 			if (!empty($input['last_name'])) { 
				    	$profile->last_name = $input['last_name'];
			    	}
				    if (!empty($input['first_name'])) {  
				    	$profile->first_name = $input['first_name'];
				    }
				    if (!empty($input['bio'])) {  
				    	 $profile->bio = $input['bio'];
				    }	    
				    if (!empty($input['country_id'])) {
				        $profile->country_id = $input['country_id'];
				    }
				    if (!empty($input['pref_id'])) {
				        $profile->pref_id = $input['pref_id'];
				    }
				    if (!empty($input['alias'])) {
				        $profile->alias = $input['alias'];
				    }
				    if (!empty($input['website'])) {
				        $profile->website = $input['website'];
				    }

					$profile->save();
					$error_code = ApiResponse::OK;
					$data = $profile->toArray();	   
			    } else {
			    	$error_code = ApiResponse::MISSING_PARAMS;
			        $data = $input;
			    }
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
	    return array("code" => $error_code, "data" => $data);
    }

    public static function uploadImage($user_id) 
    {
    	$error_code = ApiResponse::OK;
    	if(User::where('user_id',$user_id)->first()){
				$profile = Profile::where('user_id', $user_id)->first();
	    	if (Input::hasFile('file')) {
			    $file = Input::file('file');
			    $destinationPath = public_path() . '/images/' . $user_id;
			    $filename = date('YmdHis').'_'.$file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension();

			    if (!File::isDirectory($destinationPath))
				{
				    File::makeDirectory($destinationPath, $mode = 0777, true, true);	
			    }
			    $upload_success     = $file->move($destinationPath, $filename);
			    $profile->image = 'images/'. $user_id . '/' . $filename;
			    $profile->save();
			    $data = URL::asset($profile->image);
			} else {
				$error_code = ApiResponse::MISSING_PARAMS;
			    $data = null;
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
		return array("code" => $error_code, "data" => $data);
    }
    
    public static function getProfileBasicUser($user_id)
    {
    	$error_code = ApiResponse::OK;
		$user_login = Session::get('user_id');

		if(User::where('user_id',$user_id)->first()){
		$users = Profile::orderBy('rate_count', 'desc')->get();
		$i = 0;
		if ($users) {
			foreach ($users as $key) {
				$i ++;
				if($key['user_id'] == $user_id) {
					break;
				}
			}
		}

		$profile = Profile::where('user_id', $user_id)->first();
		
			if ($profile->image != null) {
	            $profile->image = URL::asset($profile->image);   
	        }
	        if ($profile->country_id != null) {
	            $country = Country::where('id', $profile->country_id)->first();
	            $profile->country_name = $country->country_name;
	            $profile->country_flag = URL::asset($country->flag_url);   
	        }

	        $wishlists = Wishlist::where('user_id', $user_id)->get();
	        if($wishlists){
	            $profile->wishlist_count = count($wishlists);
	        } else {
	            $profile->wishlist_count = 0;
	        }
	        if ($user_id != $user_login) {
	            $follow = Follow::where('from_id', $user_login)->where('to_id', $user_id)->first();
		        if($follow) {
		            $profile->is_follow = true;
		        } else {
		            $profile->is_follow = false;
		        }
	        }
	        $profile->user_ranking = $i;
			$data = $profile->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
			$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
		return array("code" => $error_code, "data" => $data);
    }

    public static function getProfileWishlistUser($user_id)
    {
    	$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			$error_code = ApiResponse::URL_NOT_EXIST;
			$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
		} else {
			$page = $pagination['page'];
			$limit = $pagination['limit'];
			
			if(User::where('user_id',$user_id)->first()){
				$profile = Profile::where('user_id', $user_id)->first();
				
				if ($profile->image != null) {
		            $profile->image = URL::asset($profile->image);   
		        }
				$wishlists = Wishlist::where('user_id', $user_id)->with('wine')->forPage($page, $limit)->get();
				if (count($wishlists) == 0) {
						$data = array();
				} else {	
				    foreach ($wishlists as $wishlist) {
						$wishlist->winery = Winery::where('id', $wishlist->wine->winery_id)->first();

						if($wishlist->wine->image_url != null) {
		            		$wishlist->wine->image_url = URL::asset($wishlist->wine->image_url);
			            }

			            if($wishlist->wine->wine_flag != null) {
			            	$wishlist->wine->wine_flag = URL::asset($wishlist->wine->wine_flag);
			            } 
					}
					
					$data = $wishlists->toArray();
				}
			} else { 
				$error_code = ApiResponse::UNAVAILABLE_USER;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		}
		return array("code" => $error_code, "data" => $data);
    }

    public static function getProfieTopRate($user_id) 
    {
    	$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			$error_code = ApiResponse::URL_NOT_EXIST;
			$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
		} else {
			$page = $pagination['page'];
			$limit = $pagination['limit'];

			if(User::where('user_id',$user_id)->first()) {
				$top_rates = Rating::where('user_id',$user_id)->orderBy('rate', 'desc')->with('wine')->forPage($page, $limit)->get();
				foreach ($top_rates as $top_rate) {
					if(Winery::where('id',$top_rate->wine->winery_id)->first()) {
						$top_rate->winery = Winery::where('id',$top_rate->wine->winery_id)->first();
					}
					if($top_rate->wine->image_url != null) {
			            $top_rate->wine->image_url = URL::asset($top_rate->wine->image_url);
				    }

				    if($top_rate->wine->wine_flag != null) {
				        $top_rate->wine->wine_flag = URL::asset($top_rate->wine->wine_flag);
				    } 
				}
				$data = $top_rates->toArray();
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		}
		return array("code" => $error_code, "data" => $data);
    }

    public static function getProfieLastRate($user_id) 
    {
    	$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			$error_code = ApiResponse::URL_NOT_EXIST;
			$data = ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST);
		} else {
			$page = $pagination['page'];
			$limit = $pagination['limit'];

			if(User::where('user_id',$user_id)->first()) {
				$last_rates = Rating::where('user_id',$user_id)->orderBy('updated_at', 'desc')->with('wine')->forPage($page, $limit)->get();
				foreach ($last_rates as $last_rate) {
					$last_rate->winery = Winery::where('id',$last_rate->wine->winery_id)->first();
					if($last_rate->wine->image_url != null) {
			            $last_rate->wine->image_url = URL::asset($last_rate->wine->image_url);
				    }

				    if($last_rate->wine->wine_flag != null) {
				        $last_rate->wine->wine_flag = URL::asset($last_rate->wine->wine_flag);
				    } 
				}
				$data = $last_rates->toArray();
			} else {
				$error_code = ApiResponse::UNAVAILABLE_USER;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			}
		}
		return array("code" => $error_code, "data" => $data);
    }
    
}