<?php

namespace Bravo\ORM;

interface supportsCRUD
{
    public function insert(array $values);
    public function update(array $values);
    public function delete();
}