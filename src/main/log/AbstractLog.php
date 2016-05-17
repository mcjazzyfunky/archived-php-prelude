<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';

use Exception;
use InvalidArgumentException;
use Throwable;

abstract class AbstractLog implements Log {
    abstract function log($level, $message, $args = null, $cause = null, $extra = null);
    
    abstract function isEnabled($level);
    
    function trace($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#trace] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }
        
        $this->log(Log::TRACE, $message, $args, $extra);   
    }
    
    function debug($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#debug] Third argument must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::DEBUG, $message, $args, $cause, $extra);   
    }
    
    function info($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#info] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }
        
        $this->log(Log::INFO, $message, $args, $cause, $extra);   
    }
    
    function notice($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#notice] Third argument must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::TRACE, $message, $args, $cause, $extra);   
    }
    
    function warn($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#warn] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::WARN, $message, $args, $cause, $extra);
    }
    
    function error($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#error] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::ERROR, $message, $args, $cause, $extra);
    }
    
    function critical($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#critical] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::CRITICAL, $message, $args, $cause, $extra);
    }
    
    function alert($message, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#alert] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::ALERT, $message, $args, $extra);
    }
    
    function fatal($message = null, $args = null, $cause = null, $extra = null) {
        if ($cause !== null
            && !($cause instanceof Exception) 
            && !($cause instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#fatal] Third argument $cause must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::FATAL, $message, $args, $cause, $extra);
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
