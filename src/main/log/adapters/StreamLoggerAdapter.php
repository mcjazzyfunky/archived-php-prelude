<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/StreamLog.php';
require_once __DIR__ . '/../LoggerAdapter.php';

use prelude\log\LoggerAdapter;
use prelude\log\adapters\internal\StreamLog;

class StreamLoggerAdapter implements LoggerAdapter {
    private $stream;
    private $logs;

    function __construct($stream) {
        $this->stream = $stream;
        $this->logs = [];
    }
    
    function getStream() {
        return $this->stream;
    }
    
    function getLog($domain) {
        $ret = @$this->logs[$domain];
        
        if ($ret === null) {
            $ret = new StreamLog($this->stream, $domain);
            $this->logs[$domain] = $ret;
        }
        
        return $ret;
    }
    
    function getLogLevelByDomain($domain) {
        return Logger::getDefaultLogLevel();
    }
}
