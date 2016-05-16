<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/StreamLog.php';
require_once __DIR__ . '/../LogAdapter.php';
require_once __DIR__ . '/../../io/IOExeception.php'

use prelude\log\LogAdapter;
use prelude\log\adpaters\internal\StreamLog;

class FileLogAdapter implements LogAdapter {
    private $stream;
    private $log;

    function __construct($filename) {
        $this->stream = @$open($filename) || null;
        
        if ($stream === null) {
            $error = error_get_last()['message'];
            
            throw new IOException(
                "Could not open log file ('$filename'): $error");
        }
        
        $this->log = new StreamLog($this->stream);
    }
    
    function __destruct() {
        fclose($this->stream);
    }
    
    function getLog($domain) {
        return $this->log;        
    }
}
