<?php

namespace prelude\log\adapters\internal;

require_once __DIR__ . '/../../AbstractLog.php';
require_once __DIR__ . '/../../Logger.php';
require_once __DIR__ . '/../../LogUtils.php';

use InvalidArgumentException;
use Throwable;
use prelude\log\AbstractLog;
use prelude\log\Logger;
use prelude\log\LogUtils;

class StreamLog extends AbstractLog {
    private $stream;

    function __construct($stream, $domain) {
        $this->stream = $stream;
        $this->domain = $domain;
    }
    
    function log($level, $message, $args = null, $throwable = null, $data = null) {
        if (!LogUtils::isValidLogLevel($level)) {
            throw new InvalidArgumentException(
                '[StreamLog::log] First argument $level must be a '
                . 'valid log level');
        }
        
        if ($this->isEnabled($level)) {
            $date = date ('Y-m-d H:i:s');
            $levelName = LogUtils::getLogLevelName($level);
            $text = LogUtils::formatLogMessage($message, $args); 
            $domain = $this->domain;
            $output = "[$date] [$levelName] [$domain] $text\n";
            
            if ($data !== null) {
                $output .= "---- Data ----\n";
                $output .= print_r($data, true);
            }
            
            if ($throwable !== null) {
                $output .= "---- Cause ----\n";
                $output .= 'Message: ';
                $output .= $throwable->getMessage();
                $output .= "\nCode: ";
                $output .= $throwable->getCode();
                $output .= "\nStack trace:\n";
                $output .= $throwable->getTraceAsString();
                $output .= "\n";
            }
            
            fputs($this->stream, $output);
            fflush($this->stream);
        }
    }
    
    function isEnabled($level) {
        return $level >= Logger::getDefaultLogLevel();
    }
}
