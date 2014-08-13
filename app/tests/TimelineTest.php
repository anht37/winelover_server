<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class TimelineTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'user_id' => '',
        );
        $this->_method = 'GET';
        $this->_uri = 'api/timeline';
        $this->_models = array('Rating', 'User', 'Login');
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
    public function testGetTimelineSuccess()
    {
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpWineNote();
        $this->setUpProfile();
        $_params = $this->_params;
        $_params['user_id'] = "user_id";
        $response = $this->_getAuth($_params);

        $error_code = ApiResponse::OK;
        $user_timeline = array();
        $user_timeline[] = $this->_user_id;
        $user_follow = Follow::where('from_id', $this->_user_id)->orderBy('updated_at', 'asc')->get();
        
        if(isset($user_follow)) {
            foreach($user_follow as $user) {
                $user_timeline[] = $user->to_id;
            }
        }
        $pagination = ApiResponse::pagination();
        $page = $pagination['page'];
        $limit = $pagination['limit'];
        $wine = Wine::with('winery')->forPage($page, $limit)->get();

        $ratings = Rating::whereIn('user_id', $user_timeline)->whereNotNull('wine_unique_id')->with('profile')->with('wine')->forPage($page, $limit)->get();

        foreach ($ratings as $rating) {   
            $winery = Winery::where('id', $rating->wine->winery_id)->first();
            $rating->winery = $winery;
            $country = Country::where('id', $rating->winery->country_id)->first();
            $rating->winery->country_name = $country->country_name; 
            $like = Like::where('user_id',$this->_user_id)->where('rating_id', $rating->id)->first();
            if($like) {
                $rating->liked = true;
            } else {
                $rating->liked = false;
            }
            $wishlist = Wishlist::where('user_id',$this->_user_id)->where('wine_unique_id',$rating->wine_unique_id)->first();
            if($wishlist) {
                $rating->wishlist = true;
            } else {
                $rating->wishlist = false;
            }
            if ($rating->wine->image_url != null) {
                $rating->wine->image_url = URL::asset($rating->wine->image_url);
            }
            if ($rating->wine->wine_flag != null) {
                $rating->wine->wine_flag = URL::asset($rating->wine->wine_flag);
            }
            if ($rating->profile->image != null) {
                 $rating->profile->image = URL::asset($rating->profile->image);   
            }
             $rating->winery = $rating->winery->toArray();
        }
        $data = $ratings;

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $ratings->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetTimelineSuccessNoRating()
    {
        $this->setUpCountry();
        $this->setUpWineNote();
        $this->setUpProfile();
        $_params = $this->_params;
        $_params['user_id'] = "user_id";
        $response = $this->_getAuth($_params);

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "")
        , json_decode($response->getContent(), true));
    }
    
    public function testGetTimelineErrorPage()
    {
        $this->setUpRating();
        $this->setUpCountry();
        $this->setUpWineNote();
        $this->setUpProfile();
        $_params = $this->_params;
        $_params['user_id'] = "user_id";

        $this->_uri = 'api/timeline?page=2';
        $response = $this->_getAuth($_params);

        $this->assertEquals(array("code" => ApiResponse::URL_NOT_EXIST, "data" => ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST))
        , json_decode($response->getContent(), true));
    }
}