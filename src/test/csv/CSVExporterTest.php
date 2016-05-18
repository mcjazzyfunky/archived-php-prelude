<?php

namespace prelude\csv;

require_once __DIR__ . '/../../main/csv/CSVExporter.php';
require_once __DIR__ . '/../../main/csv/CSVFormat.php';
require_once __DIR__ . '/../../main/io/FileWriter.php';
require_once __DIR__ . '/../../main/util/Seq.php';

use Exception;
use PHPUnit_Framework_TestCase;
use prelude\util\Seq;
use prelude\io\FileWriter;

error_reporting(E_ALL);

class CSVExporterTest extends PHPUnit_Framework_TestCase {
    function testRun() {
        $recs = [
            ['LAST_NAME' => 'Doe',
             'FIRST_NAME' => 'John',
             'Seattle',
             'USA'],
            ['FIRST_NAME' => 'Jane',
             'LAST_NAME' => 'Whoever',
             'CITY' => 'London',
             'COUNTRY' => 'USA'],
            ['Jim', 'Gym', 'Sidney', 'Australia', 'This field will not be exported']
        ];
        
        $format =
            CSVFormat::create()
                ->columns(['FIRST_NAME', 'LAST_NAME', 'CITY', 'COUNTRY'])
                ->suppressHeader(false)
                ->delimiter(';')
                ->quoteChar('"');

        $exporter =
            CSVExporter::create()
                ->format($format)
                ->mapper(function ($rec,$idx) {
                    return Seq::from([$rec, $rec]);
                })
                ->export(
                    FileWriter::fromFile('php://stdout'),
                    Seq::from($recs));
                
        flush();
    }
}
