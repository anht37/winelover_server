<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Winery extends Eloquent {
 	
 	use SoftDeletingTrait;
    protected $table = 'wineries';
    protected $primaryKey = 'id';
    protected $guarded = array();
 
 	public function wines()
    {
        return $this->hasMany('Wine', 'winery_id');
    }
    public function country()
    {
        return $this->belongsTo('Country', 'country_id');
    }
}