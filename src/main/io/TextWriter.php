<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/Files.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use InvalidArgumentException;
use prelude\util\Seq;

final class TextWriter {
    private $filename;
    private $context;
    
    private function __construct($filename, array $context = null, &$targetString = null) {
        $this->filename = $filename;
        $this->context = $context;
    }
    
    function writeFull($text) {
        if (!is_string($text)) {
            throw new InvalidArgumentException(
                '[FileWriter#writeFull] First argument $text must be a string');
        }
        
        $this->write(function ($fhandle) use ($text) {
            $length = strlen($text);
            $result = fwrite($fhandle, $text, $length);

            if ($result === false || $result !== $length) {
                $message = error_get_last()['message'];
                throw new IOException($message);
            }
            
            return $length;
        });
        
    }
    
    function writeSeq(Seq $seq, $separator = "\n") {
        return $this->write(function ($fhandle) use ($seq, $separator) {
            $itemCount = 0;
            
            foreach ($seq as $item) {
                ++$itemCount;
                
                foreach ([$item, $separator] as $s) {
                    $result = fwrite($fhandle, $s);
                
                    if ($result === false) {
                        $message = error_get_last()['message'];
                        throw new IOException($message);
                    }
                }
            }
            
            return $itemCount;
        });
        
        return;
    }
    
    function write(callable $action) {
        $ret = null;
        $filename = $this->filename;
        $context = $this->context;
                
        $fhandle = $context === null
            ? @fopen(
                $filename,
                'wb',
                false)
            : @fopen(
                $filename,
                'wb',
                false,
                $context);
        
        if ($fhandle === false) {
            $message = error_get_last()['message'];
            throw new IOException($message);
        }

        try {
            $ret = $action($fhandle);
        } finally {
            @fclose($fhandle);
        }
        
        return $ret;
    }
    
    static function fromFile($file, array $context = null) {
         if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[FileWriter.fromFile] First argument $file must either '
                . 'be a string or a File object');
        }
        
        $filename = is_string($file) ? $file : $file->getPath();

        return new self($filename, $context);
    }
}
