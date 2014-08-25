<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Wishlist extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'wishlists';
    protected $primaryKey = 'id';

    public function wine()
	{
    	return $this->belongsTo('Wine','wine_unique_id','wine_unique_id');
	}
	public static function getWishlist()
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$pagination = ApiResponse::pagination();
		if($pagination == false) {
			return array("code" => ApiResponse::URL_NOT_EXIST, "data" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST));
		}
		$page = $pagination['page'];
		$limit = $pagination['limit'];
		
		$wishlist = Wishlist::where('user_id', $user_id)->with('wine')->forPage($page, $limit)->get();
			if (count($wishlist) == 0) {
					$data = array();
			} else {	
				foreach ($wishlist as $wishlists) {
					$wishlists->winery = Winery::where('id', $wishlists->wine->winery_id)->first();

					if($wishlists->wine->image_url != null) {
			            $wishlists->wine->image_url = URL::asset($wishlists->wine->image_url);
				    }

				   	if($wishlists->wine->wine_flag != null) {
				       	 $wishlists->wine->wine_flag = URL::asset($wishlists->wine->wine_flag);
				   	} 
				}
						
			$data = $wishlist->toArray();
			}

		return array("code" => $error_code, "data" => $data);
	}

	public static function createNewWishlist($input)
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;

		$wishlist = new Wishlist;
		$wishlist->user_id = $user_id;
		if (!empty($input['wine_unique_id'])) {
			$wishlist->wine_unique_id = $input['wine_unique_id'];
			$wine_wishlist = Wishlist::where('wine_unique_id', $wishlist->wine_unique_id)->where('user_id',$user_id)->first();
			if($wine_wishlist) {
				$error_code = ApiResponse::DUPLICATED_WISHLIST_ADD;
				$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WISHLIST_ADD);
			} else {
				$wishlist->save();
				$data = $wishlist->toArray();
			}

		} else {
			$error_code = ApiResponse::MISSING_PARAMS;
	        $data = $input;
		}

	    return array("code" => $error_code, "data" => $data);
	}

	public static function deleteWishlist($wine_unique_id)
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$wishlist = Wishlist::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
	    if ($wishlist) {
	 		$wishlist->delete();
	 		$data = 'wine in wishlist is deleted';

 		} else {
 			$error_code = ApiResponse::NOT_EXISTED_WINE_WISHLIST;
	        $data = ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_WINE_WISHLIST);
	    } 
	    return array("code" => $error_code, "data" => $data);
	}
}