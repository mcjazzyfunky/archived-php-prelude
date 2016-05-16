<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/StreamLog.php';
require_once __DIR__ . '/../LogAdapter.php';

use prelude\log\LogAdapter;
use prelude\log\adapters\internal\StreamLog;

class StreamLogAdapter implements LogAdapter {
    private $log;

    function __construct($stream) {
        $this->log = new StreamLog($stream);
    }
    
    function getLog($domain) {
        return $this->log;        
    }
}
