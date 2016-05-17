<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';

use InvalidArgumentException;

final class LogUtils {
    private function __construct() {
    }
    
    private static $logLevelNames = [
        Log::TRACE => 'TRACE',
        Log::DEBUG => 'DEBUG',
        Log::INFO => 'INFO',
        Log::NOTICE => 'NOTICE',
        Log::WARN => 'WARN',
        Log::ERROR => 'ERROR',
        Log::CRITICAL => 'CRITICAL',
        Log::ALERT => 'ALERT',
        Log::FATAL => 'FATAL'
    ];
    
    static function isValidLogLevel($level) {
        return $level >= Log::TRACE && $level <= Log::FATAL;
    }
    
    static function getLogLevelName($level) {
        return self::$logLevelNames[$level];
    }
    
    static function formatLogMessage($message, $args = null) {
        if (!is_string($message)) {
            throw new InvalidArgumentException(
                '[LogUtils::formatLogText] First argument $message must be '
                . 'a string');
        } else if ($args !== null && !is_scalar($args) && !is_array($args)) {
            throw new InvalidArgumentException(
                '[LogUtils::formatLogText] Second argument $args must either '
                . 'be a scalar or an array or null');
        }
        
        $ret = null;
        
        if ($args === null) {
            $ret = $message;    
        } else {
            if (is_scalar($args)) {
                $ret = sprintf($message, $args);
            } else if (is_array($args)) {
                $parms = $args;
                array_unshift($params, $message);
                $ret = call_user_func_array('sprintf', $params);
            }
        }
        
        return $ret;
    }
}
