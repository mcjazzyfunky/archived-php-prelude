<?php

namespace prelude\io;

require_once __DIR__ . '/../../main/util/Seq.php';
require_once __DIR__ . '/../../main/io/TextWriter.php';

use prelude\util\Seq;

error_reporting(E_ALL);

class TextWriterTest extends \PHPUnit_Framework_TestCase {
    function testMethodWriteFull() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        TextWriter::fromFile($filename)
            ->writeFull('This is a test');
            
        $this->assertEquals(file_get_contents($filename), 'This is a test');
    }
    
    function testMethodWriteLines() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        $result = TextWriter::fromFile($filename)
            ->writeSeq(Seq::range(1, 4), "\n");
            
        $this->assertEquals(file_get_contents($filename), "1\n2\n3\n");
        $this->assertEquals($result, 3);
    }
}
