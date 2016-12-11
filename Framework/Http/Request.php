<?php

namespace Framework\Http;

use Framework\Traits\Singleton;

class Request {

    use Singleton;

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    protected static $headers = [];

    protected $method;

    protected $code;

    protected $fields = [];

    protected $body;

    protected $cookie;

    protected $session;


    protected function __construct(array $userSettings = []) {

        self::setDefaultEnvironment();
        self::setCurrentEnvironment();

        $this->setQueryData();

        if( ! empty($userSettings))
            self::$headers = array_merge(self::$headers, $userSettings);

    }

    private static function setCurrentEnvironment() {

        self::$headers['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];

        self::$headers['QUERY_STRING'] = urldecode($_SERVER['QUERY_STRING']); // example.com/page/?a=b&c=d -> a=b&c=d

        self::$headers['REQUEST_URI_FULL'] = urldecode($_SERVER['REQUEST_URI']);

        self::$headers['REQUEST_URI'] = str_replace([self::$headers['QUERY_STRING'], '?'], '', self::$headers['REQUEST_URI_FULL']); // example.com/page/?a=b&c=d -> /page/

        self::$headers['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;

        self::$headers['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'];

        self::$headers['SERVER_SOFTWARE'] = $_SERVER['SERVER_SOFTWARE'] ?? '';

        self::$headers['CONTENT_LENGTH'] = $_SERVER['CONTENT_LENGTH'];

        self::$headers['CONTENT_TYPE'] = $_SERVER['CONTENT_TYPE'];

        self::$headers['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

        self::$headers['ACCEPT'] = $_SERVER['HTTP_ACCEPT'];

        self::$headers['PATH_INFO'] = $_SERVER['PATH_INFO'];

//        self::$headers['COOKIE'] = $_SERVER['HTTP_COOKIE'];

        self::$headers['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
    }

    private static function setDefaultEnvironment() {

        self::$headers = [
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'USER_AGENT' => 'localhost',
            'REMOTE_ADDR' => '127.0.0.1'
        ];

    }

    private function setQueryData(){

        $input = file_get_contents('php://input');

        if($input === false)
            throw new \Exception('Can`t get data by ' . $this->method);

        $this->body = $input;

        $this->fields = array_diff($_REQUEST, $_COOKIE); // all but cookie

        $this->cookie = $_COOKIE;

        if(session_status() != PHP_SESSION_NONE)
            $this->session = $_SESSION;

    }

    public function headers() {
        
        if(empty(self::$headers)){
            self::setDefaultEnvironment();
            self::setCurrentEnvironment();
        }

        return self::$headers;
    }

    public function getUri() : string {
        return self::$headers['REQUEST_URI'];
    }

    public function getUriFull() : string {
        return self::$headers['REQUEST_URI_FULL'];
    }

    public function getMethod() : string {
        return self::$headers['REQUEST_METHOD'];
    }

}