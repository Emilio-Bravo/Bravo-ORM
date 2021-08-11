<?php

namespace Bravo\ORM;

interface logicQuerys
{
    /**
     * Adds a WHERE statement to the current query
     * 
     * @param array $data
     * @param string $operator The comparison operator to be used
     * @return self
     */
    public function where(array $data, string $operator = '='): self;

    /**
     * Finds one or more rows with the specified values
     * 
     * @param array $data ['name' => 'John', 'email' => 'john@mail.com']
     * @param string $operator The comparison operator to be used
     * @return self
     */
    public function find(array $data, string $operator = '='): self;
}
