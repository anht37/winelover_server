<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Follow extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'follows';
    protected $primaryKey = 'id';
}