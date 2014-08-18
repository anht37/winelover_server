<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class GetRatingMyWineTest extends ApiTestCase
{

    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        
        $this->_params = array(
            'user_id' => '',
            'wine_unique_id' => '1_2009',
            'rate' => '3.5',
            'comment_count' => '3',
            'like_count' => '2',
            'is_my_wine' => '1'
        );
        $this->_method = 'POST';
        $this->_uri = 'api/rating';
        $this->_models = array('Rating', 'User', 'Login');
    }
    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        //$this->setUpRating();
    }
    //test cases for Login by email - password successfully
    public function testGetListRatingMyWineSuccessNoRating()
    {  
        $response = $this->call('GET', 'api/rating');

        $rating_infor = Rating::with('wine')->get();

        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "")
        , json_decode($response->getContent(), true));
    }

    public function testGetListRatingMyWineSuccess() 
    {
        $this->setUpRating();
        $response = $this->call('GET', 'api/rating');
        
        $page = 1;
        $limit = 10;
        $rating = Rating::where('user_id', $this->_user_id)->where('is_my_wine', 1)->with('wine')->orderBy('updated_at', 'desc')->forPage($page, $limit)->get();
        
        foreach ($rating as $ratings) {
            $ratings->winery = Winery::where('id',$ratings->wine->winery_id)->first()->toArray();
            if($ratings->wine->image_url != null) {
                $ratings->wine->image_url = URL::asset($ratings->wine->image_url);
            }

            if($ratings->wine->wine_flag != null) {
                $ratings->wine->wine_flag = URL::asset($ratings->wine->wine_flag);
            }
        } 
        $data = $rating;
            
    
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $data->toArray())
        , json_decode($response->getContent(), true));
            
    }

    public function testGetListRatingMyWineWrongPage() 
    {
        $this->setUpRating();
        $response = $this->call('GET', 'api/rating?page=2');
        
        $this->assertEquals(array("code" => ApiResponse::URL_NOT_EXIST, "data" =>  ApiResponse::getErrorContent(ApiResponse::URL_NOT_EXIST))
        , json_decode($response->getContent(), true)); 
    }

    public function testGetRatingDetailSuccess() 
    {
        $this->setUpRating();
        $response = $this->call('GET', 'api/rating/1');
        $rating_infor = Rating::where('id', 1)->with('wine')->first();
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => $rating_infor->toArray())
        , json_decode($response->getContent(), true));

    }
    
    public function testGetRatingDetailErrorNoRating() 
    {
        $response = $this->call('GET', 'api/rating/1');
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));

    }
}

