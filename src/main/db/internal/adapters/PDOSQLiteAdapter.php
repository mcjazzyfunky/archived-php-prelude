<?php

namespace prelude\db\internal\adapters;

use prelude\db\internal\AbstractPDOAdapter;
use prelude\db\internal\DBUtils;

class PDOSQLiteAdapter extends AbstractPDOAdapter {
    function limitQuery($query, $limit = null, $offset = 0) {
        return DBUtils::limitQueryByLimitClause($query, $limit, $offset);        
    }
}
