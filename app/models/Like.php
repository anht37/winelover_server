<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Like extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'likes';
    protected $primaryKey = 'id';
}