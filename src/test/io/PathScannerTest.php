<?php

namespace prelude\io;

require_once __DIR__ . '/../../main/io/PathScanner.php';

error_reporting(E_ALL);

use PHPUnit_Framework_TestCase;

class PathScannerTest extends PHPUnit_Framework_TestCase {
    function testMethodScan() {
        $arr =
            PathScanner::create()
                ->recursive()
                ->includeFiles(['*.php', '*.js', '*.css'])
                ->excludeFiles('*tmp*')
                ->excludeLinks()
                ->absolutePaths()
                ->listPaths()
                ->scan('.')
                ->toArray();

        print_r($arr);
        flush();
        exit();
    }
}
