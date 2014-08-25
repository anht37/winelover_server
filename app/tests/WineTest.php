<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WineTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'wine_id' => '2',
            'name' => 'Wine_2',
            'year' => '2009',
            'winery_id' => '1',
            'wine_unique_id' => '',
            'average_price' => '2',
            'average_rate' => '1'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/wine';
        $this->_models = array('Wine', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $wine_note = new Winenote();
        $wine_note->wine_unique_id = "1_2009";
        $wine_note->user_id = $this->_user_id;
        $wine_note->note = "this is note test";
        $wine_note->save();

        $follow = new Follow();
        $follow->from_id = $this->_user_id;
        $follow->to_id = "3620a42d-fcbb-45eb-b3a5-36cada1b77b7";
        $follow->save();
    }

    public function testCreateWineSuccess()
    {
        $_params = $this->_params;
        
        $response = $this->_getAuth($_params);
        //get created login information
        $wine_infor = Wine::get(array('name','wine_unique_id','year','winery_id', 'average_price', 'average_rate', 'updated_at', 'created_at','wine_id'))->last();
        $this->assertNotNull($wine_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWineErrorWrongWinery()
    {
        $_params = $this->_params;
        $_params['winery_id'] = "wrong_winery_id"; 
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))
        , json_decode($response->getContent(), true));
    }
    
    public function testCreateWineErrorMissingName()
    {
        $_params = $this->_params;
        unset($_params['name']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testCreateWineErrorMissingYear()
    {
        $_params = $this->_params;
        unset($_params['year']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testCreateWineErrorMissingWineryId()
    {
        $_params = $this->_params;
        unset($_params['winery_id']);
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testGetListWineSuccess() 
    {
        $response = $this->call('GET', 'api/wine');
        $page = 1;
        $limit = 10;
        $wine_infor = Wine::with('winery')->forPage($page, $limit)->get();
        $error_code = ApiResponse::OK;
        foreach ($wine_infor as $wines) {
            $wines->winery_id = $wines->winery->brand_name;
            if($wines->image_url != null) {
                $wines->image_url = URL::asset($wines->image_url);
            }   
            if($wines->wine_flag != null) {
                $wines->wine_flag = URL::asset($wines->wine_flag);
            } 
        }
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));
            
    }

    public function testGetListWineSuccessNoWine()
    {  
        $wine_infor = Wine::destroy(1);
        $response = $this->call('GET', 'api/wine');
        $wine = Wine::all();
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wine->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetListWineWrongPage() 
    {
        $response = $this->call('GET', 'api/wine?page=2');
        $page = 2;
        $limit = 10;
        $wine_infor = Wine::with('winery')->forPage($page, $limit)->get()->toArray();
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $wine_infor)
        , json_decode($response->getContent(), true)); 
    }

    public function testGetWineDetailSuccess()
    {   
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpWineNote();
        $this->setUpProfile();
        $response = $this->call('GET', 'api/wine/1');
        $wine_infor = Wine::where('wine_id', 1)->with('winery')->first();
        $country_name = Country::where('id',$wine_infor->winery->country_id)->first()->country_name;

        $wine_note = Winenote::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->first();

        $wine_infor->winenote = $wine_note->note;
        $wine_infor->winery->country_id = $country_name;

        $wishlist = Wishlist::where('user_id', $this->_user_id)->where('wine_unique_id', $wine_infor->wine_unique_id)->first();
        if($wishlist) {
            $wine_infor->is_wishlist = true;
        } else {
            $wine_infor->is_wishlist = false;
        }

        $all_wines_winery = Wine::where('winery_id', $wine_infor->winery_id)->whereNotIn('wine_id', [1])->get();
        $wine_infor->winery->count_wine = count($all_wines_winery) + 1 ;
        $rate_winery = $wine_infor->rate_count;
        if(count($all_wines_winery) !== 0) {
            
            $sum_rate_winery = $wine_infor->average_rate;
            foreach ($all_wines_winery as $wine_winery) {
                $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                
                $rate_count = $wine_on_winery->rate_count;
                $rate_winery = $rate_winery + $rate_count;
                
                $average_rate = $wine_on_winery->average_rate;
                $sum_rate_winery = $sum_rate_winery + $average_rate;
            }

            $wine_infor->winery->total_rate = $rate_winery;
            $wine_inforine->winery->average_rate_winery = $sum_rate_winery/count($all_wines_winery);
        } else {
            $wine_infor->winery->total_rate = $rate_winery;
            $wine_infor->winery->average_rate_winery = $wine_infor->average_rate;
        }

        $rating_user = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->with('profile')->first();
        if ($rating_user->profile->image != null) {
                $rating_user->profile->image = URL::asset($rating_user->profile->image);   
            }
        $rating = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->whereNotIn('user_id',[$this->_user_id])->with('profile')->get();
        if(count($rating) == 0) {
            $rating = array();
        } else {
            foreach ($rating as $ratings) {
                if ($ratings->profile->image != null) {
                    $ratings->profile->image = URL::asset($ratings->profile->image);   
                }
                $follow = Follow::where('from_id', $this->_user_id)->where('to_id', $ratings->user_id)->first();
                if($follow) {
                    $ratings->is_follow = true;
                } else {
                    $ratings->is_follow = false;
                }
            }
        }
        if($wine_infor->image_url != null) {
            $wine_infor->image_url = URL::asset($wine_infor->image_url);
        }   
        if($wine_infor->wine_flag != null) {
            $wine_infor->wine_flag = URL::asset($wine_infor->wine_flag);
        } 

        $data = array('wine' => $wine_infor->toArray(),'rate_user' => $rating_user->toArray() ,'rate' => $rating->toArray(), 'wine_related' => $all_wines_winery->toArray());

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));

    }

    public function testGetWineDetailSuccessNoRate_user()
    {   
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpProfile();
        $rating_user_destroy = Rating::destroy(1);
        $response = $this->call('GET', 'api/wine/1');
        //dd($response);
        $wine_infor = Wine::where('wine_id', 1)->with('winery')->first();

        $country_name = Country::where('id',$wine_infor->winery->country_id)->first()->country_name;

        $wine_note = Winenote::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->first();
        $wine_infor->winenote = $wine_note->note;
        
        $wishlist = Wishlist::where('user_id', $this->_user_id)->where('wine_unique_id', $wine_infor->wine_unique_id)->first();
        if($wishlist) {
            $wine_infor->is_wishlist = true;
        } else {
            $wine_infor->is_wishlist = false;
        }

        $wine_infor->winery->country_id = $country_name;
        $all_wines_winery = Wine::where('winery_id', $wine_infor->winery_id)->whereNotIn('wine_id', [1])->get();
        $wine_infor->winery->count_wine = count($all_wines_winery) + 1 ;
        $rate_winery = $wine_infor->rate_count;
        if(count($all_wines_winery) !== 0) {
            
            $sum_rate_winery = $wine_infor->average_rate;
            foreach ($all_wines_winery as $wine_winery) {
                $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                
                $rate_count = $wine_on_winery->rate_count;
                $rate_winery = $rate_winery + $rate_count;
                
                $average_rate = $wine_on_winery->average_rate;
                $sum_rate_winery = $sum_rate_winery + $average_rate;
            }

            $wine_infor->winery->total_rate = $rate_winery;
            $wine_inforine->winery->average_rate_winery = $sum_rate_winery/count($all_wines_winery);
        } else {
            $wine_infor->winery->total_rate = $rate_winery;
            $wine_infor->winery->average_rate_winery = $wine_infor->average_rate;
        }

        $rating_user = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->with('profile')->get();
        $rating = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->whereNotIn('user_id',[$this->_user_id])->with('profile')->get();
        if(count($rating) == 0) {
            $rating = "";
        } else {
            foreach ($rating as $ratings) {
                if ($ratings->profile->image != null) {
                    $ratings->profile->image = URL::asset($ratings->profile->image);   
                }
                $follow = Follow::where('from_id', $this->_user_id)->where('to_id', $ratings->user_id)->first();
                if($follow) {
                    $ratings->is_follow = true;
                } else {
                    $ratings->is_follow = false;
                }
            }
        }
        if($wine_infor->image_url != null) {
            $wine_infor->image_url = URL::asset($wine_infor->image_url);
        }   
        if($wine_infor->wine_flag != null) {
            $wine_infor->wine_flag = URL::asset($wine_infor->wine_flag);
        } 
        $data = array('wine' => $wine_infor->toArray(),'rate_user' => array() ,'rate' => $rating->toArray(), 'wine_related' => $all_wines_winery->toArray());
        //dd($data);
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));

    }
    public function testGetWineDetailNoRatingOtherUser()
    {   
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpWineNote();
        $this->setUpProfile();
        $rate_user1 = Rating::destroy(2);
        $rate_user2= Rating::destroy(3);
        $response = $this->call('GET', 'api/wine/1');
        $wine_infor = Wine::where('wine_id', 1)->with('winery')->first();
        $country_name = Country::where('id',$wine_infor->winery->country_id)->first()->country_name;

        $wine_note = Winenote::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->first();

        $wine_infor->winenote = $wine_note->note;
        $wine_infor->winery->country_id = $country_name;

        $wishlist = Wishlist::where('user_id', $this->_user_id)->where('wine_unique_id', $wine_infor->wine_unique_id)->first();
        if($wishlist) {
            $wine_infor->is_wishlist = true;
        } else {
            $wine_infor->is_wishlist = false;
        }

        $all_wines_winery = Wine::where('winery_id', $wine_infor->winery_id)->whereNotIn('wine_id', [1])->get();
        $wine_infor->winery->count_wine = count($all_wines_winery) + 1 ;
        $rate_winery = $wine_infor->rate_count;
        if(count($all_wines_winery) !== 0) {
            
            $sum_rate_winery = $wine_infor->average_rate;
            foreach ($all_wines_winery as $wine_winery) {
                $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                
                $rate_count = $wine_on_winery->rate_count;
                $rate_winery = $rate_winery + $rate_count;
                
                $average_rate = $wine_on_winery->average_rate;
                $sum_rate_winery = $sum_rate_winery + $average_rate;
            }

            $wine_infor->winery->total_rate = $rate_winery;
            $wine_infor->winery->average_rate_winery = $sum_rate_winery/count($all_wines_winery);
        } else {
            $wine_infor->winery->total_rate = $rate_winery;
            $wine_infor->winery->average_rate_winery = $wine_infor->average_rate;
        }

        $rating_user = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->with('profile')->first();
        if ($rating_user->profile->image != null) {
                $rating_user->profile->image = URL::asset($rating_user->profile->image);   
            }
        $rating = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->whereNotIn('user_id',[$this->_user_id])->with('profile')->get();
        if(count($rating) == 0) {
                $rating = array();
        }
        if($wine_infor->image_url != null) {
            $wine_infor->image_url = URL::asset($wine_infor->image_url);
        }   
        if($wine_infor->wine_flag != null) {
            $wine_infor->wine_flag = URL::asset($wine_infor->wine_flag);
        } 

        $data = array('wine' => $wine_infor->toArray(),'rate_user' => $rating_user->toArray() ,'rate' => $rating, 'wine_related' => $all_wines_winery->toArray());

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));

    }
    public function testGetWineDetailNoWineNote()
    {   
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpProfile();
        $wine_note = Winenote::destroy(1);
        $response = $this->call('GET', 'api/wine/1');
        $wine_infor = Wine::where('wine_id', 1)->with('winery')->first();

        $country_name = Country::where('id',$wine_infor->winery->country_id)->first()->country_name;

        $wine_infor->winenote = null;
        $wine_infor->winery->country_id = $country_name;
        
        $wishlist = Wishlist::where('user_id', $this->_user_id)->where('wine_unique_id', $wine_infor->wine_unique_id)->first();
        if($wishlist) {
            $wine_infor->is_wishlist = true;
        } else {
            $wine_infor->is_wishlist = false;
        }
        
        $all_wines_winery = Wine::where('winery_id', $wine_infor->winery_id)->whereNotIn('wine_id', [1])->get();
        $wine_infor->winery->count_wine = count($all_wines_winery) + 1 ;
        $rate_winery = $wine_infor->rate_count;
        if(count($all_wines_winery) !== 0) {
            
            $sum_rate_winery = $wine_infor->average_rate;
            foreach ($all_wines_winery as $wine_winery) {
                $wine_on_winery = Wine::where('wine_id', $wine_winery->wine_id)->first();
                
                $rate_count = $wine_on_winery->rate_count;
                $rate_winery = $rate_winery + $rate_count;
                
                $average_rate = $wine_on_winery->average_rate;
                $sum_rate_winery = $sum_rate_winery + $average_rate;
            }

            $wine_infor->winery->total_rate = $rate_winery;
            $wine_inforine->winery->average_rate_winery = $sum_rate_winery/count($all_wines_winery);
        } else {
            $wine_infor->winery->total_rate = $rate_winery;
            $wine_infor->winery->average_rate_winery = $wine_infor->average_rate;
        }

        $rating_user = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->where('user_id',$this->_user_id)->with('profile')->first();
        if ($rating_user->profile->image != null) {
               $rating_user->profile->image = URL::asset($rating_user->profile->image);   
            }
        $rating = Rating::where('wine_unique_id', $wine_infor->wine_unique_id)->whereNotIn('user_id',[$this->_user_id])->with('profile')->get();
        if(count($rating) == 0) {
            $rating = array();
        } else {
            foreach ($rating as $ratings) {
                if ($ratings->profile->image != null) {
                    $ratings->profile->image = URL::asset($ratings->profile->image);   
                }
                $follow = Follow::where('from_id', $this->_user_id)->where('to_id', $ratings->user_id)->first();
                if($follow) {
                    $ratings->is_follow = true;
                } else {
                    $ratings->is_follow = false;
                }
            }
        }
        if($wine_infor->image_url != null) {
            $wine_infor->image_url = URL::asset($wine_infor->image_url);
        }   
        if($wine_infor->wine_flag != null) {
            $wine_infor->wine_flag = URL::asset($wine_infor->wine_flag);
        } 
        $data = array('wine' => $wine_infor->toArray(),'rate_user' => $rating_user->toArray() ,'rate' => $rating->toArray(), 'wine_related' => $all_wines_winery->toArray());

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data)
        , json_decode($response->getContent(), true));

    }

    public function testGetWineDetailError()
    {
        $wine_infor = Wine::destroy(1);
        $response = $this->call('GET', 'api/wine/1');
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
        , json_decode($response->getContent(), true));

    }
    public function testUpdateWineSuccess()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'WineController@update', array('wine_id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $wine_infor = Wine::where('wine_id', 1)->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wine_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testUpdateWineErrorWrongWinery_id()
    {
        $_params = $this->_params;
        $_params['winery_id'] = 'wrong_winery_id';
        $response = $this->action('POST', 'WineController@update', array('wine_id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(json_encode(array("code" => ApiResponse::UNAVAILABLE_WINERY, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINERY))), $response->getContent());
    }

    public function testUpdateWineErrorNoWine()
    {
        $_params = $this->_params;
        $wine_infor = Wine::destroy(1);
        $response = $this->action('POST', 'WineController@update', array('wine_id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
        , json_decode($response->getContent(), true));
    }

    public function testUpdateWineErrorNoInput()
    {
        $data = array();
        $response = $this->action('POST', 'WineController@update', array('wine_id' => 1), array('data' => json_encode($data), '_method' => 'PUT'));
        $this->assertEquals(array("code" => ApiResponse::MISSING_PARAMS, "data" => $data)
        , json_decode($response->getContent(), true));
    }

     public function testDeleteWineNoWine()
    {  
        $wine_infor = Wine::destroy(1);
        $wine = Wine::where('wine_id', 1)->first();
        $response = $this->action('delete', 'WineController@destroy', array('wine_id' => 1));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_WINE, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_WINE))
        , json_decode($response->getContent(), true));
    }

    public function testDeleteWineSuccess() 
    {
        $response = $this->action('delete', 'WineController@destroy', array('wine_id' => 1));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Wine deleted")
         , json_decode($response->getContent(), true));
    }
}