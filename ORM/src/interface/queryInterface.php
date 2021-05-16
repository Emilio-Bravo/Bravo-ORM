<?php

namespace Bravo\ORM;

interface QueryInterface
{
    public function insert(array $values);
    public function update(array $column_value);
    public function where($condition);
    public function select(array $columns = null);
}
