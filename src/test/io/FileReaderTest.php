<?php

namespace prelude\io;

require_once __DIR__ . '/../../../include.php';

use prelude\util\Seq;

class FileReaderTest extends \PHPUnit_Framework_TestCase {
    function testMethodReadreadFully() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        file_put_contents($filename, "a\nb\nc");
        
        $content =
            FileReader::fromFile($filename)
                ->readreadFully();
        
        $this->assertEquals($content, "a\nb\nc");
        
        
        $content =
            FileReader::fromString('this is a test')
                ->readreadFully();
                
        $this->assertEquals($content, 'this is a test');
    }
    
    function testMethodReadLines() {
        return;
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        file_put_contents($filename, "a\r\nb\r\nc");
        
        $lines = FileReader::fromFile($filename)
            ->readSeq()
            ->toArray();

        $this->assertEquals($lines, ['a', 'b', 'c']);
    }
}
