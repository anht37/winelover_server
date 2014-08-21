<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WishlistTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'id' => '2',
            'user_id' => '',
            'wine_unique_id' => '1_2009',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/wishlist';
        $this->_models = array('Wishlist', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();
        $wishlist = new Wishlist();
        $wishlist->id = 1;
        $wishlist->user_id = $this->_user_id;
        $wishlist->wine_unique_id = "1_2009";
        $wishlist->save();
        
    }
    public function testGetListWishlistSuccess()
    {
        $response = $this->call('GET', 'api/wishlist');

        $wishlist_infor = Wishlist::all();
        $pagination = ApiResponse::pagination();
        $page = $pagination['page'];
        $limit = $pagination['limit'];
        
        $wishlist_infor = Wishlist::where('user_id', $this->_user_id)->with('wine')->forPage($page, $limit)->get();
               
        foreach ($wishlist_infor as $wishlists) {
            $wishlists->winery = Winery::where('id', $wishlists->wine->winery_id)->first()->toArray();

            if($wishlists->wine->image_url != null) {
                $wishlists->wine->image_url = URL::asset($wishlists->wine->image_url);
            }

            if($wishlists->wine->wine_flag != null) {
                $wishlists->wine->wine_flag = URL::asset($wishlists->wine->wine_flag);
            } 
        }
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wishlist_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testGetListWishlistSuccessNoWishlist()
    {
        $wishlist = Wishlist::destroy(1);
        $response = $this->call('GET', 'api/wishlist');

        $wishlist_infor = Wishlist::all();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wishlist_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWishlistSuccess()
    {
        $wishlist = Wishlist::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $wishlist_infor = Wishlist::get(array('user_id','wine_unique_id', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($wishlist_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wishlist_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWishlistErrorWrongWine()
    {
        $wishlist = Wishlist::destroy(1);
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $_params['wine_unique_id'] = "wrong_wine_unique_id";

        $response = $this->_getAuth($_params);
        //get created login information
        $wishlist_infor = Wishlist::get(array('user_id','wine_unique_id', 'updated_at', 'created_at','id'))->last();
        $this->assertNotNull($wishlist_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $wishlist_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testCreateWishlistErrorDuplicatedWishlist()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $response = $this->_getAuth($_params);
        //get created login information
        $wishlist_infor = Wishlist::get(array('user_id','wine_unique_id', 'updated_at', 'created_at','id'))->last();

        $this->assertNotNull($wishlist_infor);
        $this->assertEquals(array("code" => ApiResponse::DUPLICATED_WISHLIST_ADD, "data" =>  ApiResponse::getErrorContent(ApiResponse::DUPLICATED_WISHLIST_ADD))
        , json_decode($response->getContent(), true)); 
    }

    public function testDeleteWishlistSuccess()
    {
        $response = $this->action('delete', 'WishlistController@destroy', array('wine_unique_id' => "1_2009"));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "wine in wishlist is deleted")
         , json_decode($response->getContent(), true));
    }

    public function testDeleteWishlistErrorNoWishlist()
    {
        $wishlist = Wishlist::destroy(1);
        $response = $this->action('delete', 'WishlistController@destroy', array('wine_unique_id' => "1_2009"));
        $this->assertEquals(array("code" => ApiResponse::NOT_EXISTED_WINE_WISHLIST, "data" => ApiResponse::getErrorContent(ApiResponse::NOT_EXISTED_WINE_WISHLIST))
         , json_decode($response->getContent(), true));
    }
}