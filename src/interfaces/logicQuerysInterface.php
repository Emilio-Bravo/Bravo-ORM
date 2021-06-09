<?php

namespace Bravo\ORM;

interface logicQuerys
{
    public function where(array $columns_values, string $operator = "=");
    public function find(array $column_value, string $operator = '=');
}
