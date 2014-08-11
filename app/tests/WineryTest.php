<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class WineryTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'id' => '2',
            'brand_name' => 'Sweet Lips',
            'country_id' => '1',
            'region' => 'Abkhazia',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/winery';
        $this->_models = array('Winery', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
    }

    public function testGetListWinerySuccess()
    {
       
    }
    
    public function testGetListWineryErrorNoWinery()
    {
        
    }

    public function testGetWineryDetailSuccess()
    {
        
    }

    public function testGetWineryDetailErrorNoWinery() 
    {
       
    }

    public function testCreateWinerySuccess()
    {
        
    }
    
    public function testDeleteWinerySuccess()
    {
       
    }

    public function testDeleteWineryErrorNoWinery()
    {
       
    }

}