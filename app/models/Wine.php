<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait; 
class Wine extends Eloquent {
    
    use SoftDeletingTrait;
    protected $table = 'wines';
    protected $primaryKey = 'wine_id';

    public static $rules = array(
		'year' => 'required',
	);	 

	public static function scan($file_path) {
        $query = Config::get('winedetect.script').' predict '.Config::get('winedetect.config').' '.$file_path;
        return exec($query);
    }
    public function winery()
	{
    	return $this->belongsTo('Winery','winery_id');
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
