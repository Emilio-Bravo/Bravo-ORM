<?php

namespace Bravo\ORM;

/**
 * Handles the database query results
 */

class QueryHandler
{
    public function is_void($query)
    {
        return is_bool($query);
    }
}
