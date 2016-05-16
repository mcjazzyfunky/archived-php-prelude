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
    
    function log($level, $message, $args = null, $ctx = null);
    
    function isEnabled($level);
    
    function trace($message, $args = null, $ctx = null);
    
    function debug($message, $args = null, $ctx = null);
    
    function info($message, $args = null, $ctx = null);
    
    function notice($message, $args = null, $ctx = null);  
    
    function warn($message, $args = null, $ctx = null);
    
    function error($mssage, $args = null, $ctx = null);
    
    function critical($message, $args = null, $ctx = null);
    
    function alert($message, $args = null, $ctx = null);
    
    function fatal($message = null, $args = null, $ctx = null);

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
