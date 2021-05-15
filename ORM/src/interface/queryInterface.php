<?php

namespace Bravo\ORM;

interface QueryInterface
{
    public function table();
    public function selectAll();
    public function insert(array $values);
    public function update();
    public function query($query, array $values = null);
    public function where($condition);
    public function select(array $columns = null);
}
