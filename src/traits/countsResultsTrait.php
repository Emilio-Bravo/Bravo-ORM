<?php

namespace Bravo\ORM;

trait countsResults
{
    public function handleMany(\PDOStatement $stmt, int $fetch_type = \PDO::FETCH_OBJ): array
    {
        return (array) $stmt->fetchAll($fetch_type);
    }

    public function areMany(\PDOStatement $stmt): bool
    {
        return $stmt->rowCount() > 1;
    }

    public function handleOne(\PDOStatement $stmt, int $fetch_type)
    {
        return $stmt->fetch($fetch_type);
    }

    public function findCase(\PDOStatement $stmt, int $fetch_type)
    {
        if ($this->areMany($stmt)) return $this->handleMany($stmt, $fetch_type);
        return $this->handleOne($stmt, $fetch_type);
    }
}
