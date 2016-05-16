<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/StreamLog.php';
require_once __DIR__ . '/../LogAdapter.php';

use prelude\log\LogAdapter;
use prelude\log\adapters\internal\StreamLog;

class StreamLogAdapter implements LogAdapter {
    private $logs;

    function __construct($stream) {
        $this->stream = $stream;
        $this->log = [];
    }
    
    function getLog($domain) {
        $ret = @$this->logs[$domain];
        
        if ($log === null) {
            $ret = new StreamLog($this->stream, $domain);
            $this->logs[$domain] = $ret;
        }
        
        return $ret;
    }
}
