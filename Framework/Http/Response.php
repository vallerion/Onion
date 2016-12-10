<?php

namespace Framework\Http;

use Framework\Traits\Singleton;
use Framework\Traits\Codes;

class Response {

    use Singleton;
    use Codes;

    protected $status;

    protected $headers;

    protected $cookies;

    protected $body;

    protected $length;
    
    protected function __construct() {
        
    }

    public function __toString() : string {
        // todo: must be return view (html)
//        return (string)__CLASS__;
    }

}