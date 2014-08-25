<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Follow extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'follows';
    protected $primaryKey = 'id';

    public static function getLishFollow()
    {
    	$user_id = Session::get('user_id');
        $error_code = ApiResponse::OK;
		$follow = Follow::where('from_id', $user_id)->get();
		if($follow) {
			$data = $follow->toArray();
		} else {
			$data = array();
		} 
	    return array("code" => $error_code, "data" => $data);
    }

     public static function createNewFollow($input)
    {
    	$follow = new Follow;
		$error_code = ApiResponse::OK;
	    $follow->from_id = Session::get('user_id');
	    if(!empty($input['follow_id'])) {
	    	if ($input['follow_id'] == $follow->from_id) {
	    		$error_code = ApiResponse::SELF_FOLLOW_ERROR;
	        	$data = ApiResponse::getErrorContent(ApiResponse::SELF_FOLLOW_ERROR);
	    	} else {
		    	$follow->to_id = $input['follow_id'];
		    	$user = User::where('user_id', $follow->to_id)->first(); 
			 	if ($user) {

			 		if(Follow::where('from_id',$follow->from_id)->where('to_id', '=', $follow->to_id)->first()) {
			 			
			 			$error_code = ApiResponse::DUPLICATED_FOLLOW;
		        		$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_FOLLOW);
			 		} else {

			 			$following_profile = Profile::where('user_id', $follow->from_id)->first();
						if($following_profile != null) {
							$following_profile->following_count = $following_profile->following_count + 1;
							$following_profile->save();
						}
						$follower_profile = Profile::where('user_id', $follow->to_id)->first();
						if($follower_profile) {
							$follower_profile->follower_count = $follower_profile->follower_count + 1;
							$follower_profile->save();
						}
				 		$follow->save();
				        $data = $follow->toArray();
			 		}

			 	} else {
			 		$error_code = ApiResponse::UNAVAILABLE_USER;
		        	$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
			 	}
			}
	    } else {
	    	$error_code = ApiResponse::MISSING_PARAMS;
	        $data = $input;
	    }
	    return array("code" => $error_code, "data" => $data);
    }

     public static function deleteFollow($follow_id)
    {
    	$error_code = ApiResponse::OK;
		$from_id = Session::get('user_id');
		$follow = Follow::where('to_id', $follow_id)->where('from_id', '=' , $from_id)->first();
		if(User::where('user_id', $follow_id)->first()) {
		    if($follow) {
		    	$following_profile = Profile::where('user_id', $follow->from_id)->first();
				if($following_profile != null) {
					$following_profile->following_count = $following_profile->following_count + 1;
					$following_profile->save();
				}
				$follower_profile = Profile::where('user_id', $follow->to_id)->first();
				if($follower_profile) {
					$follower_profile->follower_count = $follower_profile->follower_count + 1;
					$follower_profile->save();
				}

	 			$follow->delete();
		 		$data = 'Follow deleted';
	 		} else {
	 			$error_code = ApiResponse::NOT_EXISTED_FOLLOW;
		        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_FOLLOW);
		    } 
		} else {
			$error_code = ApiResponse::UNAVAILABLE_USER;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_USER);
		}
	    return array("code" => $error_code, "data" => $data);
    }

}