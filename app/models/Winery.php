<?php
 
class Winery extends Eloquent {
 
    protected $table = 'wineries';
    protected $primaryKey = 'id';
 
 	public function wines()
    {

        return $this->hasMany('Wine', 'winery_id');
    }
}