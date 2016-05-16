<?php

namespace prelude\log\internal;

require_once __DIR__ . '/../../AbstractLog.php';
require_once __DIR__ . '/../../LogUtils.php';

use InvalidArgumentException;
use prelude\log\AbstractLog;
use prelude\log\LogUtils;

class NoOperationLog extends AbstractLog {
    function log($level, $message, $args = null, $ctx = null) {
        if (!LogUtils::isValidLogLevel($level)) {
            throw new InvalidArgumentException(
                '[NoOperationLog::log] First argument $level must be a '
                . 'valid log level');
        }
    }
    
    function isEnabled($level) {
        return false;
    }
}
