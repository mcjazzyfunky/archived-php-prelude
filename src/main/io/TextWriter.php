<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/Files.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use Closure;
use InvalidArgumentException;
use prelude\util\Seq;

final class TextWriter {
    private $openFile;
    
    private function __construct(Closure $openFile) {
        $this->openFile = $openFile;
    }
    
    function open() {
        $openFile = $this->openFile;
        return $openFile();
    }
    
    function writeFull($text) {
        if (!is_string($text)) {
            throw new InvalidArgumentException(
                '[FileWriter#writeFull] First argument $text must be a string');
        }
        
        $length = strlen($text);
        $stream = $this->open();
        
        try {
            $result = fwrite($stream, $text, $length);
    
            if ($result === false || $result !== $length) {
                $message = error_get_last()['message'];
                throw new IOException($message);
            }
        } finally {
            fclose($stream);
        }
        
        return $length;
    }
    
    function writeSeq(Seq $seq, $separator = "\n") {
        $itemCount = 0;
        $stream = $this->open();
        
        try {
            foreach ($seq as $item) {
                ++$itemCount;
                
                foreach ([$item, $separator] as $s) {
                    $result = fwrite($stream, $s);
                
                    if ($result === false) {
                        $message = error_get_last()['message'];
                        throw new IOException($message);
                    }
                }
            }
        } finally {
            fclose($stream);
        }
        
        return $itemCount;
    }
    
    static function fromFile($file, array $context = null) {
        $path = Files::getPath($file);
        
        $openFile = function () use ($path, $context) {
            return Files::openFile($path, 'wb', $context);
        };
        
        return new self($openFile);
    }
}
