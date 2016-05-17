<?php

namespace prelude\log\adapters;

require_once __DIR__ . '/internal/StreamLog.php';
require_once __DIR__ . '/StreamLoggerAdapter.php';

use Exception;
use prelude\log\adapters\StreamLoggerAdapter;

class FileLoggerAdapter extends StreamLoggerAdapter {
    function __construct($path) {
        $logFilePath = str_replace('{date}', date('Y-m-d'), $path);
        $stream = @fopen($logFilePath, 'a') ?: null;
        
        if ($stream === null) {
            $error = error_get_last()['message'];
            
            // TODO - is this really good idea?
            throw new Exception(
                "Could not open log file ('$logFilePath'): $error");
        }
        
        parent::__construct($stream);
    }
    
    function __destruct() {
        @fclose($this->getStream());
    }
}
