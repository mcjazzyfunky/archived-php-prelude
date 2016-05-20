<?php

namespace prelude\db;

interface DBDeleteQuery {
    function where($whereClause, $bindings);

    function execute();
}
