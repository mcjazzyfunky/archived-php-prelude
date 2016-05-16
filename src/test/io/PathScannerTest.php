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
                ->includeFiles(['*.php', '*.json'])
                ->excludeFiles('*tmp*')
                ->excludeLinks()
                ->forceAbsolute()
                ->listPaths()
                ->scan('.')
                ->toArray();

        print_r($arr);
    }
}
