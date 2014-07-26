<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Wishlist extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'wishlists';
    protected $primaryKey = 'id';
}