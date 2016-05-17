<?php

namespace prelude\log;

interface Log {
    const TRACE = 0;
    const DEBUG = 1;
    const INFO = 2;
    const NOTICE = 3;
    const WARN = 4;
    const ERROR = 5;
    const CRITICAL = 6;
    const ALERT = 7;
    const FATAL = 8;
    
    function log($level, $message, $args = null, $data = null);
    
    function isEnabled($level);
    
    function trace($message, $args = null, $throwable = null, $data = null);
    
    function debug($message, $args = null, $throwable = null, $data = null);
    
    function info($message, $args = null, $throwable = null, $data = null);
    
    function notice($message, $args = null, $throwable = null, $data = null);  
    
    function warn($message, $args = null, $throwable = null, $data = null);
    
    function error($mssage, $args = null, $throwable = null, $data = null);
    
    function critical($message, $args = null, $throwable = null, $data = null);
    
    function alert($message, $args = null, $throwable = null, $data = null);
    
    function fatal($message = null, $args = null, $throwable = null, $data = null);

    function isTraceEnabled();

    function isDebugEnabled();

    function isInfoEnabled();

    function isNoticeEnabled();

    function isWarnEnabled();

    function isErrorEnabled();

    function isCriticalEnabled();

    function isAlertEnabled();

    function isFatalEnabled();
}
