<?php

namespace prelude\db\internal\adapters;

require_once __DIR__ . '/../AbstractPDOAdapter.php';
require_once __DIR__ . '/../DBUtils.php';

use prelude\db\internal\AbstractPDOAdapter;
use prelude\db\internal\DBUtils;

class PDOSQLiteAdapter extends AbstractPDOAdapter {
    function limitQuery($query, $limit = null, $offset = 0) {
        return DBUtils::limitQueryByLimitClause($query, $limit, $offset);        
    }
}
