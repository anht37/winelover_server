<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
 
class Profile extends Eloquent {

	use SoftDeletingTrait;
 
    protected $table = 'profiles';
    protected $primaryKey = 'id';

    public function rating()
    {
        return $this->hasMany('Rating','user_id', 'user_id');
    }
    public function wine($input)
    {
    	$rating = Rating::where('id', $input)->first();
        return $rating->belongsTo('Wine','wine_unique_id', 'wine_unique_id');
    }
}