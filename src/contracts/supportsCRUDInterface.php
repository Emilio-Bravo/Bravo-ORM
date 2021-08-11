<?php

namespace Bravo\ORM;

interface supportsCRUD
{
    /**
     * Performs an INSERT statement into the database
     * 
     * @param array $values Values to insert
     * @return self
     */
    public function insert(array $values): self;

    /**
     * Adds or performs a SELECT statement to the current query
     * 
     * @param array|null $columns If provided the statment will apply to the specified columns
     * @param array|null $tables
     * @return self
     */
    public function select(?array $columns = null, ?array $tables = null): self;

    /**
     * Sets an UPDATE statement
     * 
     * @param array $values Needs to be an associative array in wich selected columns will bind a value Example: ['name' => 'example']
     * @return self
     */
    public function update(array $values): self;

    /**
     * Sets a DELETE statement
     * 
     * @return self
     */
    public function delete(): self;
}
