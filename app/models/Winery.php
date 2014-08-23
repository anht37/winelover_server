<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Winery extends Eloquent {
 	
 	use SoftDeletingTrait;
    protected $table = 'wineries';
    protected $primaryKey = 'id';
    protected $guarded = array();
 
 	public function wines()
    {
        return $this->hasMany('Wine', 'winery_id');
    }

    public function country()
    {
        return $this->belongsTo('Country', 'country_id');
    }

    public static function getListWinery()
    {
        $winery = Winery::all();
        
        $error_code = ApiResponse::OK;
        $data = $winery->toArray();
        
        return array("code" => $error_code, "data" => $data);
    }

    public static function getWineryDetail($id)
    {
        $error_code = ApiResponse::OK;
        $winery = Winery::where('id', $id)->first();
        if($winery) {
            $data = $winery->toArray();
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINERY;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
        }
        
        return array("code" => $error_code, "data" => $data);
    }

    public static function createNewWinery($input)
    {
        $winery = new Winery;
        $error_code = ApiResponse::OK;
        if(!empty($input['brand_name'])) {
            $winery->brand_name = $input['brand_name'];

            if (!empty($input['country_id'])) {
                if( Country::where('id' , $input['country_id'])->first()) {
                    $winery->country_id = $input['country_id'];
                } else {
                    $winery->country_id = null;
                }
            }
            if (!empty($input['region'])) {
                $winery->region = $input['region'];
            }
            if (!empty($input['description'])) {
                $winery->description = $input['description'];
            }
         
            // Validation and Filtering is sorely needed!!
            // Seriously, I'm a bad person for leaving that out.
            $winery->save();
            $data = $winery->toArray();
        } else {
            $error_code = ApiResponse::MISSING_PARAMS;
            $data = $input;
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function updateWineryDetail($id, $input)
    {
        $winery = Winery::where('id', $id)->first();
        $error_code = ApiResponse::OK;
        if($winery) {
            if(!empty($input)) {
                if (!empty($input['brand_name'])) {
                    $winery->brand_name = $input['brand_name'];
                }
                if (!empty($input['country_id'])) {
                    if( Country::where('id' , $input['country_id'])->first()) {
                        $winery->country_id = $input['country_id'];
                    } else {
                        $winery->country_id = null;
                    }
                }
                if (!empty($input['region'])) {
                    $winery->region = $input['region'];
                }
                if (!empty($input['description'])) {
                    $winery->description = $input['description'];
                }
                $winery->save();
                
                $data = $winery->toArray();
            } else {
                $error_code = ApiResponse::MISSING_PARAMS;
                $data = $input;
            }
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINERY;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
        }
        return array("code" => $error_code, "data" => $data);
    }

    public static function deleteWinery($id)
    {
        $winery = Winery::where('id', $id)->first();
        $error_code = ApiResponse::OK;
        if($winery) {
            $winery->delete();
            
            $data = 'Winery deleted';
        } else {
            $error_code = ApiResponse::UNAVAILABLE_WINERY;
            $data = ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY);
        } 
        return array("code" => $error_code, "data" => $data);
    }
}