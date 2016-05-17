<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';
require_once __DIR__ . '/adapters/NoOperationLoggerAdapter.php';

use InvalidArgumentException;
use prelude\log\Log;
use prelude\log\adapters\NoOperationLoggerAdapter;

final class Logger {
    private static $adapter = null;
    private static $defaultLogLevel = Log::NONE;
    
    private function Logger() {
    }
    
    static function getLog($domain) {
        $isObject = is_object($domain);
        $isString = is_string($domain);
        
        if (!$isObject && !$isString) {
            throw new InvalidArgumentException(
                '[Logger.getLog] First argument $domain must not be '
                . 'a string or an object');
        } else if ($isString && trim($domain) === '') {
            throw new InvalidArgumentException(
                '[Logger.getLog] First argument $domain must not be '
                . 'a blank string');
        }
        
        if (self::$adapter === null) {
            self::$adapter = new NoOperationLoggerAdapter();
        }
        
        $domainName =
            is_object($domain)
            ? get_class($domain)
            : $domain;
        
        return self::$adapter->getLog($domainName);
    }
    
    static function setDefaultLogLevel($level) {
        self::$defaultLogLevel = $level;
    }
    
    static function getDefaultLogLevel() {
        return self::$defaultLogLevel; 
    }
    
    static function getLogLevelByDomain($domain) {
        return
            self::adapter === null
            ? Log::NONE
            : self::$adapter->getLogLevelByDomain($domain);
    }
    
    static function setAdapter(LoggerAdapter $adapter) {
        self::$adapter = $adapter;
    }
    
    static function getAdatper() {
        return self::$adapter;
    }
}
