<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/NoOperationLog.php';
require_once __DIR__ . '/../LoggerAdapter.php';

use prelude\log\LoggerAdapter;
use prelude\log\adapters\internal\NoOperationLog;

class NoOperationLoggerAdapter implements LoggerAdapter {
    private $log;

    function __construct() {
        $this->log = new NoOperationLog();
    }
    
    function getLog($domain) {
        return $this->log;
    }

    function getLogLevelByDomain($domain) {
        return LOG::NONE;
    }
}
