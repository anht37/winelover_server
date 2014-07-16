<<<<<<< HEAD
<?php
 
class Wine extends Eloquent {
 
    protected $table = 'wines';
    protected $primaryKey = 'wine_id';

    public static $rules = array(
		'year' => 'required',
	);	 
	public static function boot(){
		parent::boot();
		Wine::observe(new WineObserver);
	}
}

class WineObserver {
	public function saved($model){
		$model->wine_unique_id = $model->wine_id . '_' . $model->year;
	}
}
=======
e<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 15/07/2014
 * Time: 09:39
 */

class Wine {
    public static function scan($file_path) {
        $query = Config::get('winedetect.script').' predict '.Config::get('winedetect.config').' '.$file_path;
        return exec($query);
    }
} 
>>>>>>> 21cdefbf848d0fa8527aadcd988cf071c8ae5ba5
