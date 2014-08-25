<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Like extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'likes';
    protected $primaryKey = 'id';

    public static function getLishLike()
    {
    	$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$like = Like::where('user_id', $user_id)->get();
		if (count($like) > 0) {
			$data = $like->toArray();
		} else {	
			$data = array();
		}
		
	    return array("code" => $error_code, "data" => $data);
    }

    public static function createNewLike($input)
    {
    	$like = new Like;
		    $error_code = ApiResponse::OK;
		    $like->user_id = Session::get('user_id');

			if(!empty($input['rating_id'])) {
	    		$like->rating_id = $input['rating_id'];

			    $check_rating = Rating::check_rating($like->rating_id);
			    if ($check_rating !== false) {

			    	if (Like::where('rating_id', $like->rating_id)->where('user_id', '=', $like->user_id)->first()) {
						$error_code = ApiResponse::DUPLICATED_LIKE;
					 	$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_LIKE);
					} else {
						//update like_count on rating
						$like_rating = Rating::where('id', $like->rating_id)->first();
						if($like_rating != null) {

							$like_rating->like_count = $like_rating->like_count + 1;
							$like_rating->save();
						}						
						$like->save();
						
						$data = $like->toArray();
					}		   

				} else {
			    	 
			    	$error_code = ApiResponse::UNAVAILABLE_RATING;
			        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			
				}
			}
	    return array("code" => $error_code, "data" => $data);
    }

    public static function deleteLike($rating_id)
    {
    	$error_code = ApiResponse::OK;
		$user_id = Session::get('user_id');
		if(Rating::where('id',$rating_id)->first()) {
			$like = Like::where('rating_id', $rating_id)->where('user_id',$user_id)->first();
		    if($like) {

		    	//update like_count on rating
	 			$like_rating = Rating::where('id', $like->rating_id)->first();
				if($like_rating != null) {
					$like_rating->like_count = $like_rating->like_count - 1;
					$like_rating->save();
				}
	 			$like->delete();
		 		$data = 'Like deleted';
	 		} else {
	 			$error_code = ApiResponse::NOT_EXISTED_LIKE;
		        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_LIKE);
		    } 
		} else {
		 	$error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
	    return array("code" => $error_code, "data" => $data);
    }
}