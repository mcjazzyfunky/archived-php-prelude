<?php

namespace prelude\log;

use Throwable;

interface Log {
    const TRACE = 1;
    const DEBUG = 2;
    const INFO = 3;
    const NOTICE = 4;
    const WARN = 5;
    const ERROR = 6;
    const CRITICAL = 7;
    const ALERT = 8;
    const FATAL = 9;
    const NONE = 10;
    
    function log($level, $message, $args = null, $cause, $extra = null);
    
    function isEnabled($level);
    
    function trace($message, $args = null, $cause = null, $extra = null);
    
    function debug($message, $args = null, $cause = null, $extra = null);
    
    function info($message, $args = null, $cause = null, $extra = null);
    
    function notice($message, $args = null, $cause = null, $extra = null);  
    
    function warn($message, $args = null, $cause = null, $extra = null);
    
    function error($mssage, $args = null, $cause = null, $extra = null);
    
    function critical($message, $args = null, $cause = null, $extra = null);
    
    function alert($message, $args = null, $cause = null, $extra = null);
    
    function fatal($message = null, $args = null, $cause = null, $extra = null);

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
