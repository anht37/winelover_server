<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Wishlist extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'wishlists';
    protected $primaryKey = 'id';

    public function wine()
	{
    	return $this->belongsTo('Wine','wine_unique_id','wine_unique_id');
	}
}