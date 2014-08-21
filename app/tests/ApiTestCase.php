<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 08/07/2014
 * Time: 15:51
 */

class ApiTestCase extends TestCase {
    protected $_params;

    protected $_method;
    protected $_uri;
    protected $_models;

    protected function _getResponse($params = null)
    {
        $_params = $this->_params;
        if($params !== null) {
            $_params = $params;
        }
        $response = $this->call($this->_method, $this->_uri, array('data' => json_encode($_params)));
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }
    protected function _getAuth ($params = null)
    {
        $_params = $this->_params;
        if($params !== null) {
            $_params = $params;
        }
        $response = $this->call($this->_method, $this->_uri, array('data' => json_encode($_params)), array(), array("HTTP_session"=> $this->_session));
        $this->assertTrue($this->client->getResponse()->isOk());
        return $response;
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetEvents();
    }

    public function tearDown()
    {

        // Truncate all tables.
        foreach ($this->_models as $model) {
            // Flush any existing listeners.
            call_user_func(array($model, 'truncate'));
        }
    }

    private function resetEvents()
    {

        // Reset their event listeners.
        foreach ($this->_models as $model) {

            // Flush any existing listeners.
            call_user_func(array($model, 'flushEventListeners'));

            // Reregister them.
            call_user_func(array($model, 'boot'));
        }
    }
    public function setUpData()
    {
        $user = new User();
        $user->email = 'testacc@gmail.com';
        $user->password = '123456';
        $user->fb_id = '123456';
        $user->save();
        $this->_user_id = User::where('email','testacc@gmail.com')->first()->user_id;

        $login = new Login();
        $login->id = 1;
        $login->user_id = $this->_user_id ;
        $login->session_id = '3f9a362bb40714f77cadfd9f5b9d801b';
        $login->expired_at = '2019-07-30';
        $login->save();
        
        $this->_session = '3f9a362bb40714f77cadfd9f5b9d801b';
        $this->_user_id = Login::where('session_id', $this->_session)->first()->user_id;

        $winery = new Winery();
        $winery->id = 1;
        $winery->brand_name = 'Winery 1';
        $winery->country_id = '1';
        $winery->region = 'Abkhazia';
        $winery->save();

        $wine = new Wine();
        $wine->wine_id = 1 ;
        $wine->name = 'Wine_1';
        $wine->rakuten_id = 'rakuten_drinkshop_10508227';
        $wine->original_name = "this is wine_1";
        $wine->original_name_2 = "wine_1";
        $wine->winery_id = 1;
        $wine->year = '2009';
        $wine->wine_unique_id = '1_2009';
        $wine->average_price = "2200.00";
        $wine->average_rate = "3.5";
        $wine->rate_count = "3";
        $wine->save();

        $this->session(array('user_id' => $this->_user_id ));

    }

    public function setUpRating()
    {
        $rating = new Rating();
        $rating->id = 1;
        $rating->user_id = $this->_user_id;
        $rating->wine_unique_id = '1_2009';
        $rating->rate = '3.5';
        $rating->comment_count = '8';
        $rating->like_count = '6';
        $rating->save();

        $rating = new Rating();
        $rating->id = 2;
        $rating->user_id = '3620a42d-fcbb-45eb-b3a5-36cada1b77b7';
        $rating->wine_unique_id = '1_2009';
        $rating->rate = '2';
        $rating->comment_count = '18';
        $rating->like_count = '7';
        $rating->save();

        $rating = new Rating();
        $rating->id = 3;
        $rating->user_id = '1803a4fd-0e90-45ae-ad75-d8d3d2448d5e';
        $rating->wine_unique_id = '1_2009';
        $rating->rate = '5';
        $rating->comment_count = '3';
        $rating->like_count = '4';
        $rating->save();
    }

    public function setUpCountry()
    {
        $country = new Country();
        $country->id = 1;
        $country->country_name = 'Abkhazia';
        $country->flag_url = 'flags/Abkhazia.png';
        $country->save();
    }

    public function setUpWineNote()
    {
        $winenote = new Winenote();
        $winenote->wine_unique_id = '1_2009';
        $winenote->note = 'this is note test';
        $winenote->user_id = $this->_user_id;
        $winenote->save();
    }

    public function setUpProfile()
    {
        $profile = new Profile();
        $profile->id = 1;
        $profile->user_id = $this->_user_id;
        $profile->follower_count = '3';
        $profile->following_count = '5';
        $profile->rate_count = '1';
        $profile->comment_count = '2';
        $profile->scan_count = '5';
        $profile->last_name = 'pro1';
        $profile->first_name = 'user login';
        $profile->save();

        $profile = new Profile();
        $profile->id = 2;
        $profile->user_id = '3620a42d-fcbb-45eb-b3a5-36cada1b77b7' ;
        $profile->follower_count = '4';
        $profile->following_count = '7';
        $profile->rate_count = '6';
        $profile->comment_count = '9';
        $profile->scan_count = '15';
        $profile->last_name = 'pro2';
        $profile->first_name = 'user other 1';
        $profile->save();

        $profile = new Profile();
        $profile->id = 3;
        $profile->user_id = '1803a4fd-0e90-45ae-ad75-d8d3d2448d5e' ;
        $profile->follower_count = '13';
        $profile->following_count = '2';
        $profile->rate_count = '15';
        $profile->comment_count = '12';
        $profile->scan_count = '8';
        $profile->last_name = 'pro3';
        $profile->first_name = 'user login';
        $profile->save();
        
    }
    public function setUpWishlist()
    {
        $wishlist = new Wishlist();
        $wishlist->wine_unique_id = '1_2009';
        $wishlist->user_id = $this->_user_id;
        $wishlist->save();
    }

} 