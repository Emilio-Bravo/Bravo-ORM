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
        return $this->value->fetchAll(PDO::FETCH_OBJ);
    }
    public function num()
    {
        return $this->value->fetch(PDO::FETCH_NUM);
    }
    public function assoc()
    {
        return $this->value->fetchAll(PDO::FETCH_ASSOC);
    }
    public function count()
    {
        return $this->value->rowCount();
    }
    public function json()
    {
        return json_encode($this->assoc());
    }
    public function deserializable()
    {
        return $this->value->fetch(PDO::FETCH_SERIALIZE);
    }
    public function serializable()
    {
        return serialize($this->assoc());
    }
}
