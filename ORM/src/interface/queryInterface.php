<?php

namespace Bravo\ORM;

interface QueryInterface
{
    public function table();
    public function selectAll();
    public function insert();
    public function update();
    public function query($query, $values = null);
    public function where($condition);
    public function select();
}
