<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Country extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'mst_countries';
    protected $primaryKey = 'id';
}