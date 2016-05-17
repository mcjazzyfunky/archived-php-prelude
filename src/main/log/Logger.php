<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';
require_once __DIR__ . '/adapters/NoOperationLogAdapter.php';

use prelude\log\Log;
use prelude\log\adapters\NoOperationLogAdapter;

final class Logger {
    private static $adapter = null;
    private static $defaultLogLevel = Log::WARN;
    
    private function Logger() {
    }
    
    static function getLog($domain) {
        if (self::$adapter === null) {
            self::$adapter = new NoOperationLogAdapter();
        }
        
        return self::$adapter->getLog($domain);
    }
    
    static function setDefaultLogLevel($level) {
        self::$defaultLogLevel = $level;
    }
    
    static function getDefaultLogLevel() {
        return self::$defaultLogLevel; 
    }
    
    static function setAdapter(LogAdapter $adapter) {
        self::$adapter = $adapter;
    }
    
    static function getAdatper() {
        return self::$adapter;
    }
}
