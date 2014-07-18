
<?php
 
class Wine extends Eloquent {
 
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

    public static function paginate($getPage, $getLimit) {
        $paginate = array(
            'page' => $getPage,
            'limit' => $getLimit
        );
        //dd($paginate);
        $rules = array(
            'page' => 'integer',
            'limit' =>'integer'
        );
        //dd(Validator::make($paginate, $rules));
        $validator = Validator::make($paginate, $rules);

        if($validator->fails()) {
            return 'FALSE';
        } else {
            return $paginate;
        }
        
    }
}
