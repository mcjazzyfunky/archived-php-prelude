<?php

namespace prelude\db;

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
