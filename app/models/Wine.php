
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

	public static function scan($file_path) {
        $query = Config::get('winedetect.script').' predict '.Config::get('winedetect.config').' '.$file_path;
        return exec($query);
}

class WineObserver {
	public function saved($model){
		$model->wine_unique_id = $model->wine_id . '_' . $model->year;
	}
}