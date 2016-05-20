<?php

namespace prelude\db;

interface DBInsertQuery {
    function values(array $values);

    function execute();
}
