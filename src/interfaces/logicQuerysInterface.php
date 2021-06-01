<?php

namespace Bravo\ORM;

interface logicQuerys
{
    public function where(array $columns_values, $operator = "=");
    public function find(array $column_value, $operator = '=');
}
