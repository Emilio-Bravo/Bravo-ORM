<?php

namespace Bravo\ORM;

trait verifyiesData
{
    /**
     * Verifies if a valid database connection exists
     * 
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connection instanceof \PDO;
    }

    /**
     * Deternmines wether an array key is associative or not
     * 
     * @param array $array
     * @return bool
     */
    public function isAssoc(array $array): bool
    {
        return !\is_int(key($array));
    }
}
