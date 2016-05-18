<?php

namespace prelude\io;

require_once __DIR__ . '/../../../prelude.php';

use PHPUnit_Framework_TestCase;

class PathScannerTest extends PHPUnit_Framework_TestCase {
    function testMethodScan() {
        $arr =
            PathScanner::create()
                ->recursive()
                ->includeFiles(['*.php', '*.json'])
                ->excludeFiles('*tmp*')
                ->excludeLinks()
                //->forceAbsolute()
                ->sort(FileComparators::byFileSize())
                ->listPaths()
                ->scan('.')
                ->map(function ($file) {
                    return $file . " :: " . filesize($file);
                })
                ->toArray();

        print_r($arr);
    }
}
