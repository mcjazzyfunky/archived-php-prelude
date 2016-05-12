<?php

namespace prelude\io;

require_once(__DIR__ . '/../../main/util/Seq.php');
require_once(__DIR__ . '/../../main/io/FileWriter.php');

use prelude\util\Seq;

error_reporting(E_ALL);

class FileWriterTest extends \PHPUnit_Framework_TestCase {
    function testMethodWriteFullText() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        FileWriter::forFile($filename)
            ->writeFullText('This is a test');
            
        $this->assertEquals(file_get_contents($filename), 'This is a test');
    }
    
    function testMethodWriteLines() {
        $filename = tempnam(sys_get_temp_dir(), 'txt');
        
        FileWriter::forFile($filename)
            ->writeLines(Seq::range(1, 4), "\n");
            
        $this->assertEquals(file_get_contents($filename), "1\n2\n3\n");
    }
}
