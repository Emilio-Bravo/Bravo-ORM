<?php

namespace Bravo\ORM;

class ORM
{
    /**
     * The current query object
     * 
     * @var Bravo\ORM\Query
     */
    protected Query $query;

    /**
     * Wheter the query has a pending where statement 
     * 
     * @var bool
     */
    protected bool $hasPendingWhere = false;

    /**
     * The current query table
     * 
     * @var string
     */
    protected string $table;

    /**
     * Create a new ORM instance
     * 
     * @param string $table
     * @return void
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->query = new Query($this->table);
    }

    /**
     * Terminates the query 
     * 
     * @return self|DataHandler
     */
    public function __destruct()
    {
        if ($this->hasPendingWhere) {
            return $this->query->execute()->obj();
        }

        return $this;
    }

    /**
     * Perform an UPDATE statement
     * 
     * @param string $values
     * @return self
     */
    public function update(array $values): self
    {
        $this->hasPendingWhere = true;
        $this->query->update($values);

        return $this;
    }

    /**
     * Perform a WHERE statement
     * 
     * @param array $columns_values ['name' => 'Example']
     * @param string $operator The comparison operator for the query
     * @return self
     */
    public function where(array $columns_values, string $operator = '='): self
    {
        $this->query->where($columns_values, $operator);

        return $this;
    }

    /**
     * Perform a DELETE statement
     * 
     * @param array $keys ['id' => 2]
     * @param string $operator The comparison operator for the query
     * @return self
     */
    public function delete(array $keys, string $operator = '='): self
    {
        $this->query->delete()->where($keys, $operator)->execute();

        return $this;
    }

    /**
     * Perform a SELECT statement with a limit of results
     * 
     * @param int $limit
     * @return array
     */
    public function index(int $limit = 10): array
    {
        return $this->query->select()->limit($limit)->execute()->obj();
    }

    /**
     * Select all the rows of the current table
     * 
     * @return self
     */
    public function get(): self
    {
        $this->query->select();

        return $this;
    }

    /**
     * Perform an INSERT statement
     * 
     * @param array $values
     * @return self
     */
    public function insert(array $values): self
    {
        $this->query->insert($values)->execute();

        return $this;
    }

    /**
     * Select and order the rows of the current table
     * 
     * @param string $order
     * @param string $key 
     * @return array
     */
    public function all(string $order = 'asc', string $key = 'id'): array
    {
        return $this->query
            ->select()
            ->orderBy($key, $order)
            ->execute()
            ->obj();
    }

    /**
     * Find an specific row in the current table
     * 
     * @param array $columns_values ['email' => 'example@example.com'] 
     * @param string $operator The comparison operator for the query
     * @return object
     */
    public function find(array $columns_values, string $operator = '='): object
    {
        return $this->query
            ->find($columns_values, $operator)
            ->execute()
            ->obj(true);
    }

    /**
     * Find all rows that accomplish with the criteria
     * 
     * @param array $columns_values ['name' => 'example']
     * @param string $operator The comparison operator for the query
     * @return array
     */
    public function findAll(array $columns_values, string $operator = '='): array
    {
        return $this->query
            ->find($columns_values, $operator)
            ->execute()
            ->obj();
    }

    /**
     * Try to find a row if at least one specification of the criteria is met
     * 
     * @param array $columns_values ['name' => 'example', 'id' => 3]
     * @param string $operator The comparison operator for the query
     * @return object
     */
    public function findOrFail(array $columns_values, string $operator = '='): object
    {
        return $this->query
            ->findOrFail($columns_values, $operator)
            ->execute()
            ->obj(true);
    }

    /**
     * Try to find one or more rows if at least one specification of the criteria is met
     * 
     * @param array $columns_values ['name' => 'example', 'id' => 3]
     * @param string $operator The comparison operator for the query
     * @return array
     */
    public function findAllOrFail(array $columns_values, string $operator = '='): array
    {
        return $this->query
            ->findOrFail($columns_values, $operator)
            ->execute()
            ->obj();
    }

    /**
     * Select and order the rows of the current table
     * 
     * @param string $order
     * @param string $key 
     * @return array
     */
    public function orderBy($key = 'id', $order = 'asc', $limit = 10): array
    {
        return $this->query
            ->select()
            ->orderBy($key, $order)
            ->limit($limit)
            ->execute()
            ->obj();
    }

    /**
     * Set the current query table
     *
     * @param string $table The current query table
     *
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the current query object
     *
     * @return Bravo\ORM\Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * Get wheter the query has a pending where statement
     *
     * @return bool
     */
    public function getHasPendingWhere(): bool
    {
        return $this->hasPendingWhere;
    }

    /**
     * Get the current query table
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
