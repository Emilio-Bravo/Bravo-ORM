<?php

namespace Bravo\ORM;

use Exception;
use Bravo\ORM\ExceptionInterface;

class DataNotFoundException extends Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Data not found');
    }
    public function errorMessage()
    {
        return "DataNotFoundException: {$this->getMessage()}";
    }
}
