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
        return $connection->sendRequest($file_path,"PRED");
    }
    public function winery()
	{
    	return $this->belongsTo('Winery','winery_id');
	}

    public static function searchWinefromMywine($wine_name)
    {   
        $error_code = ApiResponse::OK;
        $data = array();
        $user_id = Session::get('user_id');
        
        $wine = Wine::where('name','LIKE','%'.$wine_name.'%')->with('winery')->get();
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
        return array("code" => $error_code, "data" => $data);
    }
    
    // public static function cmp($a, $b) {
    //     $a['user_id'] = Session::get('user_id');
    //     if($a['user_id'] == $b['user_id']) {
    //             return 1;
    //     } else {
    //             return 0;
    //     }   
    // }
}
