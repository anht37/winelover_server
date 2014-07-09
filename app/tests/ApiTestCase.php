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

} 