<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/NoOperationLog.php';
require_once __DIR__ . '/../LogAdapter.php';

use prelude\log\LogAdapter;
use prelude\log\adpaters\internal\NoOperationLog;

class NoOperationLogAdapter implements LogAdapter {
    private $log;

    function __construct($stream) {
        $this->log = new NoOperationLog();
    }
    
    function getLog($domain) {
        return $this->log;
    }
}
