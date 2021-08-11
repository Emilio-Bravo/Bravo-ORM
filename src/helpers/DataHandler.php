<?php

namespace Bravo\ORM;

use PDO;

class DataHandler
{

    use countsResults;

    /**
     * The query PDOStatement
     * 
     * @var \PDOStatement
     */
    protected \PDOStatement $stmt;

    /**
     * Create a new Data Handler
     * 
     * @param \PDOStatement $stmt
     * @return void
     */
    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * Fetch the query results into an object
     * 
     * @param bool $find Wheter the query is for an specific row
     * @return array|object
     */
    public function obj(bool $find = false): array|object
    {
        if ($find) {
            return $this->findCase($this->stmt, PDO::FETCH_OBJ);
        }

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetch the query results into a number
     * 
     * @return array|int
     */
    public function num(): array|int
    {
        return $this->stmt->fetch(PDO::FETCH_NUM);
    }

    /**
     * Fetch the query results into an array
     * 
     * @param bool $find Wheter the query is for an specific row
     * @return array
     */
    public function assoc(bool $find = false): array
    {
        if ($find) {
            return $this->findCase($this->stmt, PDO::FETCH_ASSOC);
        }

        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of rows of the query results
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Fetch the data into a JSON object
     * 
     * @return string|false
     */
    public function json(): string|false
    {
        return json_encode($this->assoc());
    }

    /**
     * Serialize the query results 
     * 
     * @return string
     */
    public function serialize()
    {
        return $this->stmt->fetch(PDO::FETCH_SERIALIZE);
    }
}
