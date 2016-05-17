<?php

namespace prelude\log;

require_once __DIR__ . '/Log.php';

use Exception;
use InvalidArgumentException;
use Throwable;

abstract class AbstractLog implements Log {
    abstract function log($level, $message, $args = null, $throwable = null, $data = null);
    
    abstract function isEnabled($level);
    
    function trace($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#trace] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }
        
        $this->log(Log::TRACE, $message, $args, $data);   
    }
    
    function debug($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#debug] Third argument must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::DEBUG, $message, $args, $throwable, $data);   
    }
    
    function info($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#info] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }
        
        $this->log(Log::INFO, $message, $args, $throwable, $data);   
    }
    
    function notice($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#notice] Third argument must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::TRACE, $message, $args, $throwable, $data);   
    }
    
    function warn($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#warn] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::WARN, $message, $args, $throwable, $data);
    }
    
    function error($mssage, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#error] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::ERROR, $message, $args, $throwable, $data);
    }
    
    function critical($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#critical] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::CRITICAL, $message, $args, $throwable, $data);
    }
    
    function alert($message, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#alert] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::ALERT, $message, $args, $data);
    }
    
    function fatal($message = null, $args = null, $throwable = null, $data = null) {
        if ($throwable !== null
            && !($throwable instanceof Exception) 
            && !($throwable instanceof Throwable)) {
         
            throw new InvalidArgumentException(
                '[AbstractLog#fatal] Third argument $throwable must be a '
                . 'Throwable/Exception or null');
        }

        $this->log(Log::FATAL, $message, $args, $throwable, $data);
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
