<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait; 
class Comment extends Eloquent {
	use SoftDeletingTrait;
 
    protected $table = 'comments';
    protected $primaryKey = 'id';
 	
}