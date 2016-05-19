<?php

namespace prelude\io;

require_once __DIR__ . '/../../../include.php';

use prelude\util\Seq;

class FileWriterTest extends \PHPUnit_Framework_TestCase {
    function testMethodWriteFull() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        FileWriter::fromFile($filename)
            ->writeFull('This is a test');
            
        $this->assertEquals(file_get_contents($filename), 'This is a test');

        FileWriter::fromFile($filename)
            ->append()
            ->writeFull('|This is a test');

        $this->assertEquals(
            file_get_contents($filename),
            'This is a test|This is a test');
    }
    
    function testMethodWriteLines() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        $result = FileWriter::fromFile($filename)
            ->writeSeq(Seq::range(1, 4), "\n");
            
        $this->assertEquals(file_get_contents($filename), "1\n2\n3\n");
        $this->assertEquals($result, 3);
    }
}
