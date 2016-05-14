<?php

namespace prelude\io;

require_once __DIR__ . '/../../main/util/Seq.php';
require_once __DIR__ . '/../../main/io/File.php';
require_once __DIR__ . '/../../main/io/TextReader.php';

use prelude\util\Seq;

error_reporting(E_ALL);

class TextReaderTest extends \PHPUnit_Framework_TestCase {
    function testMethodWriteFull() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        file_put_contents($filename, "a\nb\nc");
        
        $content =
            TextReader::fromFile($filename)
                ->readFull();
        
        $this->assertEquals($content, "a\nb\nc");
    }
    
    function testMethodReadLines() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        file_put_contents($filename, "a\r\nb\r\nc");
        
        $lines = TextReader::fromFile($filename)
            ->readLines()
            ->toArray();

        $this->assertEquals($lines, ['a', 'b', 'c']);
    }
}
