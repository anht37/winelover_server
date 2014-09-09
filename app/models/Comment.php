<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait; 
class Comment extends Eloquent {
	use SoftDeletingTrait;
 
    protected $table = 'comments';
    protected $primaryKey = 'id';
 	
 	public static function getListComment($rating_id)
 	{
 		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$check_rating = Rating::check_rating($rating_id);
		if ($check_rating !== false) {

		 	$comments = Comment::where('rating_id', $rating_id)->get();
		 	if(count($comments) > 0){
		 		foreach ($comments as $comment) {
		 			$profile = Profile::where('user_id', $comment->user_id)->first();
		 			if($profile->image != null) {
		 				$comment->avatar_user = URL::asset($profile->image);
		 			} else {
		 				$comment->avatar_user = $profile->image;
		 			}
		 			$comment->first_name = $profile->first_name;
		 			$comment->last_name = $profile->last_name;
		 		}
		 		$data = $comments->toArray();
		 	} else {
		        $data = array();
		 	}

		} else {
		    $error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
	    return array("code" => $error_code, "data" => $data);
 	}

 	public static function createNewComment($rating_id, $input)
 	{
 		$comment = new Comment;
	    $error_code = ApiResponse::OK;
	    $comment->user_id = Session::get('user_id');

	    if(!empty($input['content'])) {
	    	$comment->content = $input['content'];
	    	$comment->rating_id = $rating_id;
		    $check_rating = Rating::check_rating($comment->rating_id);
		    if ($check_rating !== false) {
		    	//update comment_count on rating
				$comment_rating = Rating::where('id', $comment->rating_id)->first();
				if($comment_rating != null) {
					$comment_rating->comment_count = $comment_rating->comment_count + 1;
					$comment_rating->save();
	 				$comment->delete();
	 			}

				$comment_profile = Profile::where('user_id', $comment->user_id)->first();
            
	            if($comment_profile != null) {
	                $comment_profile->comment_count = $comment_profile->comment_count + 1;
	                $comment_profile->save();
	            }

		    	$comment->save();
		    	$profile = Profile::where('user_id', $comment->user_id)->first();
		 		if($profile->image != null) {
		 			$comment->avatar_user = URL::asset($profile->image);
		 		} else {
		 			$comment->avatar_user = $profile->image;
		 		}
		 		$comment->first_name = $profile->first_name;
		 		$comment->last_name = $profile->last_name;
				$data = $comment;
					    
			} else {
			    $error_code = ApiResponse::UNAVAILABLE_RATING;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
			}
	    } else {
	    	$error_code = ApiResponse::MISSING_PARAMS;
		    $data = $input;
	    }
	    
	    return array("code" => $error_code, "data" => $data);
 	}

 	public static function showCommentDetail($rating_id, $id)
 	{
 		$error_code = ApiResponse::OK;
		$comment = Comment::where('id', $id)->first();
		if(Rating::where('id',$rating_id)->first()) {
		    if($comment) {
		    	$profile = Profile::where('user_id', $comment->user_id)->first();
			 	if($profile->image != null) {
			 		$comment->avatar_user = URL::asset($profile->image);
			 	} else {
			 		$comment->avatar_user = $profile->image;
			 	}
			 	$comment->first_name = $profile->first_name;
			 	$comment->last_name = $profile->last_name;
		       	$data = $comment->toArray();
			} else {
				$error_code = ApiResponse::UNAVAILABLE_COMMENT;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
			}    
		} else {
			$error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
		
	    return array("code" => $error_code, "data" => $data);
 	}

 	public static function updateCommentDetail($rating_id, $id, $input) 
 	{
 		$comment = Comment::where('id', $id)->first();
	    $error_code = ApiResponse::OK;
	    if(Rating::where('id',$rating_id)->first()) {
	 		if($comment) {
				if(!empty($input['content'])) {
			    	$comment->content = $input['content'];
					$comment->save();
					$data = $comment->toArray();	    
				} else {
					$error_code = ApiResponse::MISSING_PARAMS;
			        $data = $input;
				}
		 	} else {
		 		$error_code = ApiResponse::UNAVAILABLE_COMMENT;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
		 	}
		 } else {
		 	$error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		 }
	    return array("code" => $error_code, "data" => $data);
 	}

 	public static function deleteComment($rating_id, $id)
 	{
 		$comment = Comment::where('id', '=', $id)->first();
 		$error_code = ApiResponse::OK;
 		if(Rating::where('id',$rating_id)->first()) {
		    if($comment) {
		    	$comment_profile = Profile::where('user_id', $comment->user_id)->first();
	            
	            if($comment_profile != null) {
	                $comment_profile->comment_count = $comment_profile->comment_count - 1;
	                $comment_profile->save();
	            }

		    	//update comment_count on rating

	 			$comment_rating = Rating::where('id', $comment->rating_id)->first();
				if($comment_rating != null) {
					$comment_rating->comment_count = $comment_rating->comment_count - 1;
					$comment_rating->save();
	 				$comment->delete();
	 			}
		 		$data = 'Comment deleted';
	 		} else {
	 			$error_code = ApiResponse::UNAVAILABLE_COMMENT;
		        $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT);
		    } 
		} else {
		 	$error_code = ApiResponse::UNAVAILABLE_RATING;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING);
		}
	    return array("code" => $error_code, "data" => $data);
 	}
}