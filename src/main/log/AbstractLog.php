<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';

abstract class AbstractLog implements Log {
    abstract function log($level, $message, $args = null, $ctx = null);
    
    abstract function isEnabled($level);
    
    function trace($message, $args = null, $ctx = null) {
        $this->log(Log::TRACE, $message, $args, $ctx);   
    }
    
    function debug($message, $args = null, $ctx = null) {
        $this->log(Log::DEBUG, $message, $args, $ctx);   
    }
    
    function info($message, $args = null, $ctx = null) {
        $this->log(Log::INFO, $message, $args, $ctx);   
    }
    
    function notice($message, $args = null, $ctx = null) {
        $this->log(Log::TRACE, $message, $args, $ctx);   
    }
    
    function warn($message, $args = null, $ctx = null) {
        $this->log(Log::WARN, $message, $args, $ctx);
    }
    
    function error($mssage, $args = null, $ctx = null) {
        $this->log(Log::ERROR, $message, $args, $ctx);
    }
    
    function critical($message, $args = null, $ctx = null) {
        $this->log(Log::CRITICAL, $message, $args, $ctx);
    }
    
    function alert($message, $args = null, $ctx = null) {
        $this->log(Log::ALERT, $message, $args, $ctx);
    }
    
    function fatal($message = null, $args = null, $ctx = null) {
        $this->log(Log::FATAL, $message, $args, $ctx);
    }

    function isTraceEnabled() {
        return $this->isEnabled(Log::TRACE);   
    }

    function isDebugEnabled() {
        return $this->isEnabled(Log::DEBUG);
    }

    function isInfoEnabled() {
        return $this->isEnabled(Log::INFO);
    }

    function isNoticeEnabled() {
        return $this->isEnabled(Log::NOTICE);
    }

    function isWarnEnabled() {
        return $this->isEnabled(Log::WARN);
    }

    function isErrorEnabled() {
        return $this->isEnabled(Log::ERROR);
    }

    function isCriticalEnabled() {
        return $this->isEnabled(Log::CRITICAL);
    }

    function isAlertEnabled() {
        return $this->isEnabled(Log::ALERT);
    }

    function isFatalEnabled() {
        return $this->isEnabled(Log::FATAL);
    }
}
