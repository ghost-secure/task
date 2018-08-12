<?php

namespace Api;

/**
 * @file
 * Request api class file
 */

/**
 * @class Request api class
 */
class Client {

    private $host; 
    private $user;
    private $password;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get Data
     * @param array $request
     * @return string
     */
    public static function send($request) {
        /*
             connect here to external service
        */     
        return array(1,2,3);
    } 


}
