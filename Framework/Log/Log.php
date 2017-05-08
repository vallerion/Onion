<?php

namespace Framework\Log;

use Framework\Traits\Singleton;

class Log {

    use Singleton;


    protected $logFile = 'onion';

    protected $types = [
        'message'   =>  'MESSAGE',
        'error'     =>  ' ERROR ',
    ];


    public function log($fileName) {

        if( ! preg_match('/^[a-z0-9-]+$/', $fileName))
            throw new \InvalidArgumentException;

        $this->logFile = $fileName;

        return $this;
    }

    public function write($message) {

        $fullPath = $this->logPath();

        $message = $this->makeMessage($message, 'message');

        file_put_contents(
            $fullPath,
            $message,
            FILE_APPEND
        );

        return $this;
    }

    public function error($message) {

        $fullPath = $this->logPath();

        $message = $this->makeMessage($message, 'error');

        file_put_contents(
            $fullPath,
            $message,
            FILE_APPEND
        );

        return $this;
    }

    protected function makeMessage($message, $type) {

        $date = date('d.m.Y H:i');
        $type = isset($this->types[$type]) ? $this->types[$type] : $this->types[0];

        return "[$date] [$type]" . chr(9) . "$message" . chr(10);
    }

    protected function logPath() {
        return __DIR__ . '/../../logs/' . $this->logFile . '.log';
    }

}
