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
        $winery->country_id = '2';
        $winery->region = 'Chile';
        $winery->save();

        $wine = new Wine();
        $wine->wine_id = 1 ;
        $wine->name = 'Wine_1';
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
    }

} 