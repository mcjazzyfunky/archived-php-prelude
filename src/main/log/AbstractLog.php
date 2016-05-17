<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';

abstract class AbstractLog implements Log {
    abstract function log($level, $message, $args = null, $throwable = null, $data = null);
    
    abstract function isEnabled($level);
    
    function trace($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::TRACE, $message, $args, $data);   
    }
    
    function debug($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::DEBUG, $message, $args, $data, $throwable);   
    }
    
    function info($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::INFO, $message, $args, $data, $throwable);   
    }
    
    function notice($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::TRACE, $message, $args, $data, $throwable);   
    }
    
    function warn($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::WARN, $message, $args, $data, $throwable);
    }
    
    function error($mssage, $args = null, $throwable = null, $data = null) {
        $this->log(Log::ERROR, $message, $args, $data, $throwable);
    }
    
    function critical($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::CRITICAL, $message, $args, $data, $throwable);
    }
    
    function alert($message, $args = null, $throwable = null, $data = null) {
        $this->log(Log::ALERT, $message, $args, $data);
    }
    
    function fatal($message = null, $args = null, $throwable = null, $data = null) {
        $this->log(Log::FATAL, $message, $args, $data, $throwable);
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
