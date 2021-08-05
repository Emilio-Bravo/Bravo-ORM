<?php

namespace Bravo\ORM;

use PDOStatement;

trait countsResults
{
    /**
     * Handles a set of results
     * 
     * @param \PDOStatement $stmt
     * @param int $fetchType
     * @return array
     */
    public function handleMany(PDOStatement $stmt, int $fetchType = \PDO::FETCH_OBJ): array
    {
        return $stmt->fetchAll($fetchType);
    }

    /**
     * Wheter the number of results are more tha one
     * 
     * @param \PDOStatement $stmt
     * @return bool
     */
    public function areMany(PDOStatement $stmt): bool
    {
        return $stmt->rowCount() > 1;
    }

    /**
     * Handle one result 
     * 
     * @param \PDOStatement $stmt
     * @param int $fetchType
     * @return mixed
     */
    public function handleOne(PDOStatement $stmt, int $fetchType)
    {
        return $stmt->fetch($fetchType);
    }

    /**
     * Wheter the query is a find context
     * 
     * @param \PDOStatement $stmt
     * @param int $fetchType
     * @return mixed
     */
    public function findCase(PDOStatement $stmt, int $fetchType)
    {
        if ($this->areMany($stmt)) {
            return $this->handleMany($stmt, $fetchType);
        }

        return $this->handleOne($stmt, $fetchType);
    }
}
