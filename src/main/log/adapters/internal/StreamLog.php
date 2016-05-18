<?php

namespace prelude\log\adapters\internal;

require_once __DIR__ . '/../../AbstractLog.php';
require_once __DIR__ . '/../../Log.php';
require_once __DIR__ . '/../../Logger.php';
require_once __DIR__ . '/../../LogUtils.php';

use InvalidArgumentException;
use Throwable;
use prelude\log\AbstractLog;
use prelude\log\Log;
use prelude\log\Logger;
use prelude\log\LogUtils;

class StreamLog extends AbstractLog {
    private $stream;

    function __construct($stream, $domain) {
        $this->stream = $stream;
        $this->domain = $domain;
    }
    
    function log($level, $message, $args = null, $cause = null, $extra = null) {
        if ($level !== LOG::NONE) {
            if (!LogUtils::isValidLogLevel($level, true)) {
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
                
                if ($extra !== null) {
                    $output .= "---- Extra ----\n";
                    $output .= trim(print_r($extra, true));
                }
                
                if ($cause !== null) {
                    $output .= "\n---- Cause ----";
                    $output .= "\nClass: ";
                    $output .= get_class($cause);
                    $output .= "\nMessage: ";
                    $output .= $cause->getMessage();
                    $output .= "\nCode: ";
                    $output .= $cause->getCode();
                    $output .= "\nFile: ";
                    $output .= $cause->getFile();
                    $output .= "\nLine: ";
                    $output .= $cause->getLine();
                    $output .= "\nStack trace:\n";
                    $output .= $cause->getTraceAsString();
                    $output .= "\n";
                }
                
                fputs($this->stream, $output);
                fflush($this->stream);
            }
        }
    }
    
    function isEnabled($level) {
        return $level >= Logger::getDefaultLogLevel();
    }
}
