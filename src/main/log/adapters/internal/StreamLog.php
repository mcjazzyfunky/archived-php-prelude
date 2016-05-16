<?php

namespace prelude\log\adapters\internal;

require_once __DIR__ . '/../../AbstractLog.php';
require_once __DIR__ . '/../../Logger.php';
require_once __DIR__ . '/../../LogUtils.php';

use InvalidArgumentException;
use prelude\log\AbstractLog;
use prelude\log\Logger;
use prelude\log\LogUtils;

class StreamLog extends AbstractLog {
    private $stream;

    function __construct($stream) {
        $this->stream = $stream;
    }
    
    function log($level, $message, $args = null, $ctx = null) {
        if (!LogUtils::isValidLogLevel($level)) {
            throw new InvalidArgumentException(
                '[StreamLog::log] First argument $level must be a '
                . 'valid log level');
        }
        
        if ($this->isEnabled($level)) {
            $date = date ('Y-m-d H:i:s');
            $levelName = LogUtils::getLogLevelName($level);
            $text = LogUtils::formatLogMessage($message, $args); 
    
            $output = "[$date] [$levelName] $text\n";
            fputs($this->stream, $output);
            fflush($this->stream);
        }
    }
    
    function isEnabled($level) {
        return $level >= Logger::getDefaultLogLevel();
    }
}
