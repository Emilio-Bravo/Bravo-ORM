<?php

namespace Bravo\ORM;

/**
 * Handles the database query results
 */

class QueryHandler
{
    public function is_void($query): bool
    {
        return is_bool($query);
    }
}
