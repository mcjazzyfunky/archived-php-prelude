<?php

namespace prelude\db;

use Exception;
use InvalidArgumentException;

class DBException extends Exception {
    function __construct($message) {
        if (!is_string($message)) {
            throw new InvalidArgumentException(
                '[DBException.__construct] First argument $message must be a string');
        }
        
        parent::__construct($message);
    }
}