<?php

/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 04/08/2014
 * Time: 10:24
 */
class TcpConnetion
{
    static protected $_maxConn = 4;
    static protected $_currentConnIndex = 0;
    static protected $_connections;
    static protected $_action = "PING";
    protected $_conn;

    public function __construct($hostname = "localhost", $port = "2088") {
//        if (!is_array(self::$_connections)) {
//            self::$_connections = array();
//        }
//        if (empty(self::$_connections[self::$_currentConnIndex])) {
//            self::$_connections[self::$_currentConnIndex] = fsockopen($hostname, $port);
//        }
//        self::$_currentConnIndex = self::$_currentConnIndex % 4;
//        $connection = self::$_connections[self::$_currentConnIndex];
//        self::$_currentConnIndex++;
//        $this->_conn = $connection;
        $this->_conn = pfsockopen($hostname, $port);
    }


    public function sendRequest($data, $action = "PING")
    {
        $header = unpack('C*',$action);
        $length = unpack('C*', pack("L", strlen($data)));
        $footer = unpack('C*',$data);
        $send_data = array_merge($header,$length,$footer);
        if(!$this->_conn) {
            return false;
        }
        fwrite($this->_conn, implode(array_map("chr",$send_data)), count($send_data));

        $header = fread($this->_conn,4);

        $length = unpack("C*",fread($this->_conn, 4));

        $len = 0;
        foreach($length as $key => $value) {
            $len += $value << ($key-1)*8;
        }
        $data = fread($this->_conn,$len*1);

        return $data;
    }


}

