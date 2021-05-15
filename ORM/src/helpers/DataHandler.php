<?php

namespace Bravo\ORM;

use PDO;

class DataHandler
{
    private $value;

    public function __invoke($value)
    {
        $this->value = $value;
        return $this;
    }
    public function __construct($value = null)
    {
        $this->value = $value ?? false ?? $value;
    }
    public function obj()
    {
        return $this->value->fetch(PDO::FETCH_OBJ);
    }
    public function num()
    {
        return $this->value->fetch(PDO::FETCH_NUM);
    }
}
