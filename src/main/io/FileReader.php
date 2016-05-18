<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/Files.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use \Closure;
use InvalidArgumentException;
use prelude\util\Seq;

final class FileReader {
    private $openFile;

    private function __construct(Closure $openFile) {
        $this->openFile = $openFile;
    }

    function open() {
        $openFile = $this->openFile;
        return $openFile();
    }

    function readFull() {
        $ret = '';
        $stream = $this->open();

        try {        
            while (!feof($stream)) {
                $ret .= fread($stream, 8192);
            }
        } finally {        
            fclose($stream);
        }

        return $ret;
    }
    
    function readSeq() {
        $openFile = $this->openFile;
        
        return Seq::from(function() use ($openFile) {
            $stream = $openFile();
            
            try {
                while (($line = @fgets($stream)) !== false) {
                    $length = strlen($line);
                    
                    while ($length > 0
                        && ($line[$length - 1] === "\r" || $line[$length -1] === "\n")) {
                    
                        --$length;
                    }
                    
                    yield substr($line, 0, $length);
                }
                
                if (!feof($stream)) {
                    $message = error_get_last()['message'];
                    throw new IOException($message);
                }
            } finally {
                fclose($stream);
            }
        });
    }
    
    static function fromFile($file, $context = null) {
        $path = Files::getPath($file);
        
        $openFile = function () use ($path, $context) {
            return Files::openFile($path, 'rb', $context);
        };

        return new self($openFile);
    }
    
    static function fromString($text) {
        $openFile = function () use ($text) {
            $stream = fopen('php://memory', 'wr');
            fwrite($stream, $text);
            rewind($stream);
            return $stream;
        };
        
        return new self($openFile);
    }
}
