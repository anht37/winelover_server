<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Winenote extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'wine_notes';
    protected $primaryKey = 'id';


	public static function getListWinenote()
	{
		$user_id = Session::get('user_id');
    	$winenote = Winenote::where('user_id', $user_id)->get();
		$error_code = ApiResponse::OK;
		if($winenote) {
			$data = $winenote->toArray();
		} else {
		    $data = array();
		}
		return array("code" => $error_code, "data" => $data);
	}

	public static function createNewWinenote($input)
	{
		$user_id = Session::get('user_id');
    	$winenote = new Winenote;
		$error_code = ApiResponse::OK;
		
		if(!empty($input['wine_unique_id']) && !empty($input['note'])) {
			$winenote->wine_unique_id = $input['wine_unique_id'];
			$winenote->note = $input['note'];
		 	$winenote->user_id = $user_id;
		    // Validation and Filtering is sorely needed!!
		    // Seriously, I'm a bad person for leaving that out.
		 	if(Wine::where('wine_unique_id',$winenote->wine_unique_id)->first()) {
		 		if(Winenote::where('wine_unique_id',$winenote->wine_unique_id)->where('user_id', $winenote->user_id)->first()) {
		 			$error_code = ApiResponse::DUPLICATED_WINE_NOTE_ADD;
	            	$data = ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WINE_NOTE_ADD);
		 		} else {
		 			$winenote->save();
	            	$data = $winenote;
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

	public static function getWinenoteDetail($wine_unique_id)
	{
		$user_id = Session::get('user_id');
    	$winenote = Winenote::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
		$error_code = ApiResponse::OK;
		if($winenote) {
			
			$data = $winenote->toArray();
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE_NOTE;
		    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE);
		}
		return array("code" => $error_code, "data" => $data);
	}

	public static function updateWinenoteDetail($wine_unique_id, $input)
	{
		$user_id = Session::get('user_id');
		$error_code = ApiResponse::OK;
		$Wine = Wine::where('wine_unique_id',$wine_unique_id)->first();
		if($Wine) {
	    	$winenote = Winenote::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
			if ($winenote) {
				if(!empty($input)) {
					if(!empty($input['note']) || $input['note'] == null) {
						$winenote->note = $input['note'];
					}
					$winenote->save();
				    $data = $winenote; 	
					
				} else {
					$error_code = ApiResponse::MISSING_PARAMS;
				    $data = $input;
				}
			} else {
				$error_code = ApiResponse::UNAVAILABLE_WINE_NOTE;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE;
			$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		}
	    return array("code" => $error_code, "data" => $data);
	}

	public static function deleteWinenote($wine_unique_id)
	{
		$user_id = Session::get('user_id');
    	$winenote = Winenote::where('user_id', $user_id)->where('wine_unique_id', $wine_unique_id)->first();
		$error_code = ApiResponse::OK;
		if(Wine::where('wine_unique_id',$wine_unique_id)->first()) {
			if($winenote) {
				$winenote->delete();
				$data = 'Wine note is deleted';
			} else {
				$error_code = ApiResponse::UNAVAILABLE_WINE_NOTE;
			    $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE_NOTE);
			}
		} else {
			$error_code = ApiResponse::UNAVAILABLE_WINE;
			$data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE);
		}
		return array("code" => $error_code, "data" => $data);
	}



}