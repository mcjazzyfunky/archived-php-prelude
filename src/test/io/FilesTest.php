<?php

namespace prelude\io;

require_once __DIR__ . '/../../main/io/Files.php';

use prelude\util\Seq;

error_reporting(E_ALL);

class FilesTest extends \PHPUnit_Framework_TestCase {
    function testMethodListDir() {
        $arr = Files::listDir('.', [
            'recursive' => true,
            'types' => ['f'],
            'returnFilenames' => true,
            'fileSelector' => '*.php',
        ])
        ->toArray();
        
        print_r($arr);
    }
}
