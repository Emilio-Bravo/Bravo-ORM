<?php

namespace Bravo\ORM;

trait verifyiesData
{
    /**
     * Verifies if a valid database connection exists
     * @return bool
     */

    public function is_connected(): bool
    {
        return $this->connection instanceof \PDO;
    }

    /**
     * Deternmines wether an array key is associative or not
     * @return bool
     */

    public function is_assoc(array $array): bool
    {
        return !is_int(key($array));
    }
}
