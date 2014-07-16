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
