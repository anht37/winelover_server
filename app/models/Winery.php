<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Winery extends Eloquent {
 	
 	use SoftDeletingTrait;
    protected $table = 'wineries';
    protected $primaryKey = 'id';
 
 	public function wines()
    {
        return $this->hasMany('Wine', 'winery_id');
    }
}