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
    
}