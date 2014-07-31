<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Winenote extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'wine_notes';
    protected $primaryKey = 'id';
}