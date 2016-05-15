<?php

namespace prelude\db;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../util/Seq.php';
require_once __DIR__ . '/../util/ValueObject.php';

use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;

interface DBQuery {
    function bind($params);

    function limit($n);

    function offset($n);

    function execute();

    function fetchSingle();

    function fetchRow();

    function fetchRec();

    function fetchVO();

    function fetchSingles();

    function fetchRows();

    function fetchRecs();

    function fetchMap();

    function fetchSeqOfSingles();

    function fetchSeqOfRows();

    function fetchSeqOfRecs();

    function fetchSeqOfVOs();
}
